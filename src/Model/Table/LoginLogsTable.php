<?php

namespace App\Model\Table;

use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LoginLogs Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\LoginLog get($primaryKey, $options = [])
 * @method \App\Model\Entity\LoginLog newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\LoginLog[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LoginLog|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LoginLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\LoginLog[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\LoginLog findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LoginLogsTable extends Table
{

    /**
     * how many login attempts are allowed from the same ip and the same user
     * during with time period, until the user gets locked out
     */
    const LIMIT_LOGIN_ATTEMPTS_PER_USER = 10;
    const LIMIT_TOTAL_LOGIN_ATTEMPTS = 100;
    const TIME_TO_RESET = '8 hours';

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

        $this->setTable('login_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'email',
            'bindingKey' => 'email',
            'joinType' => 'LEFT'
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
            ->requirePresence('ip', 'create')
            ->notEmpty('ip');

        $validator
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->boolean('successful')
            ->requirePresence('successful', 'create')
            ->notEmpty('successful');

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
        return $rules;
    }

    /**
     * Log the users login attempt
     *
     * @param string $email
     * @param string $ip
     * @param bool $successful true if login was successful
     */
    public function log(string $email, string $ip, bool $successful)
    {
        $entry = $this->newEntity();
        $entry->email = $email;
        $entry->ip = $ip;
        $entry->successful = $successful;

        $this->save($entry);
    }

    /**
     * Check if the user didn't try to log in to often from the given ip
     *
     * @param string $email
     * @param string $ip
     *
     * @return bool
     */
    public function canLogIn(string $email, string $ip): bool
    {
        $time = new \DateTime('-' . self::TIME_TO_RESET);
        $attempts = $this->find()->where([
            'created > ' => $time->format('c'),
            'successful' => false,
            'ip' => $ip
        ]);

        if ($attempts->count() > self::LIMIT_TOTAL_LOGIN_ATTEMPTS) {
            return false;
        }

        return self::LIMIT_LOGIN_ATTEMPTS_PER_USER > $attempts->andWhere(['email' => $email])->count();
    }
}
