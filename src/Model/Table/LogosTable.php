<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;
use ArrayObject;

/**
 * Logos Model
 *
 * @property GroupsTable $Groups
 * @property UsersTable $Users
 *
 * @method \App\Model\Entity\Logo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Logo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Logo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Logo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Logo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Logo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Logo findOrCreate($search, callable $callback = null)
 */
class LogosTable extends Table
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

        $this->table('logos');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');

        $this->belongsToMany('Groups', [
            'foreignKey' => 'logo_id',
            'targetForeignKey' => 'group_id',
            'joinTable' => 'groups_logos'
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'added_by_user_id',
            'joinType' => 'LEFT',
        ]);

        $this->hasMany('GroupsLogos', [
            'foreignKey' => 'logo_id',
            'joinType' => 'LEFT',
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
            ->allowEmpty('top_path');

        $validator
            ->allowEmpty('subline');

        $validator
            ->allowEmpty('name');

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
        $rules->add($rules->existsIn(['added_by_user_id'], 'Users'));
        $rules->add($rules->isUnique(['name']));

        return $rules;
    }

    /**
     * returns an array with all logo ULRs (not paths)
     *
     * @return array
     */
    public function getTopPaths()
    {
        $pathToLogos = ROOT . DS . 'protected' . DS . 'logos'; // directory of the logos
        $fileFormat = '.svg'; // the dot is mandatory

        $files = scandir($pathToLogos, SCANDIR_SORT_ASCENDING);

        $logos = array();

        foreach ($files as $file) {
            if (strpos($file, $fileFormat)) {
                $logos['/protected/logos/' . $file] = $file;
            }
        }

        return $logos;
    }
}
