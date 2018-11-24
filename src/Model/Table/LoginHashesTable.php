<?php

namespace App\Model\Table;

use App\Model\Entity\LoginHash;
use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Security;
use Cake\Validation\Validator;

/**
 * LoginHashes Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\LoginHash get($primaryKey, $options = [])
 * @method \App\Model\Entity\LoginHash newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\LoginHash[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LoginHash|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LoginHash patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\LoginHash[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\LoginHash findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LoginHashesTable extends Table
{

    const REMEMBER = 'remember';
    const RESET_PW = 'reset';
    const REGISTER = 'register';

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

        $this->table('login_hashes');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

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
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->requirePresence('selector', 'create')
            ->notEmpty('selector')
            ->add('selector', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->requirePresence('hash', 'create')
            ->notEmpty('hash');

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
        $rules->add($rules->isUnique(['selector']));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    /**
     * Add a remember hash and return the new entity or false on error
     *
     * @param int $user_id
     *
     * @return LoginHash|false
     */
    public function remember(int $user_id)
    {
        return $this->_newHash($user_id, self::REMEMBER);
    }

    /**
     * Add a hash of the given type and return the new entity or false on error
     *
     * @param int $user_id
     *
     * @return LoginHash|false
     */
    private function _newHash(int $user_id, string $type)
    {
        $hash = $this->newEntity();
        $hash->user_id = $user_id;
        $hash->type = $type;
        $hash->selector = md5($user_id . microtime());
        $hash->token = Security::hash(Security::randomBytes(64));

        if ($this->save($hash)) {
            return $hash;
        } else {
            return false;
        }
    }

    /**
     * Lookup the given selector token combination
     * and return the corresponding user_id if found and hash has not expired.
     *
     * @param string $selector
     * @param string $token
     *
     * @return bool|int user_id if selector token combination is valid else false
     */
    public function authenticate(string $selector, string $token)
    {
        $hash = $this->find()->where([
            'selector' => $selector,
            'token' => $token,
        ])->first();

        if (!$hash) {
            // selector token pair not valid
            return false;
        }

        /** @var LoginHash $hash */
        switch ($hash->type) {
            case self::REMEMBER:
                $valid_time = Configure::read('Login.rememberme_expiration');
                break;
            case self::RESET_PW:
                $valid_time = Configure::read('Login.password_reset_link_expiration');
                break;
            case self::REGISTER:
                $valid_time = Configure::read('Login.registration_link_expiration');
                break;
            default:
                $valid_time = 0; // expire immediately
        }

        // return false if expired
        return $hash->created->wasWithinLast($valid_time) ? $hash->user_id : false;
    }

    /**
     * Delete all entities that match the given selector token combination
     *
     * @param string $selector
     * @param string $token
     */
    public function remove(string $selector, string $token)
    {
        $this->deleteAll([
            'selector' => $selector,
            'token' => $token,
        ]);
    }

    /**
     * Add a password reset hash and return the new entity or false on error
     *
     * @param int $user_id
     *
     * @return LoginHash|false
     */
    public function resetPassword(int $user_id)
    {
        return $this->_newHash($user_id, self::RESET_PW);
    }

    /**
     * Add a registration hash and return the new entity or false on error
     *
     * @param int $user_id
     *
     * @return LoginHash|false
     */
    public function register(int $user_id)
    {
        return $this->_newHash($user_id, self::REGISTER);
    }
}
