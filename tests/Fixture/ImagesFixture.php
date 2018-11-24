<?php

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ImagesFixture
 *
 */
class ImagesFixture extends TestFixture
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
        'opath' => [
            'type' => 'string',
            'length' => 255,
            'null' => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'npath' => [
            'type' => 'string',
            'length' => 255,
            'null' => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'width' => [
            'type' => 'float',
            'length' => null,
            'precision' => null,
            'unsigned' => false,
            'null' => true,
            'default' => null,
            'comment' => ''
        ],
        'height' => [
            'type' => 'float',
            'length' => null,
            'precision' => null,
            'unsigned' => false,
            'null' => true,
            'default' => null,
            'comment' => ''
        ],
        'flattext' => [
            'type' => 'string',
            'length' => 255,
            'null' => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'tags' => [
            'type' => 'string',
            'length' => 255,
            'null' => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'created' => [
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
            'fk_images_users1_idx' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_images_users1' => [
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
            'user_id' => 1,
            'opath' => 'Lorem ipsum dolor sit amet',
            'npath' => 'Lorem ipsum dolor sit amet',
            'width' => 1,
            'height' => 1,
            'flattext' => 'Lorem ipsum dolor sit amet',
            'tags' => 'Lorem ipsum dolor sit amet',
            'created' => '2016-12-30 22:28:17'
        ],
    ];
}
