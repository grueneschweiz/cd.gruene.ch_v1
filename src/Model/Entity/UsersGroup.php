<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UsersGroup Entity
 *
 * @property int $id
 * @property int $group_id
 * @property int $user_id
 * @property int $added_by_user_id
 * @property bool $admin
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $deleted
 *
 * @property \App\Model\Entity\Group $group
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\User $added_by_user
 */
class UsersGroup extends Entity
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
}
