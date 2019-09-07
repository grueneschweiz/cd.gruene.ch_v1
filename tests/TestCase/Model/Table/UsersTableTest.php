<?php

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersTable;
use Cake\ORM\Locator\TableLocator;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\UsersTable
     */
    public $Users;

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
        $config = TableRegistry::getTableLocator()->exists('Users') ? [] : ['className' => 'App\Model\Table\UsersTable'];
        $this->Users = TableRegistry::getTableLocator()->get('Users', $config);
        $this->Users->Groups->recover();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Users);

        parent::tearDown();
    }

    /**
     * User->getGroups() - User Entity
     */
    public function testUserGetGroups()
    {
        // super admin
        $g1 = $this->Users->get(1)->getGroups()->extract('id')->toArray();
        $test1 = [1, 5, 2, 4, 3, 6];
        $this->assertEquals($test1, $g1);

        // admin of second level must have all groups below its admin group + the parent group
        $g2 = $this->Users->get(2)->getGroups()->toArray();
        $test2 = $this->Users->Groups->find()
            ->contain(['UsersGroups'])
            ->where('id IN (2,3,4)')
            ->order('FIELD(Groups.id,2,4,3)')
            ->toArray();

        foreach ($test2 as $key => $test) {
            $this->assertEquals($test->id, $g2[$key]->id);
        }

        // non admin user with single group in levels 1,2,3 (nationalrat)
        $g3 = $this->Users->get(5)->getGroups()->toArray();
        $test3 = $this->Users->Groups->find()
            ->contain(['UsersGroups'])
            ->where('id IN (1,2,3)')
            ->order(['name' => 'ASC'])
            ->toArray();

        foreach ($test3 as $key => $test) {
            $this->assertEquals($test->id, $g3[$key]->id);
        }
    }

    /**
     * User->getAdminGroups() - User Entity
     */
    public function testUserGetAdminGroups()
    {
        // admin of second level must have all groups below its admin group + the parent group
        $g2 = $this->Users->get(2)->getManageableGroups()->toArray();
        $test2 = $this->Users->Groups->find()
            ->contain(['UsersGroups'])
            ->where('id IN (2,3,4)')
            ->order('FIELD(Groups.id,2,4,3)')
            ->toArray();

        foreach ($test2 as $key => $test) {
            $this->assertEquals($test->id, $g2[$key]->id);
        }

        // non admin
        $g3 = $this->Users->get(5)->getManageableGroups()->toArray();
        $this->assertEmpty($g3);

        // admin with same level non admin groups
        $g4 = $this->Users->get(3)->getManageableGroups()->toArray();
        $test4 = $this->Users->Groups->find()
            ->contain(['UsersGroups'])
            ->where('id IN (3)')
            ->order('FIELD(Groups.id,3)')
            ->toArray();
        foreach ($test4 as $key => $test) {
            $this->assertEquals($test->id, $g4[$key]->id);
        }
    }

    /**
     * User->isAdmin() - User Entity
     */
    public function testUserIsAdmin()
    {
        $this->assertEquals(true, $this->Users->get(1)->isAdmin());
        $this->assertEquals(true, $this->Users->get(2)->isAdmin());
        $this->assertEquals(false, $this->Users->get(5)->isAdmin());
    }

    /**
     * User->canManageGroup() - User Entity
     */
    public function testUserCanManageGroup()
    {
        // super admin
        $user = $this->Users->get(1);
        $groups = $this->Users->Groups->find();
        foreach ($groups as $group) {
            $this->assertEquals(true, $user->canManageGroup($group));
        }

        // non admin
        $user = $this->Users->get(5);
        foreach ($groups as $group) {
            $this->assertEquals(false, $user->canManageGroup($group));
        }

        // part admin
        $user = $this->Users->get(2);
        $admin_groups = [2, 3, 4];
        foreach ($groups as $group) {
            if (in_array($group->id, $admin_groups)) {
                $this->assertEquals(true, $user->canManageGroup($group));
            } else {
                $this->assertEquals(false, $user->canManageGroup($group));
            }
        }
    }

    /**
     * User->canManageLogo() - User Entity
     */
    public function testUserCanManageLogo()
    {
        // super admin
        $user = $this->Users->get(1);

        $Logos = TableRegistry::getTableLocator()->get('Logos');
        $logos = $Logos->find();

        foreach ($logos as $l) {
            $this->assertTrue($user->canManageLogo($l));
        }

        // non admin
        $user = $this->Users->get(5);
        foreach ($logos as $l) {
            $this->assertFalse($user->canManageLogo($l));
        }

        // part admin
        $user = $this->Users->get(2);
        $admin_logos = [2, 3, 4, 6];
        foreach ($logos as $l) {
            if (in_array($l->id, $admin_logos)) {
                $this->assertTrue($user->canManageLogo($l));
            } else {
                $this->assertFalse($user->canManageLogo($l));
            }
        }

        // part admin
        $user = $this->Users->get(3);
        $admin_logos = [3, 6];
        foreach ($logos as $l) {
            if (in_array($l->id, $admin_logos)) {
                $this->assertTrue($user->canManageLogo($l));
            } else {
                $this->assertFalse($user->canManageLogo($l));
            }
        }
    }

    /**
     * User->getLogos() - User Entity
     */
    public function testUserGetLogos()
    {
        // super admin
        $user = $this->Users->get(1);
        $logosExp = [1, 4, 5, 2, 3];
        $logosActual = $user->getLogos()->extract('id')->toArray();
        $this->assertEquals($logosExp, $logosActual);

        // non admin user with single group in levels 1,2,3 (nationalrat)
        $user = $this->Users->get(5);
        $logosExp = [1, 2, 3];
        $logosActual = $user->getLogos()->extract('id')->toArray();
        $this->assertEquals($logosExp, $logosActual);

        // part admin
        $user = $this->Users->get(2);
        $logosExp = [4, 2, 3];
        $logosActual = $user->getLogos()->extract('id')->toArray();
        $this->assertEquals($logosExp, $logosActual);

        // part admin
        $user = $this->Users->get(3);
        $logosExp = [4, 3];
        $logosActual = $user->getLogos()->extract('id')->toArray();
        $this->assertEquals($logosExp, $logosActual);
    }
}
