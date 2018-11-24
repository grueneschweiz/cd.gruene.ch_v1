<?php

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * LogosFixture
 *
 */
class LogosFixture extends TestFixture
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
        'top_path' => [
            'type' => 'string',
            'length' => 255,
            'null' => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'subline' => [
            'type' => 'string',
            'length' => 255,
            'null' => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'name' => [
            'type' => 'string',
            'length' => 255,
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
            'top_path' => 'gruene.ch',
            'subline' => 'gruene.ch',
            'name' => 'Grüne',
            'added_by_user_id' => 1,
            'created' => '2018-05-06 14:06:27',
            'modified' => '2018-05-06 14:06:27'
        ],
        [
            'id' => 2,
            'top_path' => 'gruene.ch',
            'subline' => 'gruenebern.ch',
            'name' => 'Grüne Kanton Bern',
            'added_by_user_id' => 1,
            'created' => '2018-05-06 14:06:27',
            'modified' => '2018-05-06 14:06:27'
        ],
        [
            'id' => 3,
            'top_path' => 'gruene.ch',
            'subline' => 'gruenemuri.ch',
            'name' => 'Grüne Muri',
            'added_by_user_id' => 1,
            'created' => '2018-05-06 14:06:27',
            'modified' => '2018-05-06 14:06:27'
        ],
        [
            'id' => 4,
            'top_path' => 'gruene.ch',
            'subline' => 'grueneaarberg.ch',
            'name' => 'Grüne Aarberg',
            'added_by_user_id' => 1,
            'created' => '2018-05-06 14:06:27',
            'modified' => '2018-05-06 14:06:27'
        ],
        [
            'id' => 5,
            'top_path' => 'gruene.ch',
            'subline' => 'grueneaargau.ch',
            'name' => 'Grüne Aargau',
            'added_by_user_id' => 1,
            'created' => '2018-05-06 14:06:27',
            'modified' => '2018-05-06 14:06:27'
        ],
        [
            'id' => 6,
            'top_path' => 'gruene.ch',
            'subline' => 'gruene.ch',
            'name' => 'Lost Logo',
            'added_by_user_id' => 1,
            'created' => '2018-05-06 14:06:27',
            'modified' => '2018-05-06 14:06:27'
        ],

    ];
}
