<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Logo Entity
 *
 * @property int $id
 * @property string $top_path
 * @property string $subline
 * @property string $name
 * @property int $added_by_user_id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $deleted
 *
 * @property \App\Model\Entity\Group[] $groups
 */
class Logo extends Entity
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
