<?php

namespace App\Model\Entity;

use App\Model\Table\GroupsTable;
use App\Model\Table\LogosTable;
use App\Model\Table\UsersTable;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;
use Cake\ORM\Locator\TableLocator;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

/**
 * User Entity
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property int added_by_user_id
 * @property int $managed_by_group_id
 * @property bool super_admin
 * @property string lang
 * @property int $login_count
 * @property \Cake\I18n\Time $last_login
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $deleted
 *
 * @property User|null $added_by
 * @property Group|null $managed_by
 * @property \App\Model\Entity\Group[] $manageable_groups
 *
 * @property \App\Model\Entity\Image[] $images
 * @property \App\Model\Entity\Group[] $groups
 * @property \App\Model\Entity\Group[] $users_groups
 */
class User extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password'
    ];

    /**
     * Get all logos the user can manage
     *
     * @return array|\Cake\ORM\Query
     */
    public function getManageableLogos()
    {
        /** @var LogosTable $Logos */
        $locator = TableRegistry::getTableLocator();
        $Logos = $locator->get('Logos');

        $logos = $Logos->find();
        $adminLogos = [];
        foreach ($logos as $logo) {
            if ($this->canManageLogo($logo)) {
                $adminLogos[] = $logo->id;
            }
        }

        return $Logos->find()->where(['id IN (' . implode(',', $adminLogos) . ')']);
    }

    /**
     * Checks if the user has admin privileges for at least one group (check up the hierarchy)
     * the given logo is associated with, or if the logo is not yet associated with any groups.
     *
     * @param Logo|int $logo the logo or the logo id
     *
     * @return bool
     */
    public function canManageLogo($logo)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!$this->isAdmin()) {
            return false;
        }

        if ($logo instanceof Logo) {
            $logo_id = $logo->id;
        } else {
            $logo_id = $logo;
        }

        // get logo again in every case to make sure we've got the associations loaded
        $locator = TableRegistry::getTableLocator();
        /** @var LogosTable $Logos */
        $Logos = $locator->get('Logos');

        /** @var Logo $logo */
        $logo = $Logos->find()
            ->contain(['Groups'])
            ->where(['Logos.id' => $logo_id])
            ->first();

        // no association yet
        if (empty($logo->groups)) {
            return true;
        }

        foreach ($logo->groups as $group) {
            if ($this->canManageGroup($group)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Just for convenience
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->super_admin;
    }

    /**
     * Check if user has any admin privileges to any groups
     *
     * @return bool
     */
    public function isAdmin()
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $locator = TableRegistry::getTableLocator();
        /** @var UsersTable $Users */
        $Users = $locator->get('Users');

        return (bool)$Users->UsersGroups->find()
            ->where(['user_id' => $this->id, 'admin' => true])
            ->count();
    }

    /**
     * Checks if the user has admin privileges for the given group or one further up in the hierarchy
     *
     * @param Group|int $group the group or the group id
     *
     * @return bool
     */
    public function canManageGroup($group)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $locator = TableRegistry::getTableLocator();
        /** @var GroupsTable $Groups */
        $Groups = $locator->get('Groups');

        if ($group instanceof Group) {
            $group_id = $group->id;
            $parent = $group->getParent();
        } else {
            $group_id = $group;
            $parent = $Groups->get($group_id)->getParent();
        }

        $query = $Groups->find()
            ->contain(['UsersGroups'])
            ->matching('UsersGroups', function (Query $q) {
                return $q->where(['user_id' => $this->id, 'admin' => true]);
            })
            ->where([
                'Groups.id' => $group_id,
            ]);

        if ((bool)$query->first()) {
            return true;
        } else {
            if (null === $parent) {
                return false;
            }

            /** @var Group $parent */
            return $this->canManageGroup($parent);
        }
    }

    /**
     * Get all users the user can manage
     *
     * @return array|\Cake\ORM\Query
     */
    public function getManageableUsers()
    {
        $locator = TableRegistry::getTableLocator();
        /** @var UsersTable $Users */
        $Users = $locator->get('Users');

        $users = $Users->find();
        $adminUsers = [];
        foreach ($users as $user) {
            if ($this->canManageUser($user)) {
                $adminUsers[] = $user->id;
            }
        }

        return $Users->find()->where(['Users.id IN (' . implode(',', $adminUsers) . ')']);
    }

    /**
     * Checks if this user has admin privileges for the group or one further up in the hierarchy
     * that is responsible for managing the given user.
     *
     * @param User|int $user the user or the user id
     *
     * @return bool
     */
    public function canManageUser($user)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!($user instanceof User)) {
            $locator = TableRegistry::getTableLocator();
            /** @var UsersTable $Users */
            $Users = $locator->get('Users');
            $user = $Users->get($user);
        }

        if ($user->id === $this->id) {
            return true;
        }

        return $this->canManageGroup($user->managed_by_group_id);
    }

    /**
     * Check if this user has privileges to use this logo
     *
     * @param int|Logo $logo
     *
     * @return bool
     */
    public function canUseLogo($logo)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($logo instanceof Logo) {
            $logoId = $logo->id;
        } else {
            $logoId = $logo;
        }

        return in_array($logoId,
            $this->getLogos()->extract('id')->toArray());
    }

    /**
     * Get all logos this user can use
     *
     * @return Query
     */
    public function getLogos()
    {
        $groups = $this->getGroups()->extract('id')->toArray();

        if (empty($groups)) {
            $groups = [-1];
        }

        $locator = TableRegistry::getTableLocator();
        /** @var LogosTable $Logos */
        $Logos = $locator->get('Logos');

        return $Logos->find()
            ->distinct('Logos.id')
            ->matching('Groups', function (Query $q) use ($groups) {
                return $q->where('Groups.id IN (' . implode(',', $groups) . ')');
            })->order(['subline' => 'ASC']);
    }

    /**
     * Get all groups this user has access to in a flat array ordered by hierarchy and name.
     * For all groups the user is admin, the groups descends will be retrieved too.
     *
     * @param bool $includeDescendants
     *
     * @return array|\Cake\ORM\Query
     */
    public function getGroups(bool $includeDescendants = true)
    {
        $locator = TableRegistry::getTableLocator();
        /** @var GroupsTable $Groups */
        $Groups = $locator->get('Groups');

        if ($this->isSuperAdmin()) {
            $allIds = $Groups->find()->extract('id')->toArray();
            $groups = $Groups->find()
                ->where([
                    'OR' => [
                        'parent_id IS NULL',
                        'parent_id NOT IN (' . implode(',', $allIds) . ')'
                    ]
                ]);
        } else {
            $groups = $Groups->find()
                ->contain(['UsersGroups'])
                ->matching('UsersGroups', function (Query $q) {
                    return $q->where(['UsersGroups.user_id' => $this->id]);
                })
                ->order(['UsersGroups.admin' => 'DESC', 'Groups.name' => 'ASC']);
        }

        if ($includeDescendants) {
            $groups = $Groups->findIncludingDescendantsIfAdmin($groups, $this);
        }

        return $groups;
    }

    /**
     * Encrypt passwords
     *
     * @param string $value
     *
     * @return string
     */
    protected function _setPassword($value)
    {
        $hasher = new DefaultPasswordHasher();

        return $hasher->hash($value);
    }

    /**
     * Get fist and last name of user and the email (virtual property)
     *
     * @return string
     */
    protected function _getFullNameEmail()
    {
        return $this->_properties['first_name'] . '  ' .
            $this->_properties['last_name'] . ' (' .
            $this->_properties['email'] . ')';
    }

    /**
     * Virtual property to get the group who manages this user
     *
     * @return array|\Cake\Datasource\EntityInterface|null
     */
    protected function _getManagedBy()
    {
        if (!$this->managed_by_group_id) {
            return null;
        }

        $locator = TableRegistry::getTableLocator();
        /** @var GroupsTable $Groups */
        $Groups = $locator->get('Groups');

        return $Groups->find('withTrashed')->where(['id' => $this->managed_by_group_id])->first();
    }

    /**
     * Virtual property to get the user who added this user
     *
     * @return array|\Cake\Datasource\EntityInterface|null
     */
    protected function _getAddedBy()
    {
        if (!$this->added_by_user_id) {
            return null;
        }

        $locator = TableRegistry::getTableLocator();
        /** @var UsersTable $Users */
        $Users = $locator->get('Users');

        return $Users->find('withTrashed')->where(['id' => $this->added_by_user_id])->first();
    }

    /**
     * Virtual property to get the groups the user belongs to
     *
     * @return array|Query
     */
    protected function _getGroups()
    {
        return $this->getGroups();
    }

    /**
     * Virtual Property to get the groups the user can manage
     *
     * @return array|Query
     */
    protected function _getManageableGroups()
    {
        return $this->getManageableGroups();
    }

    /**
     * Get all groups this user has admin privileges to in a flat array ordered by hierarchy and name
     *
     * @param bool $includeDescendants
     *
     * @return array|\Cake\ORM\Query
     */
    public function getManageableGroups(bool $includeDescendants = true)
    {
        $groups = $this->getGroups($includeDescendants);

        $manageableGroups = [];
        foreach ($groups as $group) {
            if ($this->canManageGroup($group)) {
                $manageableGroups[] = $group->id;
            }
        }

        if (empty($manageableGroups)) {
            $manageableGroups = [-1];
        }

        $locator = TableRegistry::getTableLocator();
        /** @var GroupsTable $Groups */
        $Groups = $locator->get('Groups');

        $manageableGroupsIds = implode(',', $manageableGroups);

        return $Groups->find()
            ->where("id IN ($manageableGroupsIds)")
            ->order("FIELD(Groups.id,$manageableGroupsIds)");
    }
}
