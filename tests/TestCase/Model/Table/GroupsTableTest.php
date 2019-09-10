<?php

namespace App\Test\TestCase\Model\Table;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Locator\TableLocator;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GroupsTable Test Case
 */
class GroupsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\GroupsTable
     */
    public $Groups;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.UsersGroups',
        'app.Logos',
        'app.Groups',
        'app.GroupsLogos',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Groups') ? [] : ['className' => 'App\Model\Table\GroupsTable'];
        $this->Groups = TableRegistry::getTableLocator()->get('Groups', $config);
        $this->Groups->recover();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Groups);

        parent::tearDown();
    }

    /**
     * Group->getParent() - Group Entity
     */
    public function testGroupGetParent()
    {
        // test element with parent
        $group = $this->Groups->get(4);
        $parent = $group->getParent();
        $this->assertEquals(2, $parent->id);

        // test root element
        $group = $this->Groups->get(1);
        $parent = $group->getParent();
        $this->assertEquals(null, $parent);

        // test missing element
        $group = $this->Groups->get(6);
        $this->assertEquals(null, $group->getParent());
    }

    /**
     * Group->getDescendants() - Group Entity
     */
    public function testGroupGetDescendants()
    {
        $group = $this->Groups->get(1);
        $descendants = $group->getDescendants();

        // group 1 is excluded because its the parent
        // group 6 is excluded because it has no parents
        $test = $this->Groups->find()
            ->where('id IN (5,2,4,3)')
            ->order('FIELD(Groups.id,5,2,4,3)');

        $this->assertEquals($test->toArray(), $descendants->toArray());
    }

    /**
     * Group->gadded_by - Group Entity (Virtual Property)
     */
    public function testGroupAddedBy()
    {
        $group = $this->Groups->get(1);
        $this->assertEquals(1, $group->added_by->id);
    }
}
