<?php

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * LoginHashesFixture
 *
 */
class LoginHashesFixture extends TestFixture
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
        'type' => [
            'type' => 'string',
            'length' => 12,
            'null' => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'selector' => [
            'type' => 'string',
            'length' => 32,
            'null' => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'hash' => [
            'type' => 'string',
            'length' => 64,
            'null' => false,
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
        '_indexes' => [
            'selector_idx' => ['type' => 'index', 'columns' => ['selector'], 'length' => []],
            'fk_login_hashes_users1_idx' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'selector_UNIQUE' => ['type' => 'unique', 'columns' => ['selector'], 'length' => []],
            'fk_login_hashes_users1' => ['type' => 'foreign',
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
    public $records = [];

    public function init()
    {
        $this->records = [
            [
                'user_id' => 1,
                'type' => 'remember',
                'selector' => '7bea6da65726ad84659ca75bfe3d0719',
                'hash' => '6184dc88356226eebac508ddb03b599f73c50c21',
                'created' => date('Y-m-d H:i:s', strtotime('-2 years')),
            ],
            [
                'user_id' => 1,
                'type' => 'remember',
                'selector' => '1ca5a0478b26c9d521fa18dc29073193',
                'hash' => '6184dc88356226eebac508ddb03b599f73c50c21',
                'created' => date('Y-m-d H:i:s', strtotime('-2 days')),
            ],
        ];
        parent::init();
    }
}
