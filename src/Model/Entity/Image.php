<?php

namespace App\Model\Entity;

use App\Controller\Component\ImageFileHandlerComponent;
use Cake\ORM\Entity;

/**
 * Image Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $filename
 * @property int $original_id
 * @property float $width
 * @property float $height
 * @property string $flattext
 * @property string $hash
 * @property string $legal
 * @property boolean $reusable
 * @property int|null $logo_id
 * @property string $src
 * @property bool $isRawImage
 * @property bool $hasRawImage
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $deleted
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Logo $logo
 */
class Image extends Entity {

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
        '*'  => true,
        'id' => false
    ];

    protected function _getSrc() {
        if ( $this->isRawImage ) {
            return ImageFileHandlerComponent::pathToSrc( ImageFileHandlerComponent::getRawImagePath( $this->filename ) );
        } else {
            return ImageFileHandlerComponent::pathToSrc( ImageFileHandlerComponent::getFinalImagePath( $this->filename ) );
        }
    }

    protected function _getThumbSrc() {
        if ( $this->isRawImage ) {
            return ImageFileHandlerComponent::pathToSrc( ImageFileHandlerComponent::getRawThumbPath( $this->filename ) );
        } else {
            return ImageFileHandlerComponent::pathToSrc( ImageFileHandlerComponent::getFinalThumbPath( $this->filename ) );
        }
    }

    protected function _getIsRawImage() {
        return null === $this->original_id;
    }

    protected function _getHasRawImage() {
        return 0 <= $this->original_id;
    }
}
