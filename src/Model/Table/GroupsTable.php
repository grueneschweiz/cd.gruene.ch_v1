<?php

namespace App\Model\Table;

use App\Model\Entity\Group;
use App\Model\Entity\User;
use Cake\Collection\Iterator\MapReduce;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\ORM\Behavior\TreeBehavior;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;
use ArrayObject;


/**
 * Groups Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ParentGroups
 * @property \Cake\ORM\Association\HasMany $ChildGroups
 * @property LogosTable $Logos
 * @property UsersTable $Users
 *
 * @method \App\Model\Entity\Group get($primaryKey, $options = [])
 * @method \App\Model\Entity\Group newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Group[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Group|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Group patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Group[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Group findOrCreate($search, callable $callback = null)
 *
 * @mixin TimestampBehavior
 * @mixin TreeBehavior
 */
class GroupsTable extends Table
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

        $this->setTable('groups');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree', [
            'recoverOrder' => ['name' => 'ASC'],
        ]);
        $this->addBehavior('Muffin/Trash.Trash');

        $this->belongsTo('ParentGroups', [
            'className' => 'Groups',
            'foreignKey' => 'parent_id'
        ]);
        $this->hasMany('UsersGroups', [
            'className' => 'UsersGroups',
            'foreignKey' => 'group_id'
        ]);
        $this->hasMany('Users', [
            'className' => 'Users',
            'foreignKey' => 'managed_by_group_id'
        ]);
        $this->belongsToMany('Logos', [
            'foreignKey' => 'group_id',
            'targetForeignKey' => 'logo_id',
            'joinTable' => 'groups_logos'
        ]);
        $this->belongsToMany('Users', [
            'foreignKey' => 'group_id',
            'targetForeignKey' => 'user_id',
            'joinTable' => 'users_groups'
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
            ->allowEmpty('parent_id')
            ->integer('parent_id');

        $validator
            ->notEmpty('name');

        $validator
            ->notEmpty('added_by_user_id');

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
        $rules->add($rules->isUnique(['name'], __('A group with this name already exists')));
        $rules->add($rules->existsIn(['parent_id'], 'ParentGroups'));
        $rules->add($rules->existsIn(['added_by_user_id'], 'Users'));

        $rules->add(function ($entity, $options) {
            return $entity->parent_id !== $entity->id;
        }, 'selfReferencing', [
            'errorField' => 'parent_id',
            'message' => __('Except for God, one can not be his own parent ;)')
        ]);

        return $rules;
    }

    /**
     * On inserting data, before validating
     *
     * @param Event $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        // sanitize name
        $data['name'] = preg_replace('/^[\s-]*/', '', $data['name']);
    }

    /**
     * Make sure to recover the groups tree after every save, to be sure the order is correct
     *
     * @param Event $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     */
    public function afterSaveCommit(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $this->recover();
    }

    /**
     * Make sure to recover the groups tree after every delete, to be sure the order is correct
     *
     * @param Event $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     */
    public function afterDeleteCommit(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $this->recover();
    }

    /**
     * For all groups the given query will find, add the descendants if the given user
     * has admin privileges for the parent. Return a query that results in a flat array
     * ordered by hierarchy and name.
     *
     * @param Query $query
     * @param User $user
     *
     * @return Query
     */
    public function findIncludingDescendantsIfAdmin(Query $query, User $user)
    {
        $groups = [];

        /** @var Group $group */
        foreach ($query as $group) {
            $groups[$group->id] = $group;

            if ($user->canManageGroup($group) || $user->isSuperAdmin()) {
                /** @var Group $group */
                foreach ($group->getDescendants() as $descendant) {
                    $groups[$descendant->id] = $descendant;
                }
            }
        }

        // fetch groups again to have a pageable object
        $g = [];
        foreach ($groups as $group) {
            $g[] = $group->id;
        }

        $group_id_list = empty($g) ? '0' : implode(',', $g);

        return $this->find()
            ->where("id IN ($group_id_list)")
            ->order("FIELD(Groups.id,$group_id_list)");
    }

}
