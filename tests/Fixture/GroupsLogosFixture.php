<?php

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * GroupsLogosFixture
 *
 */
class GroupsLogosFixture extends TestFixture
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
        'logo_id' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
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
        '_indexes' => [
            'fk_groups_logos_logos_idx' => [
                'type' => 'index',
                'columns' => ['logo_id'],
                'length' => []
            ],
            'fk_groups_logos_groups1_idx' => [
                'type' => 'index',
                'columns' => ['group_id'],
                'length' => []
            ],
        ],
        '_constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => ['id'],
                'length' => []
            ],
            'fk_groups_logos_groups1' => [
                'type' => 'foreign',
                'columns' => ['group_id'],
                'references' => ['groups', 'id'],
                'update' => 'noAction',
                'delete' => 'noAction',
                'length' => []
            ],
            'fk_groups_logos_logos' => [
                'type' => 'foreign',
                'columns' => ['logo_id'],
                'references' => ['logos', 'id'],
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
            'logo_id' => 1,
            'group_id' => 1
        ],
        [
            'id' => 2,
            'logo_id' => 2,
            'group_id' => 2
        ],
        [
            'id' => 3,
            'logo_id' => 3,
            'group_id' => 3
        ],
        [
            'id' => 4,
            'logo_id' => 4,
            'group_id' => 4
        ],
        [
            'id' => 5,
            'logo_id' => 5,
            'group_id' => 5
        ],
        [
            'id' => 6,
            'logo_id' => 2,
            'group_id' => 1
        ],
    ];
}
