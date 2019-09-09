<?php

namespace App\Model\Table;

use App\Model\Entity\Image;
use Cake\Datasource\EntityInterface;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Images Model
 *
 * @property UsersTable $Users
 * @property LogosTable $Logos
 *
 * @method \App\Model\Entity\Image get( $primaryKey, $options = [] )
 * @method \App\Model\Entity\Image newEntity( $data = null, array $options = [] )
 * @method \App\Model\Entity\Image[] newEntities( array $data, array $options = [] )
 * @method \App\Model\Entity\Image|bool save( \Cake\Datasource\EntityInterface $entity, $options = [] )
 * @method \App\Model\Entity\Image patchEntity( \Cake\Datasource\EntityInterface $entity, array $data, array $options = [] )
 * @method \App\Model\Entity\Image[] patchEntities( $entities, array $data, array $options = [] )
 * @method \App\Model\Entity\Image findOrCreate( $search, callable $callback = null )
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ImagesTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     *
     * @return void
     */
    public function initialize( array $config ) {
        parent::initialize( $config );

        $this->setTable( 'images' );
        $this->setDisplayField( 'id' );
        $this->setPrimaryKey( 'id' );

        $this->addBehavior( 'Timestamp' );
        $this->addBehavior( 'Muffin/Trash.Trash' );

        $this->belongsTo( 'Users', [
            'foreignKey' => 'user_id',
            'joinType'   => 'LEFT'
        ] );

        $this->belongsTo( 'Logos', [
            'foreignKey' => 'logo_id',
            'joinType'   => 'LEFT'
        ] );
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationDefault( Validator $validator ) {
        $validator
            ->integer( 'id' )
            ->allowEmpty( 'id', 'create' );

        $validator
            ->numeric( 'width' )
            ->allowEmpty( 'width' );

        $validator
            ->numeric( 'height' )
            ->allowEmpty( 'height' );

        $validator
            ->allowEmpty( 'flattext' );

        $validator
            ->allowEmpty( 'logo_id' );

        $validator
            ->allowEmpty( 'legal' );

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     *
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules( RulesChecker $rules ) {
        $rules->add( $rules->existsIn( [ 'user_id' ], 'Users' ) );
        $rules->add( $rules->existsIn( [ 'logo_id' ], 'Logos' ) );

        return $rules;
    }

    /**
     * Define all image sizes here.
     *
     * @return array
     */
    public function getImageSizes() {
        return [
            '1080x1080' => __( 'Square' ),
            '1200x630'  => __( 'Facebook Timeline Image' ),
            '1920x1080' => __( 'Facebook Event Image' ),
            '1200x628'  => __( 'Facebook Featured Website' ),
            '1024x512'  => __( 'Twitter Feed Image' ),
            '1080x1920' => __( 'Instagram Story' ),
            '1140x638'  => __( 'Website Slider' ),
            'custom'    => __( 'Custom size' ),
        ];
    }

    /**
     * Define layouts here
     *
     * @return array
     */
    public function getLayouts() {
        return [
            'left'  => __( 'Bars left' ),
            'right' => __( 'Bars right' ),
        ];
    }

    /**
     * Define color schemes here
     *
     * @return array
     */
    public function getColorSchemes() {
        return [
            'white'      => __( 'White' ),
            'green'      => __( 'Green' ),
            'greengreen' => __( 'Green head- & sublines' ),
        ];
    }

    /**
     * Define border options here
     *
     * @return array
     */
    public function getBorderOptions() {
        return [
            'auto' => __( 'Standard' ),
            'none' => __( 'None' ),
        ];
    }

    /**
     * Add or update the original image
     *
     * @param string $path to the image
     * @param \stdClass $data @see ImagesController::ajaxAdd()
     * @param string $file_name
     *
     * @return int image id
     */
    public function addOriginal( string $path, \stdClass $data, $file_name ) {
        $hash = $this->getNewHash();
        $dims = getimagesize( $path );

        $image           = $this->newEntity();
        $image->filename = $file_name;
        $image->width    = $dims[0];
        $image->height   = $dims[1];
        $image->hash     = $hash;
        $image->user_id  = $data->user_id;
        $image->flattext = $this->_getBarText( $data->bars->data ) . ' ' . $data->logo->subline;

        $this->save( $image );

        return $image->id;
    }

    /**
     * Generate a new image hash
     *
     * @return string
     */
    public function getNewHash() {
        return uniqid( '' );
    }

    /**
     * Return flattened string from bars
     *
     * @param array $bars
     *
     * @return string
     */
    private function _getBarText( array $bars ): string {
        $tmp = '';
        foreach ( $bars as $bar ) {
            $tmp .= $bar->text . ' ';
        }

        return trim( $tmp );
    }

    /**
     * Add the final image
     *
     * @param string $path to the image
     * @param \stdClass $data @see ImagesController::ajaxAdd()
     * @param string $filename
     * @param int $original_id the primary key of the original image
     */
    public function addFinal( string $path, \stdClass $data, string $filename, int $original_id ) {
        $dims = getimagesize( $path );

        $image = $this->_getOrCreateByHash( $data->hash );

        $image->original_id = $original_id;
        $image->filename    = $filename;
        $image->width       = $dims[0];
        $image->height      = $dims[1];
        $image->user_id     = $data->user_id;

        $image->flattext .= $this->_getBarText( $data->bars->data );

        $this->save( $image );
    }

    /**
     * Get image by hash. Return a new entity if no image was found.
     *
     * @param $hash
     *
     * @return Image
     */
    private function _getOrCreateByHash( $hash ): Image {
        $image = $this->find()->where( [ 'hash' => $hash ] )->first();
        if ( ! $image ) {
            $image       = $this->newEntity();
            $image->hash = $hash;
        }

        return $image;
    }

    /**
     * Complete image data with legal information. Wait until image generation has finished.
     *
     * @param array $data as received by ImagesController::ajaxAddLegal()
     * Must contain at least the following key value pairs (key => [possible, values]):
     * - people => ["0", "1"]
     * - right_of_personality => ["0","1","unclear"]
     * - own_image => ["me", "stock", "agency", "friend", "unknown"]
     * Optional:
     * - others_can_use => [true, false]
     * - source => any input
     * - licence => ["other", "cc+", "cc"]
     * - originator => any input
     *
     * @return bool
     */
    public function addLegal( array $data ) {
        $image = $this->find()->where( [ 'hash' => $data['hash'] ] )->first();
        if ( ! $image ) {
            return false;
        }

        if ( $image->original_id ) {
            $original = $this->get( $image->original_id );
        } else {
            $original = $image;
        }

        $others_can_use = false;
        if ( ! empty( $data['others_can_use'] ) ) {
            $others_can_use = $this->_preValidateOthersCanUse( $data );
        }

        $original->reusable = $others_can_use;
        $original->legal    = json_encode( $this->_preValidateLegal( $data ) );

        return (bool) $this->save( $original );
    }

    /**
     * Make sure that the others can use may only be set to true if its absolutely not problematic.
     *
     * @param array $data
     *
     * @return bool
     */
    private function _preValidateOthersCanUse( array $data ) {
        $others_can_use = true;

        if ( "1" === $data['people'] && "1" !== $data['right_of_personality'] ) {
            $others_can_use = false;
        }

        if ( in_array( $data['own_image'], [ 'stock', 'unknown', '0' ] ) ) {
            $others_can_use = false;
        }

        if ( $others_can_use ) {
            $others_can_use = (bool) $data['others_can_use'];
        }

        return $others_can_use;
    }

    /**
     * Make sure all fields are set but have to make sense.
     *
     * @param $data
     *
     * @return array
     */
    private function _preValidateLegal( $data ) {
        $return = [];

        $return['people'] = (int) $data['people'];
        if ( ! $return['people'] ) {
            $return['right_of_personality'] = null;
        } else {
            $return['right_of_personality'] = $data['right_of_personality'];
        }

        $return['own_image'] = $data['own_image'];

        if ( 'stock' === $data['own_image'] ) {
            $return['source']  = $data['source'];
            $return['licence'] = $data['licence'];
        } else {
            $return['source']  = null;
            $return['licence'] = null;
        }

        if ( 'unknown' === $data['own_image'] ) {
            $return['originator'] = null;
        } else {
            $return['originator'] = $data['originator'];
        }

        return $return;
    }
}
