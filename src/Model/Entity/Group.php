<?php

namespace App\Model\Entity;

use App\Model\Table\GroupsTable;
use App\Model\Table\UsersTable;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Group Entity
 *
 * @property int $id
 * @property int $parent_id
 * @property int $lft
 * @property int $rght
 * @property string $name
 * @property int $added_by_user_id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $deleted
 *
 * @property Group $parent
 * @property User $added_by
 * @property array $direct_children
 *
 * @property \App\Model\Entity\Logo[] $logos
 * @property \App\Model\Entity\User[] $users
 */
class Group extends Entity
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
     * Virtual property to get parent
     *
     * @return array|\Cake\Datasource\EntityInterface|null
     */
    protected function _getParent()
    {
        return $this->getParent();
    }

    /**
     * Return parent entity or null if its the root element
     *
     * @return array|\Cake\Datasource\EntityInterface|null
     */
    public function getParent()
    {
        if (!$this->parent_id) {
            return null;
        }

        /** @var GroupsTable $Groups */
        $Groups = TableRegistry::get('Groups');

        return $Groups->find()->where(['id' => $this->parent_id])->first();
    }

    /**
     * Virtual property to get the user who added the group
     *
     * @return array|\Cake\Datasource\EntityInterface|null
     */
    protected function _getAddedBy()
    {
        if (!$this->added_by_user_id) {
            return null;
        }

        /** @var UsersTable $Users */
        $Users = TableRegistry::get('Users');
        return $Users->find('withTrashed')->where(['id' => $this->added_by_user_id])->first();
    }

    /**
     * Virtual property to get direct children
     *
     * @return array|\Cake\ORM\Query|null
     */
    protected function _getDirectChildren()
    {
        return $this->getDescendants(true)->toArray();
    }

    /**
     * Return array of descendants (excluding the parent (this group))
     *
     * @param bool $nested if set false, a flat array will be returned, else a nested one
     *
     * @return array|\Cake\ORM\Query|null
     */
    public function getDescendants(bool $nested = false)
    {
        /** @var GroupsTable $Groups */
        $Groups = TableRegistry::get('Groups');

        $groups = $Groups->find('children', ['for' => $this->id]);

        if ($nested) {
            $groups->find('threaded');
        }

        return $groups;
    }
}
