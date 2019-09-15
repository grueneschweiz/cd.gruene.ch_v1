<?php

namespace App\Model\Table;

use Cake\Datasource\ConnectionManager;
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
     * Add the original image
     *
     * @param string $path to the image
     * @param \stdClass $data @see ImagesController::ajaxAdd()
     * @param string $file_name
     *
     * @return bool|int the image id or false on error
     */
    public function addOriginal( string $path, \stdClass $data, $file_name ) {
        return $this->addImage( $path, $data, $file_name );
    }

    /**
     * Add imgage from given parameters
     *
     * @param string $path to the image
     * @param \stdClass $data @see ImagesController::ajaxAdd()
     * @param string $file_name
     * @param int $original_id the primary key of the original image
     *
     * @return bool|int the image id or false on error
     */
    private function addImage( string $path, \stdClass $data, string $file_name, ?int $original_id = null ) {
        if ( property_exists( $data->logo, 'id' ) ) {
            $logo_id = intval( $data->logo->id );
            $logo_id = $logo_id > 0 ? $logo_id : null;
        } else {
            $logo_id = null;
        }

        // only calculate hash of raw images
        // gradient based images have an $original_id of -1
        if ( null === $original_id ) {
            $hash = md5_file( $path );
        } else {
            $hash = null;
        }

        $dims = getimagesize( $path );

        $image              = $this->newEntity();
        $image->filename    = $file_name;
        $image->width       = $dims[0];
        $image->height      = $dims[1];
        $image->hash        = $hash;
        $image->user_id     = $data->user->id;
        $image->logo_id     = $logo_id;
        $image->original_id = $original_id;
        $image->flattext    = $this->_getBarText( $data->bars->data )
                              . ' ' . $data->logo->subline
                              . ' ' . $this->_stripCopyrightText( $data->copyright->text )
                              . ' ' . $data->user->full_name;

        $image = $this->save( $image );

        return $image ? $image->id : false;
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
            $tmp .= trim( $bar->text ) . ' ';
        }

        return $this->_stripDuplicates( trim( $tmp ) );
    }

    /**
     * Strip duplicate words from the given string
     *
     * @param string $string
     *
     * @return string
     */
    private function _stripDuplicates( string $string ): string {
        $tokens = explode( ' ', $string );

        return implode( ' ', array_unique( $tokens ) );
    }

    /**
     * Return only the text after the first collon, all if no collon is found
     *
     * @param string $string
     *
     * @return string
     */
    private function _stripCopyrightText( string $string ): string {
        $start = strpos( $string, ':' );

        if ( $start >= 0 ) {
            return trim( substr( $string, $start + 1 ) );
        }

        return $string;
    }

    /**
     * Append the given bar text to the image with the given id preventing duplicate words
     *
     * This function is mainly used for search purposes.
     *
     * @param int $original_id
     * @param \stdClass $data
     */
    public function appendBarText( int $original_id, \stdClass $data ) {
        $image = $this->get( $original_id );

        $text            = $image->flattext . ' ' . $this->_getBarText( $data->bars->data );
        $image->flattext = $this->_stripDuplicates( $text );

        $this->save( $image );
    }

    /**
     * Custom finder to get all final images
     *
     * @param Query $query
     *
     * @return Query
     */
    public function findFinal( Query $query ) {
        return $query->whereNotNull( 'original_id' );
    }

    /**
     * Add the final image
     *
     * @param string $path to the image
     * @param \stdClass $data @see ImagesController::ajaxAdd()
     * @param string $file_name
     * @param int $original_id the primary key of the original image
     *
     * @return bool|int the image id or false on error
     */
    public function addFinal( string $path, \stdClass $data, string $file_name, int $original_id ) {
        return $this->addImage( $path, $data, $file_name, $original_id );
    }

    /**
     * Complete image data with legal information. Update old information.
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
     * @param int $userId
     *
     * @return bool
     */
    public function addLegal( array $data, int $userId ) {
        $image = $this->find()
                      ->where( [ 'hash' => $data['hash'] ] )
                      ->andWhere( [ 'user_id' => $userId ] )
                      ->first();
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

    /**
     * Search for final images
     *
     * Search for final images with the given string using MySQL's full text
     * search. All text in the images flattext is searched. The results
     * are primarely ordered by the best match, secondary by creation date.
     *
     * @param string $string
     *
     * @return int[] the image ids
     */
    public function search( string $string ): array {
        $terms = $this->prepareTerms( $string );

        if ( ! $terms ) {
            return [];
        }

        $connection  = ConnectionManager::get( 'default' );
        $match_query = 'MATCH (flattext) AGAINST (? IN BOOLEAN MODE)';
        $results     = $connection->execute(
            "SELECT id, $match_query as score " .
            "FROM {$this->getTable()} " .
            "WHERE deleted IS NULL AND original_id IS NOT NULL AND $match_query" .
            'ORDER BY score DESC, created DESC',
            [ $terms, $terms ]
        )->fetchAll( 'assoc' );

        return count( $results ) ? array_column( $results, 'id' ) : [];
    }

    /**
     * Do only search word characters and allow partial matches (word start)
     *
     * @param string $string
     *
     * @return string|null
     */
    private function prepareTerms( string $string ): ?string {
        // quoted strings must be treated differently
        // so extract them first
        if ( preg_match_all( '/(".*")/U', $string, $quoted ) ) {
            $query  = implode( ' ', $quoted[0] ) . ' ';
            $string = trim( str_replace( $quoted[0], '', $string ) );
        } else {
            $query = '';
        }

        // if there are unquoted parts left, process them for partial matches
        if ( $string ) {
            $terms = preg_split( "/[^\w\+\-\"]+/Uu", $string, 0, PREG_SPLIT_NO_EMPTY );

            if ( ! $terms ) {
                return $query;
            }

            $query .= implode( '* ', $terms ) . '*';
        }

        return $query;
    }
}
