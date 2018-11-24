<?php /** @noinspection PhpInconsistentReturnPointsInspection */

namespace App\Controller;

use App\Model\Entity\User;

/**
 * Groups Controller
 *
 * @property \App\Model\Table\GroupsTable $Groups
 */
class GroupsController extends AppController
{

    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Paginator');
    }

    /**
     * Grant access to any user with some admin rights to index and add method,
     * check privileges for entity to access other methods
     *
     * @param User|array $user
     *
     * @return boolean
     */
    public function isAuthorized($user)
    {
        if (is_array($user)) {
            $user = $this->Groups->Users->get($user['id']);
        }

        if (!$user->isAdmin()) {
            return false;
        }

        $route = $this->request->getAttributes()['params'];

        // allow 'index' and 'add' action for any admins
        if (in_array($route['action'], ['index', 'add'])) {
            return true;
        }

        // allow accessing a group the user is admin of
        if ($user->canManageGroup($route['pass'][0])) {
            return true;
        }

        return false;
    }

    /**
     * Show list of all groups
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['ParentGroups']
        ];

        // get user id
        $userId = $this->Auth->user('id');

        // get groups the user has admin privileges to
        $allUsersAdminGroups = $this->Groups->Users->get($userId)
            ->getManageableGroups()
            ->find('treeList', [
                'spacer' => '&mdash; '
            ]);

        $this->paginate = [
            'limit' => 100,
        ];

        // and display them
        $groups = $this->Paginator->paginate($allUsersAdminGroups, $this->paginate);

        $this->set(compact('groups'));
        $this->set('_serialize', ['groups']);
    }

    /**
     * Show single group
     *
     * @param string|null $id Group id.
     *
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {

        $group = $this->Groups->get($id, [
            'contain' => [
                'Logos',
                'Users',
                'Users.Groups'
            ]
        ]);

        $this->set('group', $group);
        $this->set('_serialize', ['group']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $userId = $this->Auth->user('id');

        $group = $this->Groups->newEntity();
        if ($this->request->is('post')) {
            $data = array_merge($this->request->getData(), array('added_by_user_id' => $this->Auth->user('id')));
            $group = $this->Groups->patchEntity($group, $data);
            if ($this->Groups->save($group)) {
                $this->Flash->success(__('The group has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The group could not be saved. Please, try again.'));
            }
        }

        $currentUser = $this->Groups->Users->get($userId);

        $parentGroups = $currentUser->getManageableGroups()
            ->find('treeList');
        $logos = $currentUser->getManageableLogos()
            ->find('list')->order(['subline' => 'ASC']);
        $users = $currentUser->getManageableUsers()
            ->find('list')->order(['first_name' => 'ASC', 'last_name' => 'ASC']);
        $this->set(compact('group', 'parentGroups', 'logos', 'users'));
        $this->set('_serialize', ['group']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Group id.
     *
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $userId = $this->Auth->user('id');

        $group = $this->Groups->get($id, [
            'contain' => ['Logos', 'Users']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $group = $this->Groups->patchEntity($group, $data);
            if ($this->Groups->save($group)) {
                $this->Flash->success(__('The group has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The group could not be saved. Please, try again.'));
            }
        }

        $currentUser = $this->Groups->Users->get($userId);

        $parentGroups = $currentUser->getManageableGroups()
            ->find('treeList');
        $logos = $currentUser->getManageableLogos()
            ->find('list')->order(['subline' => 'ASC']);
        $users = $currentUser->getManageableUsers()
            ->find('list')->order(['first_name' => 'ASC', 'last_name' => 'ASC']);
        $this->set(compact('group', 'parentGroups', 'logos', 'users'));
        $this->set('_serialize', ['group']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Group id.
     *
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $group = $this->Groups->get($id);
        if ($this->Groups->delete($group)) {
            $this->Flash->success(__('The group has been deleted.'));
        } else {
            $this->Flash->error(__('The group could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
