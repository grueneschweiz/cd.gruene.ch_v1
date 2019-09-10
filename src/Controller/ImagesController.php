<?php

namespace App\Controller;

use App\Controller\Component\ImageEditorComponent;
use App\Controller\Component\ImageFileHandlerComponent;
use App\Controller\Component\InvalidImageException;
use App\Model\Entity\User;
use Cake\Event\Event;

/**
 * Images Controller
 *
 * @property \App\Model\Table\ImagesTable $Images
 * @property ImageEditorComponent $ImageEditor
 * @property ImageFileHandlerComponent $ImageFileHandler
 */
class ImagesController extends AppController {
    /**
     * Do first
     *
     * @param Event $event
     */
    public function beforeFilter( Event $event ) {
        parent::beforeFilter( $event );
        // disable automatic form security for the ajaxAdd action
        $this->Security->setConfig( 'unlockedActions', [
            'ajaxAdd',
            'ajaxGetLogo',
            'ajaxAddLegal',
            'ajaxUploadImage'
        ] );
    }

    /**
     * Initialize
     */
    public function initialize() {
        parent::initialize();
        $this->loadComponent( 'RequestHandler' );
        $this->loadComponent( 'ImageFileHandler' );
        $this->loadComponent( 'ImageEditor' );
    }

    /**
     * Grant access to every logged in user
     *
     * @param User|array $user
     *
     * @return boolean
     */
    public function isAuthorized( $user ) {
        return true;
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index() {
        $this->paginate = [
            'contain' => [ 'Users' ]
        ];
        $images         = $this->paginate( $this->Images );

        $this->set( compact( 'images' ) );
        $this->set( '_serialize', [ 'images' ] );
    }

    /**
     * View method
     *
     * @param string|null $id Image id.
     *
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view( $id = null ) {
        $image = $this->Images->get( $id, [
            'contain' => [ 'Users', 'Bars' ]
        ] );

        $this->set( 'image', $image );
        $this->set( '_serialize', [ 'image' ] );
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        // get user id
        $userId = $this->Auth->user( 'id' );

        // get user
        $user = $this->Images->Users->get( $userId );

        // get logos of the user
        $logos = $user->getLogos()->find( 'list' )->toArray();

        // get image sizes
        $image_sizes = $this->Images->getImageSizes();

        // get layouts
        $layouts = $this->Images->getLayouts();

        // get color schemes
        $color_schemes = $this->Images->getColorSchemes();

        // get border options
        $border_options = $this->Images->getBorderOptions();

        $this->set( 'logos', $logos );
        $this->set( 'image_sizes', $image_sizes );
        $this->set( 'layouts', $layouts );
        $this->set( 'color_schemes', $color_schemes );
        $this->set( 'border_options', $border_options );
    }

    /**
     * Handle image uploads in chunks
     */
    public function ajaxUploadImage() {
        if ( $this->request->is( 'post' ) && $this->request->is( 'ajax' ) ) {
            $chunk     = $this->request->getData( 'imageChunk' );
            $file_name = $this->request->getData( 'fileName' );
            $part      = (int) $this->request->getData( 'chunkNum' );
            $content   = $this->ImageFileHandler->saveChunk( $chunk, $file_name, $part );
        } else {
            $content = 'access denied';
        }

        $this->set( [ 'content' => $content ] );
        $this->render( '/Element/ajaxreturn' );
    }

    /**
     * Generate image
     */
    public function ajaxAdd() {
        if ( $this->request->is( 'post' ) && $this->request->is( 'ajax' ) ) {
            // to generate big images we need more time
            set_time_limit( 180 );

            // get data
            $data          = json_decode( $this->request->getData( 'addImage' ) );
            $data->user_id = $this->Auth->user( 'id' );
            $original_id   = - 1;
            $hash          = false;

            // if a custom image was given
            if ( ! empty( $data->image->name ) ) {
                // save it
                try {
                    $path    = $this->ImageFileHandler->save( $data );
                    $success = $path;
                } catch ( InvalidImageException $exception ) {
                    $path    = false;
                    $success = $exception->getMessage();
                }

                // prevent duplicates
                if ( false !== $path ) {
                    $hash       = md5_file( $path );
                    $duplicates = $this->Images->find()->where( [ 'hash' => $hash ] );
                    if ( $duplicates->count() ) {
                        $older      = $duplicates->first();
                        $older_path = $this->ImageFileHandler->getRawImagePath( $older->filename );
                    }
                    if ( isset( $older_path ) ) {
                        unlink( $path );
                        $path        = $older_path;
                        $original_id = $older->id;
                        $success     = true;
                    }
                }

                // store in db
                if ( false !== $path && ! isset( $older_path ) ) {
                    $file_name   = pathinfo( $path, PATHINFO_FILENAME ) . '.' . pathinfo( $path, PATHINFO_EXTENSION );
                    $original_id = $this->Images->addOriginal( $path, $data, $file_name );
                    $success     = isset( $original_id ) && ! empty( $original_id );
                }
            } else {
                // generate gradient image if custom image is missing
                $gradient = $this->ImageEditor->createWithGradient( $data->preview->size );
                // save it
                $path    = $this->ImageFileHandler->saveGradient( $gradient );
                $success = (bool) $path;
            }

            // if we have a processable image
            if ( true === $success ) {
                $this->ImageEditor->createFromImage( $path );

                $zoom = (float) $data->image->zoom;

                // set final image dims
                $width  = (int) $data->preview->size->width;
                $height = (int) $data->preview->size->height;

                // if its not a gradient
                if ( ! isset( $gradient ) ) {
                    // since we've already read the image with the right orientation
                    // (according to the EXIF information) we should now reset the
                    // orientation, so the final image won't get rotated again.
                    $this->ImageEditor->setOrientation();

                    // set the color profile to prevent issues with CMYK etc
                    $this->ImageEditor->setColorProfile();

                    // crop
                    $startX = (int) - $data->image->pos->x;
                    $startY = (int) - $data->image->pos->y;
                    $this->ImageEditor->crop( $width / $zoom, $height / $zoom, $startX / $zoom, $startY / $zoom );

                    // resize image by width
                    $this->ImageEditor->resizeByWidth( $width );
                }
            } else {
                // if the image couldn't be saved (custom image) or created (generic image)
                $error = $success;
            }

            // if all went right until now
            if ( ! isset( $error ) ) {
                $success = $this->ImageEditor->addBorder( $data->border, $width, $height );
                if ( ! $success ) {
                    $error = $success;
                }
            } else {
                $error = $success;
            }

            // if all went right until now
            if ( ! isset( $error ) ) {
                if ( isset( $data->logo->src ) && strpos( $data->logo->src, 'alternative' ) ) {
                    /**
                     * Hack to cope with imagick 6.9.4-10's difficulties with rendering the svg correctly
                     * This only applies to the logo of the alternative zug
                     */
                    $data->logo->src = preg_replace( '/.svg$/', '.png', $data->logo->src );
                }
                $success = $this->ImageEditor->addLogo( $data->logo, $width, $height );
                if ( ! $success ) {
                    $error = $success;
                }
            } else {
                $error = $success;
            }

            // if all went right until now
            if ( ! isset( $error ) ) {
                $success = $this->ImageEditor->addBars( $data->bars, $width, $height, 'auto' === $data->border->type );
                if ( ! $success ) {
                    $error = $success;
                }
            } else {
                $error = $success;
            }

            // if all went right until now
            if ( ! isset( $error ) ) {
                $filename = $this->ImageEditor->save();
                $this->Images->addFinal( $this->ImageEditor->path, $data, $filename, $original_id );

                $content = [ 'success' => true, 'filename' => $filename, 'rawImageHash' => $hash ];
            } else {
                $content = $error;
            }
        } else {
            $content = 'access denied';
        }

        $json = json_encode( $content );
        $this->set( [ 'content' => $json ] );
        $this->render( '/Element/ajaxreturn' );
    }

    /**
     * Serve logo
     */
    public function ajaxGetLogo() {
        if ( $this->request->is( 'post' ) && $this->request->is( 'ajax' ) ) {
            // get data
            $data = json_decode( $this->request->getData( 'id' ) );

            // if logo id was given
            if ( ! empty( $data->id ) ) {
                // and user has access to this logo
                $userId = $this->Auth->user( 'id' );
                $user   = $this->Images->Users->get( $userId );

                if ( $user->canUseLogo( $data->id ) ) {
                    // return top_path and subline
                    $logo       = $this->Images->Users->Groups->Logos->get( $data->id );
                    $path_parts = pathinfo( $logo['top_path'] );
                    $return     = [
                        //'top_path' => $logo['top_path'],
                        'dirname'   => $path_parts['dirname'] . '/colored/',
                        'filename'  => $path_parts['filename'],
                        'extension' => $path_parts['extension'],
                        'subline'   => $logo['subline'],
                    ];
                } else {
                    $return = 'access denied';
                }
            } else {
                // if no id was given
                $return = 'missing_id';
            }

        } else {
            $return = 'access denied';
        }
        $json = json_encode( $return );
        $this->set( [ 'content' => $json ] );
        $this->render( '/Element/ajaxreturn' );
    }

    /**
     * add legal information to image and return true on success
     */
    public function ajaxAddLegal() {
        if ( $this->request->is( 'post' ) && $this->request->is( 'ajax' ) ) {
            // get data
            $data   = $this->request->getData();
            $userId = $this->Auth->user( 'id' );
            $return = $this->Images->addLegal( $data, $userId );
        } else {
            $return = 'access denied';
        }
        $json = json_encode( $return );
        $this->set( [ 'content' => $json ] );
        $this->render( '/Element/ajaxreturn' );
    }
}
