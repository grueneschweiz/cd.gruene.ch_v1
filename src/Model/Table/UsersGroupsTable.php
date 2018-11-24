<?php

namespace App\Model\Table;

use App\Model\Entity\Group;
use App\Model\Entity\User;
use App\Model\Entity\UsersGroup;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UsersGroups Model
 *
 * @property GroupsTable $Groups
 * @property UsersTable $Users
 * @property UsersTable $AddedByUsers
 *
 * @method \App\Model\Entity\UsersGroup get($primaryKey, $options = [])
 * @method \App\Model\Entity\UsersGroup newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UsersGroup[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UsersGroup|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UsersGroup patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UsersGroup[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UsersGroup findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersGroupsTable extends Table
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

        $this->table('users_groups');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');

        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
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
            ->boolean('active')
            ->allowEmpty('active');

        $validator
            ->boolean('admin')
            ->allowEmpty('admin');

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
        $rules->add($rules->existsIn(['group_id'], 'Groups'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['added_by_user_id'], 'Users'));
        $rules->add($rules->isUnique(['group_id', 'user_id'], __('This user already belongs to this group.')));

        return $rules;
    }
}
