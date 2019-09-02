<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use App\Model\Entity\User;
use App\Model\Table\LoginHashesTable;
use App\Model\Table\UsersTable;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see http://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        $this->loadComponent('Security');
        $this->loadComponent('Csrf');
        $this->loadComponent('Cookie');

        /**
         * Login stuff
         */
        $this->loadComponent('Auth', [
            'authorize' => 'Controller',
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password'
                    ]
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'loginRedirect' => '/',
            'unauthorizedRedirect' => Router::url(['controller' => 'Users', 'action' => 'accessDenied'], true),
            'authError' => false,
        ]);
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // autologin from remember me cookie
        if (empty($this->Auth->user('id'))) {
            $this->_rememberLogin();
        }

        // call this again, because it may have changed in the _rememberLogin method
        if (!empty($this->Auth->user('id'))) {
            // redirect user to index action, if he accesses the login page but is already logged in
            if ($this->request->getParam('controller') === 'Users' && $this->request->getParam('action') === 'login') {
                return $this->redirect('/');
            }
        }

        $this->setLang();
    }

    /**
     * Try to log in user using the rememberme cookie
     *
     * @return bool true for successful login
     */
    private function _rememberLogin()
    {
        /** @var string|null|array $cookie */
        $cookie = $this->Cookie->read('rememberme');
        if (empty($cookie) || empty($cookie['selector']) || empty($cookie['token'])) {
            return false;
        }

        /** @var LoginHashesTable $LoginHashes */
        $LoginHashes = TableRegistry::getTableLocator()->get('LoginHashes');
        $user_id = $LoginHashes->authenticate($cookie['selector'], $cookie['token']);

        if (!$user_id) {
            return false;
        }

        /** @var UsersTable $Users */
        $Users = TableRegistry::getTableLocator()->get('Users');
        $user = $Users->find()->where(['id' => $user_id])->first();

        if (!$user) {
            return false;
        }
        /** @var User $user */

        $Users->updateLoginStats($user['id']);
        $Users->LoginLogs->log($user->email, $this->request->clientIp(), true);

        $this->Auth->setUser($user);

        return (bool)$this->Auth->user('id');
    }

    /**
     * Set language according to the users setting or the browser if the user is not logged in
     */
    public function setLang()
    {
        if ($this->Auth && $this->Auth->user('lang')) {
            $lang = $this->Auth->user('lang');
        } else {
            $lang_string = $this->request->getEnv('HTTP_ACCEPT_LANGUAGE');
            $lang = substr($lang_string, 0, 2);
        }

        if (empty($lang)) {
            $lang = 'en';
        }

        I18n::setLocale($lang . '_CH');
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     *
     * @return \Cake\Network\Response|null|void
     */
    public function beforeRender(Event $event)
    {
        if (!$this->viewBuilder()->getVar('_serialize') &&
            in_array($this->response->getType(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }

        // set $admin as global var for all views
        if ($this->Auth && $this->Auth->user('id')) {
            $Users = TableRegistry::getTableLocator()->get('Users');
            /** @var User $user */
            $user = $Users->get($this->Auth->user('id'));
            $admin = $user->isAdmin();
        } else {
            $admin = false;
        }

        $this->set('admin', $admin);
    }

    /**
     * Deny all access by default
     *
     * @param User|array $user
     *
     * @return boolean
     */
    public function isAuthorized($user)
    {
        return false;
    }
}
