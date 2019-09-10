<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

class ImageFileHandlerComponent extends Component {
    const TTL_CHUNK = 3600; // one hour
    const TTL_GRADIENTS = 86400; // one day

    const BASE_PATH = ROOT . DS . 'protected';
    const FOLDER_CHUNKS = 'chunks';
    const FOLDER_RAWIMAGES = 'rawimages';
    const FOLDER_GRADIENTS = 'gradients';

    const ALLOWED_MIME = [ 'image/jpeg', 'image/png' ];
    const ALLOWED_EXT = [ 'jpg', 'jpeg', 'png' ];
    const ALLOWED_MAX_FILE_SIZE = 16 * 1024 * 1024; // 16MB

    /**
     * Validate the chunk and move the chunk to its final path.
     *
     * @param \stdClass $data
     *
     * @return bool|string
     *
     * @throws InvalidImageException
     */
    public function save( \stdClass $data ) {
        $file_name = $data->image->name;

        $chunk_path = $this->getChunkFileName( $file_name, true );
        $chunk      = new File( $chunk_path, false );

        $extension = $this->getExtension( $file_name );

        try {
            $this->validate( $chunk, $extension );
        } catch ( InvalidImageException $exception ) {
            $chunk->delete();
            throw $exception;
        }

        $target_path = $this->getTargetPath( $file_name, $this->getRawImagesFolder() );

        // move chunk to target path
        if ( $chunk->copy( $target_path ) && $chunk->delete() ) {
            return $target_path;
        } else {
            $chunk->delete();
        }

        return false;
    }

    /**
     * Create an deterministic but hard to guess filename
     *
     * @param string $original_file_name
     * @param bool $full_path return absolute path
     *
     * @return string
     */
    private function getChunkFileName( string $original_file_name, bool $full_path ): string {
        $file_name = md5( session_id() . $original_file_name );

        if ( $full_path ) {
            $file_name = $this->getChunkFolder()->path . DS . $file_name;
        }

        return $file_name;
    }

    /**
     * Return folder for chunks. Create it, if it doesn't exist.
     *
     * @return Folder
     */
    private function getChunkFolder(): Folder {
        return new Folder( self::BASE_PATH . DS . self::FOLDER_CHUNKS, true );
    }

    /**
     * Return the extension fo the given file name or false if none found.
     *
     * @param string $file_name
     *
     * @return bool|string
     */
    private function getExtension( string $file_name ) {
        if ( ! preg_match( '/.*\.([a-zA-Z0-9]+)$/', $file_name, $extension ) ) {
            return false;
        }

        return empty( $extension[1] ) ? false : strtolower( $extension[1] );
    }

    /**
     * Checks if it's an JPEG oder PNG image and if its not oversize
     *
     * @param File $file the file to validate
     * @param string $extension the file extension
     *
     * @throws InvalidImageException
     */
    public function validate( File $file, string $extension ): void {
        // file exists
        if ( ! $file->exists() ) {
            throw new InvalidImageException( __( 'Uploaded image not found' ) );
        }

        // file extension
        if ( ! in_array( $extension, self::ALLOWED_EXT ) ) {
            throw new InvalidImageException( __( 'Only JPG, JPEG & PNG files are allowed.' ) );
        }

        // mime type
        if ( ! in_array( $file->mime(), self::ALLOWED_MIME ) ) {
            throw new InvalidImageException( __( 'Only JPG, JPEG & PNG files are allowed.' ) );
        }

        // file size
        if ( self::ALLOWED_MAX_FILE_SIZE < $file->size() ) {
            throw new InvalidImageException( __( 'Max file size ({0}MB) exceeded', round( self::ALLOWED_MAX_FILE_SIZE / ( 1024 * 1024 ) ) ) );
        }

        // can we get the image size ?
        $check = getimagesize( $file->path );
        if ( false === $check ) {
            throw new InvalidImageException( __( 'Failed to read image. Try to resize it in an image editor and upload it again.' ) );
        }
    }

