<?php
/**
 * Created by PhpStorm.
 * User: cyrillbolliger
 * Date: 08.06.18
 * Time: 12:29
 */

namespace App\View\Helper;


use Cake\ORM\Entity;
use Cake\View\Helper;


class ReferencedByHelper extends Helper
{
    public $helpers = ['Html'];

    public function addedBy($addedEntity): string
    {
        if (empty($addedEntity->added_by)) {
            return $this->_getHardDeleted();
        } else {
            return $this->_getReference(
                $addedEntity->added_by,
                'full_name_email',
                'Users',
                'view'
            );
        }
    }

    private function _getHardDeleted(): string
    {
        return '#' . __('deleted');
    }

    private function _getReference(
        Entity $referenced,
        string $property,
        string $controller,
        string $action
    ): string
    {
        if (empty($referenced->deleted)) {
            return $this->Html->link(
                h($referenced->$property),
                [
                    'controller' => $controller,
                    'action' => $action,
                    $referenced->id
                ]
            );
        } else {
            return '<span class="deleted">'
                . h($referenced->$property) .
                '</span>';
        }
    }

    public function managedBy($managedEntity): string
    {
        if (empty($managedEntity->managed_by)) {
            return $this->_getHardDeleted();
        } else {
            return $this->_getReference(
                $managedEntity->managed_by,
                'name',
                'Groups',
                'view'
            );
        }
    }
}