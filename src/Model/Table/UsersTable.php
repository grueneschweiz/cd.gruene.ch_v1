<?php

namespace App\Model\Table;

use App\Model\Entity\LoginHash;
use App\Model\Entity\User;
use App\Model\Entity\UsersGroup;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property ImagesTable $Images
 * @property UsersGroupsTable $UsersGroups
 * @property LoginHashesTable $LoginHashes
 * @property GroupsTable $Groups
 * @property LoginLogsTable $LoginLogs
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null)
 */
class UsersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     *
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('full_name_email');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');

        $this->hasMany('Images', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('LoginHashes', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('LoginLogs', [
            'foreignKey' => 'email',
            'bindingKey' => 'email',
        ]);
        $this->hasMany('UsersGroups', [
            'className' => 'UsersGroups',
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('ManagingGroups', [
            'foreignKey' => 'managed_by_group_id',
            'className' => 'Groups',
        ]);
        $this->belongsToMany('Groups', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'group_id',
            'joinTable' => 'users_groups'
        ]);
        $this->hasOne('Users', [
            'foreignKey' => 'id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('first_name');

        $validator
            ->allowEmpty('last_name');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->requirePresence('password', 'create')
            ->add('password', 'length', ['rule' => ['lengthBetween', 8, 100]])
            ->notEmpty('password');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     *
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['added_by_user_id'], 'Users'));
        $rules->add($rules->existsIn(['managed_by_group_id'], 'ManagingGroups'));

        return $rules;
    }

    /**
     * Tasks to perform before saving
     *
     * @param Event $event
     * @param EntityInterface $entity
     * @param \ArrayObject $options
     */
    public function beforeSave(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        // delete all login hashes if the password was changed
        if (!$entity->isNew() && $entity->isDirty('password')) {
            $this->LoginHashes->deleteAll(['user_id' => $entity->id]);
        }
    }

    /**
     * Remove all remember me hashes
     *
     * @param int $userId
     */
    public function logoutEverywhere(int $userId)
    {
        $this->LoginHashes->deleteAll(['user_id' => $userId, 'type' => LoginHashesTable::REMEMBER]);
    }

    /**
     * Save entity with group associations while checking all permissions.
     *
     * Modifications on groups the current user has no privileges to will be ignored. A flash message will be triggered.
     *
     * @param User $user the user to save
     * @param array $data the data to save
     * @param int $current_user_id the id of the user that made the changes
     *
     * @return bool
     */
    public function saveEntityIncludingGroups(User $user, array $data, int $current_user_id)
    {
        try {
            // handle everything as a transaction
            $this->getConnection()->transactional(function () use ($user, $data, $current_user_id) {
                // get associations
                $new = empty($data['groups']['_ids']) ? [] : $data['groups']['_ids'];
                $new_admin = empty($data['admin_groups']) ? [] : $data['admin_groups'];
                $new = array_diff($new, $new_admin); // cause we do always have the right to use, if were admin

                // make sure we have integer values
                $new = array_map('intval', $new);
                $new_admin = array_map('intval', $new_admin);

                // and remove them from the user cause we want to treat them manually
                unset($data['groups']);
                unset($data['admin_groups']);

                // save entity first, cause we need its id
                $user = $this->patchEntity($user, $data);
                $this->saveOrFail($user);

                // get old associations
                $old = $this->UsersGroups->find()->select(['group_id'])
                    ->where(['user_id' => $user->id])
                    ->extract('group_id')
                    ->toArray();
                $old_admin = $this->UsersGroups->find()->select(['group_id'])
                    ->where(['user_id' => $user->id, 'admin' => true])
                    ->extract('group_id')
                    ->toArray();

                // get the user that made the changes
                $current_user = $this->get($current_user_id);

                // add groups (only the ones the current user has admin privileges for)
                $notAllowed = [];
                foreach (array_diff($new_admin, $old_admin) as $id) {
                    if ($current_user->canManageGroup($id)) {
                        $group = $this->UsersGroups->find()->where([
                            'user_id' => $user->id,
                            'group_id' => $id
                        ])->first();
                        if (!$group) {
                            $group = $this->UsersGroups->newEntity();
                            $group->group_id = $id;
                            $group->user_id = $user->id;
                            $group->added_by_user_id = $current_user_id;
                        }
                        $group->admin = true;
                        $this->UsersGroups->saveOrFail($group);
                    } else {
                        $notAllowed[] = $id;
                    }
                }
                foreach (array_diff($new, $old) as $id) {
                    if ($current_user->canManageGroup($id)) {
                        $group = $this->UsersGroups->newEntity();
                        $group->group_id = $id;
                        $group->user_id = $user->id;
                        $group->added_by_user_id = $current_user_id;
                        $group->admin = false;
                        $this->UsersGroups->saveOrFail($group);
                    } else {
                        $notAllowed[] = $id;
                    }
                }

                // remove groups
                $notAllowed = [];
                foreach (array_diff($old_admin, $new_admin) as $id) {
                    if ($current_user->canManageGroup($id)) {
                        /** @var UsersGroup $el */
                        $el = $this->UsersGroups->find()->where([
                            'group_id' => $id,
                            'user_id' => $user->id,
                        ])->firstOrFail();
                        $el->admin = false;
                        $this->UsersGroups->saveOrFail($el);
                    } else {
                        $notAllowed[] = $id;
                    }
                }
                foreach (array_diff($old, $new, $new_admin) as $id) {
                    if ($current_user->canManageGroup($id)) {
                        $el = $this->UsersGroups->find()->where([
                            'group_id' => $id,
                            'user_id' => $user->id,
                        ])->firstOrFail();
                        $this->UsersGroups->deleteOrFail($el);
                    } else {
                        $notAllowed[] = $id;
                    }
                }

                // trigger not allowed flash messages
                $notAllowedNames = [];
                foreach ($notAllowed as $id) {
                    $notAllowedNames[] = $this->Groups->get($id)->name;
                }
                if ($notAllowedNames) {
                    $this->Flash->error(
                        __("You don't have the privileges to save the users changes concerning the group(s) {groups}.",
                            ['groups' => implode(', ', $notAllowedNames)]));
                }

                return true; // commit
            });

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Log the users login attempt
     *
     * @param int $userId
     */
    public function updateLoginStats(int $userId)
    {
        $user = $this->get($userId);
        $user->login_count++;
        $user->last_login = Time::now();
        $this->save($user);
    }
}
