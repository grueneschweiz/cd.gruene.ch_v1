<?php /** @noinspection PhpInconsistentReturnPointsInspection */

namespace App\Controller;

use App\Model\Entity\User;
use Cake\ORM\Query;

/**
 * Logos Controller
 *
 * @property \App\Model\Table\LogosTable $Logos
 */
class LogosController extends AppController
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
            $user = $this->Logos->Users->get($user['id']);
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
        if ($user->canManageLogo($route['pass'][0])) {
            return true;
        }

        return false;
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        // get user id
        $userId = $this->Auth->user('id');

        // get logos the user has privilegs to
        $userAdminLogos = $this->Logos->Users->get($userId)->getManageableLogos();

        $this->paginate = [
            'order' => ['name'],
        ];

        // and display them
        $logos = $this->Paginator->paginate($userAdminLogos, $this->paginate);

        $this->set(compact('logos'));
        $this->set('_serialize', ['logos']);
    }

    /**
     * View method
     *
     * @param string|null $id Logo id.
     *
     * @return void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $logo = $this->Logos->get($id, ['contain' => ['Users']]);

        $groups = $this->Logos->Groups->find('list');
        $groups->matching('Logos', function (Query $q) use ($id) {
            return $q->where(['Logos.id' => $id]);
        })->order(['Groups.name']);

        $this->set('logo', $logo);
        $this->set('groups', $groups);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $userId = $this->Auth->user('id');

        $logo = $this->Logos->newEntity();
        if ($this->request->is('post')) {
            $data = array_merge($this->request->getData(), ['added_by_user_id' => $userId]);
            $logo = $this->Logos->patchEntity($logo, $data);
            if ($this->Logos->save($logo)) {
                $this->Flash->success(__('The logo has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The logo could not be saved. Please, try again.'));
            }
        }

        $top_paths = $this->Logos->getTopPaths();
        $this->set(compact('logo', 'top_paths'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Logo id.
     *
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $logo = $this->Logos->get($id, ['contain' => ['Users']]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $logo = $this->Logos->patchEntity($logo, $data);
            if ($this->Logos->save($logo)) {
                $this->Flash->success(__('The logo has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The logo could not be saved. Please, try again.'));
            }
        }

        $top_paths = $this->Logos->getTopPaths();
        $this->set(compact('logo', 'top_paths'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Logo id.
     *
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $logo = $this->Logos->get($id);
        if ($this->Logos->delete($logo)) {
            $this->Flash->success(__('The logo has been deleted.'));
        } else {
            $this->Flash->error(__('The logo could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}