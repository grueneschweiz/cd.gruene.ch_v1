<?php

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 *
 */
class UsersFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => false,
            'default' => null,
            'comment' => '',
            'autoIncrement' => true,
            'precision' => null
        ],
        'first_name' => [
            'type' => 'string',
            'length' => 60,
            'null' => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'last_name' => [
            'type' => 'string',
            'length' => 60,
            'null' => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'email' => [
            'type' => 'string',
            'length' => 120,
            'null' => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'password' => [
            'type' => 'string',
            'length' => 255,
            'null' => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'added_by_user_id' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => true,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
        ],
        'super_admin' => [
            'type' => 'boolean',
            'length' => null,
            'null' => true,
            'default' => null,
            'comment' => '',
            'precision' => null
        ],
        'lang' => [
            'type' => 'string',
            'length' => 2,
            'null' => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'managed_by_group_id' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
        ],
        'created' => [
            'type' => 'datetime',
            'length' => null,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null
        ],
        'modified' => [
            'type' => 'datetime',
            'length' => null,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null
        ],
        'deleted' => [
            'type' => 'datetime',
            'length' => null,
            'null' => true,
            'default' => null,
            'comment' => '',
            'precision' => null
        ],
        '_indexes' => [
            'fk_users_groups1_idx' => ['type' => 'index', 'columns' => ['managed_by_group_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_users_groups1' => [
                'type' => 'foreign',
                'columns' => ['managed_by_group_id'],
                'references' => ['groups', 'id'],
                'update' => 'noAction',
                'delete' => 'noAction',
                'length' => []
            ],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'first_name' => 'Cyrill',
            'last_name' => 'Bolliger',
            'email' => 'cyrill.bolliger@gruene.ch',
            'password' => 'laksjdfhladsioufzqlwkejf,<dkjz',
            'added_by_user_id' => 1,
            'super_admin' => 1,
            'lang' => 'de',
            'managed_by_group_id' => 1,
            'created' => '2018-05-06 07:39:39',
            'modified' => '2018-05-06 07:39:39'
        ],
        [
            'id' => 2,
            'first_name' => 'Esther',
            'last_name' => 'Meier',
            'email' => 'esther.meier@gruenebern.ch',
            'password' => 'laksjdfhladsioufzqlwkejf,<dkjz',
            'added_by_user_id' => 1,
            'super_admin' => 0,
            'lang' => 'de',
            'managed_by_group_id' => 1,
            'created' => '2018-05-06 07:39:39',
            'modified' => '2018-05-06 07:39:39'
        ],
        [
            'id' => 3,
            'first_name' => 'Gino',
            'last_name' => 'Brenni',
            'email' => 'gino@gibtsnicht.asdf',
            'password' => 'laksjdfhladsioufzqlwkejf,<dkjz',
            'added_by_user_id' => 2,
            'super_admin' => 0,
            'lang' => 'de',
            'managed_by_group_id' => 2,
            'created' => '2018-05-06 07:39:39',
            'modified' => '2018-05-06 07:39:39'
        ],
        [
            'id' => 4,
            'first_name' => 'Anik',
            'last_name' => 'Thaler',
            'email' => 'anik@gibtsnicht.asdf',
            'password' => 'laksjdfhladsioufzqlwkejf,<dkjz',
            'added_by_user_id' => 3,
            'super_admin' => 0,
            'lang' => 'de',
            'managed_by_group_id' => 3,
            'created' => '2018-05-06 07:39:39',
            'modified' => '2018-05-06 07:39:39'
        ],
        [
            'id' => 5,
            'first_name' => 'Aline',
            'last_name' => 'Trede',
            'email' => 'aline@gibtsnicht.asdf',
            'password' => 'laksjdfhladsioufzqlwkejf,<dkjz',
            'added_by_user_id' => 1,
            'super_admin' => 0,
            'lang' => 'de',
            'managed_by_group_id' => 2,
            'created' => '2018-05-06 07:39:39',
            'modified' => '2018-05-06 07:39:39'
        ],
    ];
}
