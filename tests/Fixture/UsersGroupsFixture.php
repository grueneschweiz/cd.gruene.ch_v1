<?php

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersGroupsFixture
 *
 */
class UsersGroupsFixture extends TestFixture
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
        'group_id' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
        ],
        'user_id' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
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
        'admin' => [
            'type' => 'boolean',
            'length' => null,
            'null' => true,
            'default' => null,
            'comment' => '',
            'precision' => null
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
            'fk_users_groups_groups1_idx' => ['type' => 'index', 'columns' => ['group_id'], 'length' => []],
            'fk_users_groups_users1_idx' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_users_groups_groups1' => [
                'type' => 'foreign',
                'columns' => ['group_id'],
                'references' => ['groups', 'id'],
                'update' => 'noAction',
                'delete' => 'noAction',
                'length' => []
            ],
            'fk_users_groups_users1' => [
                'type' => 'foreign',
                'columns' => ['user_id'],
                'references' => ['users', 'id'],
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
            'group_id' => 1,
            'user_id' => 1,
            'added_by_user_id' => 1,
            'admin' => 1,
            'created' => '2018-05-06 07:45:51',
            'modified' => '2018-05-06 07:45:51'
        ],
        [
            'id' => 2,
            'group_id' => 2,
            'user_id' => 2,
            'added_by_user_id' => 1,
            'admin' => 1,
            'created' => '2018-05-06 07:45:51',
            'modified' => '2018-05-06 07:45:51'
        ],
        [
            'id' => 3,
            'group_id' => 3,
            'user_id' => 3,
            'added_by_user_id' => 2,
            'admin' => 1,
            'created' => '2018-05-06 07:45:51',
            'modified' => '2018-05-06 07:45:51'
        ],
        [
            'id' => 4,
            'group_id' => 3,
            'user_id' => 4,
            'added_by_user_id' => 3,
            'admin' => 0,
            'created' => '2018-05-06 07:45:51',
            'modified' => '2018-05-06 07:45:51'
        ],
        [
            'id' => 5,
            'group_id' => 3,
            'user_id' => 2,
            'added_by_user_id' => 2,
            'admin' => 0,
            'created' => '2018-05-06 07:45:51',
            'modified' => '2018-05-06 07:45:51'
        ],
        [
            'id' => 6,
            'group_id' => 1,
            'user_id' => 5,
            'added_by_user_id' => 1,
            'admin' => 0,
            'created' => '2018-05-06 07:45:51',
            'modified' => '2018-05-06 07:45:51'
        ],
        [
            'id' => 7,
            'group_id' => 2,
            'user_id' => 5,
            'added_by_user_id' => 1,
            'admin' => 0,
            'created' => '2018-05-06 07:45:51',
            'modified' => '2018-05-06 07:45:51'
        ],
        [
            'id' => 8,
            'group_id' => 3,
            'user_id' => 5,
            'added_by_user_id' => 1,
            'admin' => 0,
            'created' => '2018-05-06 07:45:51',
            'modified' => '2018-05-06 07:45:51'
        ],
        [
            'id' => 9,
            'group_id' => 4,
            'user_id' => 3,
            'added_by_user_id' => 2,
            'admin' => 0,
            'created' => '2018-05-06 07:45:51',
            'modified' => '2018-05-06 07:45:51'
        ],
    ];
}
