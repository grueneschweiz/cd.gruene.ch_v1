<?php

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * GroupsFixture
 *
 */
class GroupsFixture extends TestFixture
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
        'parent_id' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => true,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
        ],
        'lft' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
        ],
        'rght' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
        ],
        'name' => [
            'type' => 'string',
            'length' => 45,
            'null' => true,
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
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
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
            'parent_id' => null,
            'lft' => 1,
            'rght' => 1,
            'name' => 'Grüne Schweiz',
            'added_by_user_id' => 1,
            'created' => '2018-05-05 15:10:34',
            'modified' => '2018-05-05 15:10:34'
        ],
        [
            'id' => 2,
            'parent_id' => 1,
            'lft' => 1,
            'rght' => 1,
            'name' => 'Grüne Kanton Bern',
            'added_by_user_id' => 1,
            'created' => '2018-05-05 15:10:34',
            'modified' => '2018-05-05 15:10:34'
        ],
        [
            'id' => 3,
            'parent_id' => 2,
            'lft' => 1,
            'rght' => 1,
            'name' => 'Grüne Muri',
            'added_by_user_id' => 1,
            'created' => '2018-05-05 15:10:34',
            'modified' => '2018-05-05 15:10:34'
        ],
        [
            'id' => 4,
            'parent_id' => 2,
            'lft' => 1,
            'rght' => 1,
            'name' => 'Grüne Aarberg',
            'added_by_user_id' => 1,
            'created' => '2018-05-05 15:10:34',
            'modified' => '2018-05-05 15:10:34'
        ],
        [
            'id' => 5,
            'parent_id' => 1,
            'lft' => 1,
            'rght' => 1,
            'name' => 'Grüne Aargau',
            'added_by_user_id' => 1,
            'created' => '2018-05-05 15:10:34',
            'modified' => '2018-05-05 15:10:34'
        ],
        [
            'id' => 6,
            'parent_id' => 9999,
            'lft' => 1,
            'rght' => 1,
            'name' => 'Missing Parent Group',
            'added_by_user_id' => 1,
            'created' => '2018-05-05 15:10:34',
            'modified' => '2018-05-05 15:10:34'
        ],
    ];
}
