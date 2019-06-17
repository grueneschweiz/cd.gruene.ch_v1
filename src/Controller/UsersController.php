<?php /** @noinspection PhpInconsistentReturnPointsInspection */

namespace App\Controller;

use App\Model\Entity\LoginHash;
use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\Routing\Router;
use Cake\Utility\Security;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    /**
     * Grant access to any user with some admin rights and
     * to the users edit method of himself.
     *
     * @param User|array $user
     *
     * @return boolean
     */
    public function isAuthorized($user)
    {
        if (is_array($user)) {
            $user = $this->Users->get($user['id']);
        }

        $route = $this->request->getAttributes()['params'];

        if ('setMyNewPassword' === $route['action']) {
            return true;
        }

        // allow 'index' and 'add' action for any admins
        if ($user->isAdmin() && in_array($route['action'], ['index', 'add'])) {
            return true;
        }

        // allow accessing a group the user is admin of
        if (in_array($route['action'], ['edit', 'delete', 'view']) && $user->canManageUser($route['pass'][0])) {
            return true;
        }

        return false;
    }

    /**
     * Initialize
     */
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['logout', 'accessDenied', 'forgotPassword', 'register', 'resetPassword']);
    }

    /**
     * Do first
     *
     * @param Event $event
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        // disable automatic form security for the following actions (required for ajax)
        $this->Security->setConfig('unlockedActions', ['forgotPassword']);
    }

    /**
     * Before render
     *
     * @param Event $event
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        // make the view variable super_admin accessible for all user views
        $this->set('super_admin', (bool)$this->Auth->user('super_admin'));
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['ManagingGroups'],
            'limit' => 100,
            'order' => ['first_name', 'last_name'],
        ];

        $userId = $this->Auth->user('id');
        $users = $this->paginate($this->Users->get($userId)->getManageableUsers());

        $this->set(['users' => $users]);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     *
     * @return void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['UsersGroups', 'Groups', 'Images']
        ]);
        $image_count = count($user->images);

        $this->set('user', $user);
        $this->set('image_count', $image_count);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $userId = $this->Auth->user('id');

        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $data = array_merge($this->request->getData(), ['added_by_user_id' => $userId]);

            if (empty($data['password'])) {
                // set new random password
                $data['password'] = Security::hash(Security::randomBytes(64));
            }

            if ($this->Users->saveEntityIncludingGroups($user, $data, $userId)) {
                $this->Flash->success(__('The user has been saved.'));

                // notify user
                if ($data['notify']) {
                    $this->sendUserNotification($user);
                }

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }

        // get groups the currently logged in user has admin privileges to
        $removableLogoGroups = $groups = $this->Users->get($userId)->getManageableGroups()->find('treeList');

        $nonRemovableGroups = $nonRemovableAdminGroups = null;
        $adminGroups = $inheritedLogoGroups = $inheritedAdminGroups = null;

        $this->set(compact(
            'user',
            'groups',
            'adminGroups',
            'inheritedLogoGroups',
            'removableLogoGroups',
            'inheritedAdminGroups',
            'nonRemovableGroups',
            'nonRemovableAdminGroups'
        ));
    }

    /**
     * Notify the user about his account and invite him to set a password
     *
     * @param User $user
     *
     * @return \Cake\Http\Response|null
     */
    private function sendUserNotification(User $user) {
        $hash = $this->Users->LoginHashes->register($user->id);
        if (!$hash) {
            $this->Flash->error(__('The user could not be informed about his new login. Please, do it manually.'));

            return $this->redirect(['action' => 'index']);
        }

        $subject = __('cd.gruene.ch: Account created');
        $body = __("Salut {{first_name}},\n\nWe're very happy to inform you about your new login on cd.gruene.ch. Now it's time to set your password.\n\nDefine a password: {{password_reset_link}}\n\nOnce you've set the password, use it in combination with this email address to log in:\n\n Email: {{email}}\n\nSincerely,\nThe cd.gruene.ch registration service.");
        $password_reset_link = Router::url([
            '_full' => true,
            'controller' => 'Users',
            'action' => 'reset-password',
            $hash->selector,
            $hash->token
        ]);
        $replacements = [
            '{{first_name}}' => $user->first_name,
            '{{email}}' => $user->email,
            '{{password_reset_link}}' => $password_reset_link,
        ];

        $body = str_replace(array_keys($replacements), $replacements, $body);
        $email = new Email('default');
        $email->setTo($user->email)
              ->setReplyTo($this->Auth->user('email'))
              ->setSubject($subject)
              ->send($body);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     *
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $userId = $this->Auth->user('id');

        // the user we edit
        $user = $this->Users->get($id, [
            'contain' => ['Groups', 'UsersGroups']
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            if (empty($data['password'])) {
                unset($data['password']);
            }

            if ($this->Users->saveEntityIncludingGroups($user, $data, $userId)) {
                $this->Flash->success(__('The user has been saved.'));

                // notify edited user if editor chose so
                if ($data['notify']) {
                    $this->sendUserNotification($user);
                }

                // if i edited myself
                if ($userId === $user->id) {
                    // update the auth
                    $this->Auth->setUser($user);
                    // manually set i18n in case it was changed
                    $this->setLang();
                } else {
                    // only redirect if you didn't edit yourself
                    // cause you might not have access to the users index action
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }

        // the user that is editing
        $currentUser = $this->Users->get($userId);

        // all groups the currently logged in user can manage
        $groups = $currentUser->getManageableGroups()->find('treeList');

        // all group ids the user we edit can manage (without descendants)
        $adminGroupsList = $user->getManageableGroups(false)->find('list')->toArray();
        $adminGroups = array_keys($adminGroupsList);

        $manageableGroups = $currentUser->getManageableGroups()->find('list')->toArray();

        // get the groups of the user where the currently logged in user has no admin privileges to
        $nonRemovableGroups = array_diff_key(
            $user->getGroups()->find('list')->toArray(),
            $manageableGroups
        );

        // get the admin groups of the user where the currently logged in user has no admin privileges to
        $nonRemovableAdminGroups = array_diff_key(
            $user->getManageableGroups()->find('list')->toArray(),
            $manageableGroups
        );

        // get the groups that are inherited from the manageable groups
        $inheritedAdminGroups = array_diff_key(
            $user->getManageableGroups()->find('list')->toArray(),
            $adminGroupsList
        );

        // get groups the logo is inherited because the user can manage those groups
        $inheritedLogoGroups = $user->getManageableGroups()->find('list')->toArray();

        // get groups that are not manageable by our edited user, but he can still use the logo of
        $removableLogoGroups = array_diff_key(
            $groups->toArray(),
            $inheritedLogoGroups,
            $nonRemovableGroups
        );

        $this->set(compact(
            'user',
            'groups',
            'adminGroups',
            'inheritedLogoGroups',
            'removableLogoGroups',
            'inheritedAdminGroups',
            'nonRemovableGroups',
            'nonRemovableAdminGroups'
        ));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     *
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Login method
     *
     * @return \Cake\Network\Response|void
     */
    public function login()
    {
        if ($this->request->is('post')) {
            $email = $this->request->getData('email');

            $user = $this->Auth->identify();

            $can_log_in = $this->Users->LoginLogs->canLogIn($email, $this->request->clientIp());

            if ($user && $can_log_in) {
                $this->Users->updateLoginStats($user['id']);
                $this->Users->LoginLogs->log($email, $this->request->clientIp(), true);

                $this->Auth->setUser($user);

                // set remember me cookie
                $hash = false;
                if ($this->request->getData('rememberme')) {
                    $hash = $this->Users->LoginHashes->remember($user['id']);
                }
                if ($hash) {
                    $this->Cookie->configKey('rememberme', [
                        'expires' => Configure::read('Login.rememberme_expiration'),
                    ]);
                    $this->Cookie->write('rememberme', [
                        'selector' => $hash->selector,
                        'token' => $hash->token,
                    ]);
                }

                return $this->redirect($this->Auth->redirectUrl());
            }

            $this->Users->LoginLogs->log($email, $this->request->clientIp(), false);

            if ($can_log_in) {
                $message = __('Your username or password is incorrect.')
                    . ' <a href="#" class="forgot-password-link alert-link">'
                    . __('Click here to reset your password.') . '</a>';
            } else {
                $message = __('Too many invalid login attempts. Please try again tomorrow.');
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $this->Flash->inline_error($message, ['escape' => false]);
        }

        $this->render('/Users/login', '/clean');
    }

    /**
     * Logout method
     *
     * @return \Cake\Network\Response | null
     */
    public function logout()
    {
        /** @var null|array $cookie */
        $cookie = $this->Cookie->read('rememberme');
        if (!empty($cookie) && !empty($cookie['selector']) && !empty($cookie['token'])) {
            $this->Users->LoginHashes->remove($cookie['selector'], $cookie['token']);
        }
        $this->Cookie->delete('rememberme');

        /** @noinspection PhpUndefinedMethodInspection */
        $this->Flash->inline_success(__('You are now logged out.'));

        return $this->redirect($this->Auth->logout());
    }

    /**
     * Show access denied page
     */
    public function accessDenied()
    {
        $this->Flash->error(__('Access denied'));

        $this->redirect($this->referer());
    }

    /**
     * Generate a new password reset token and send it to the user by email.
     * This method must be called by a post ajax request.
     */
    public function forgotPassword()
    {
        if (!$this->request->is('post') || !$this->request->is('ajax')) {
            return $this->response->withStatus(400);
        }

        $email = $this->request->getData('email');
        $user = null;

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user = $this->Users->find()->where(['email' => $email])->first();
        }

        if ($user) {
            // set new reset hash
            $hash = $this->Users->LoginHashes->resetPassword($user->id);

            if (!$hash) {
                return false;
            }

            $this->_sendPasswordResetLink($user, $hash);
        }

        $return = $user ? true : false;

        $json = json_encode($return);
        $this->set(['content' => $json]);
        $this->render('/Element/ajaxreturn');
    }

    /**
     * Send the user a link to reset his password
     *
     * @param User|EntityInterface $user
     * @param LoginHash $hash
     */
    private function _sendPasswordResetLink(User $user, LoginHash $hash)
    {
        $subject = __('Password reset cd.gruene.ch');
        $body = __("Salut {{first_name}}\n\nYou've requested to reset the password for your account on cd.gruene.ch. Click the following link, to set a new password:\n\nSet new password: {{password_reset_link}}\n\nEnjoy,\nThe Greens of Switzerland");
        $password_reset_link = Router::url([
            '_full' => true,
            'controller' => 'Users',
            'action' => 'reset-password',
            $hash->selector,
            $hash->token
        ]);
        $replacements = [
            '{{first_name}}' => $user->first_name,
            '{{password_reset_link}}' => $password_reset_link,
        ];

        $body = str_replace(array_keys($replacements), $replacements, $body);

        $email = new Email('default');
        $email->setTo($user->email)
            ->setSubject($subject)
            ->send($body);
    }

    /**
     * Display registration form ans send registration data to the contact defined in the app.php
     * once the user has filled it out.
     */
    public function register()
    {
        if ($this->request->is('post') && !empty($this->request->getData('email'))) {
            $subject = __('New user');
            $body = __("Hello Admin\n\n{{first_name}} {{last_name}} ({{email}}) from {{city}} just applied for a login to cd.gruene.ch.\n\nCreate account: {{approve_url}}\n\nSincerely,\nThe cd.gruene.ch registration service.");
            $approve_url = Router::url([
                '_full' => true,
                'controller' => 'Users',
                'action' => 'add',
            ]);
            $replacements = [
                '{{first_name}}' => $this->request->getData('first_name'),
                '{{last_name}}' => $this->request->getData('last_name'),
                '{{email}}' => $this->request->getData('email'),
                '{{city}}' => $this->request->getData('city'),
                '{{approve_url}}' => $approve_url,
            ];

            $body = str_replace(array_keys($replacements), $replacements, $body);
            $email = new Email('default');
            $email->setTo(Configure::read('Contact.user_creation'))
                ->setReplyTo($this->request->getData('email'))
                ->setSubject($subject)
                ->send($body);

            /** @noinspection PhpUndefinedMethodInspection */
            $this->Flash->inline_success(__('Your application was successfully sent to the Greens Switzerland.'));
            $this->render('/Users/register-ok', '/clean');
        } else {
            $this->render('/Users/register', '/clean');
        }
    }

    /**
     * Remove permanent login for user with given id on every device
     *
     * @param null $id
     *
     * @return \Cake\Http\Response|null
     */
    public function logoutEverywhere($id = null)
    {
        if (null === $id || !$this->Users->get($this->Auth->user('id'))->canManageUser($id)) {
            return $this->redirect(['action' => 'accessDenied']);
        }

        $this->Users->logoutEverywhere($id);
        $this->Flash->success(__('Permanent login revoked for all devices.'));

        return $this->redirect($this->referer());
    }

    /**
     * Check if we've received a valid selector token pair and redirect user according to it:
     * - valid: log user in and redirect him to the set-my-new-password function
     * - invalid: send him to the login page and inform him with a flash message
     *
     * @param string $selector
     * @param string $token
     *
     * @return \Cake\Http\Response|null
     */
    public function resetPassword(string $selector, string $token)
    {
        $user_id = $this->Users->LoginHashes->authenticate($selector, $token);

        $user = null;
        if ($user_id) {
            $user = $this->Users->find()->where(['id' => $user_id])->first();
        }

        if (!$user_id || !$user) {
            $expiration = \DateInterval::createFromDateString(Configure::read('Login.password_reset_link_expiration'));
            /** @noinspection PhpUndefinedMethodInspection */
            $this->Flash->inline_error(
                __('Your password reset link has expired.')
                . ' <a href="#" class="forgot-password-link alert-link">'
                . __('Click here to get a new password reset link.')
                . '</a> '
                . __('Please make sure to reset your password within the next {number} hours.',
                    ['number' => $expiration->h]),
                ['escape' => false]
            );

            return $this->redirect(['action' => 'login']);
        }

        $this->Auth->setUser($user);
        $this->redirect(['action' => 'set-my-new-password']);
    }

    /**
     * Set new password for the current user
     *
     * @return \Cake\Http\Response|null
     */
    public function setMyNewPassword()
    {
        $user = $this->Users->get($this->Auth->user('id'));

        if ($this->request->is(['patch', 'post', 'put'])) {
            $password = $this->request->getData('password');
            $user->password = $password; // immediately hashed, so we can't check if it's empty any more

            if (!empty($password) && $this->Users->save($user)) {

                $this->Flash->success(__('The new password was saved.'));

                return $this->redirect('/');
            } else {
                /** @noinspection PhpUndefinedMethodInspection */
                $this->Flash->inline_error(__('The password could not be saved. Is it secure enough?'));
            }
        }

        $this->set('user', $user);
        $this->render('/Users/set_my_new_password', '/clean');
    }
}