    /**
     * Generate a random file name and return the full path to store the raw image
     *
     * @param string $file_name
     * @param Folder $target_dir the destination folder
     *
     * @return bool|string
     */
    private function getTargetPath( string $file_name, Folder $target_dir ) {
        $extension = $this->getExtension( $file_name );

        if ( false === $extension ) {
            return false;
        }

        $target = new File( $target_dir->path . DS . $this->randomHash() . '.' . $extension, false );

        // make sure we get a unique file name (append counter)
        while ( $target->exists() ) {
            $target = new File( $target_dir->path . DS . $this->randomHash() . '.' . $extension, false );
        }

        if ( empty( $target->path ) ) {
            return false;
        }

        return $target->path;
    }

    /**
     * Get a random string of 32 chars length
     *
     * @return string
     */
    private function randomHash() {
        try {
            return md5( random_bytes( 16 ) );
        } catch ( \Exception $e ) {
            return md5( rand() );
        }
    }

    /**
     * Return folder for raw images. Create it, if it doesn't exist.
     *
     * @return Folder
     */
    private function getRawImagesFolder(): Folder {
        return new Folder( self::BASE_PATH . DS . self::FOLDER_RAWIMAGES, true );
    }

    /**
     * Save the image chunk
     *
     * @param string $chunk
     * @param string $original_file_name
     * @param int $chunkNum
     *
     * @return bool
     */
    public function saveChunk( string $chunk, string $original_file_name, int $chunkNum ): bool {
        $this->removeOldChunks();

        // the image is the part after the last comma
        $start = strrpos( $chunk, ',' );

        if ( false === $start ) {
            return false;
        }

        $base64_chunk = substr( $chunk, $start );
        $image_chunk  = base64_decode( $base64_chunk );

        if ( false === $image_chunk ) {
            return false;
        }

        // this must be the same for every chunk, so don't check for existance
        $file_path = $this->getChunkFileName( $original_file_name, true );

        // store image to tempfile
        $file = new File( $file_path, true, 0644 );

        // truncate any existing file content on first chunk
        return 0 === $chunkNum ? $file->write( $image_chunk ) : $file->append( $image_chunk );
    }

    /**
     * Remove chunks without change for TTL_CHUNK seconds
     */
    private function removeOldChunks(): void {
        $folder = $this->getChunkFolder();
        $this->removeOldFiles( $folder, self::TTL_CHUNK );
    }

    /**
     * Remove all files in folder that are older than $ttl seconds
     *
     * @param Folder $folder
     * @param int $ttl time to live in seconds
     *
     * @return bool
     */
    private function removeOldFiles( Folder $folder, int $ttl ): bool {
        $files   = $folder->read( false, false, true )[1];
        $max_age = time() - $ttl;

        foreach ( $files as $file ) {
            $f = new File( $file );

            if ( $f->lastChange() && $f->lastChange() < $max_age ) {
                if ( ! $f->delete() ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Saves a gradient image to the gradients folder and sets path. Cleans up folder after 24h
     *
     * @param \Imagick $image
     *
     * @return bool true if everything went well
     */
    public function saveGradient( \Imagick $image ) {
        $this->removeOldGradients();

        $format = 'jpeg';
        $path   = $this->getTargetPath( "gradient.$format", $this->getGradientsFolder() );
        $image->setImageFormat( $format );

        if ( $image->writeImage( $path ) ) {
            return $path;
        }

        return false;
    }

    /**
     * Remove gradients without change for TTL_GRADIENTS seconds
     */
    private function removeOldGradients(): void {
        $folder = $this->getGradientsFolder();
        $this->removeOldFiles( $folder, self::TTL_GRADIENTS );
    }

    /**
     * Return folder for gradients. Create it, if it doesn't exist.
     *
     * @return Folder
     */
    private function getGradientsFolder(): Folder {
        return new Folder( self::BASE_PATH . DS . self::FOLDER_GRADIENTS, true );
    }

    /**
     * Return full file path from the given file name or false if file doesn't exist
     *
     * @param string $file_name
     *
     * @return bool|string
     */
    public function getRawImagePath( string $file_name ) {
        $file = new File( $this->getRawImagesFolder()->path . DS . $file_name );

        return $file->exists() ? $file->pwd() : false;
    }
}
