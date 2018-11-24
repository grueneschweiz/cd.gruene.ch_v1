<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Filesystem\Folder;

class ImageFileHandlerComponent extends Component
{
    public function save(\stdClass $data)
    {
        // generate a temporary image
        $this->_tempSaveRawImage($data->image);

        // set target image path
        $dir = new Folder(ROOT . DS . 'protected' . DS . 'rawimages', true);
        $filename = $data->image->name;
        $this->setTargetPath($dir->path, $filename);

        // validate image
        $this->allowed_mime = ['image/jpeg', 'image/png'];
        $this->allowed_ext = ['jpg', 'jpeg', 'png'];
        $this->max_file_size = 16 * 1024 * 1024; // 16MB
        $success = $this->validate();

        // save if valid
        if (true === $success) {
            $success = $this->saveRawImage();
        }

        return $success;
    }

    /**
     * Create image from base64 string and save as temporary file
     * Store temp path to $this->temp_path
     *
     * @param \stdClass $image
     */
    private function _tempSaveRawImage(\stdClass $image)
    {
        //save your data into a variable - last part is the base64 encoded image
        $encoded = strip_tags($image->src);

        //explode at ',' - the last part should be the encoded image now
        $exp = explode(',', $encoded);

        //we just get the last element with array_pop
        $base64 = array_pop($exp);

        //decode the image
        $data = base64_decode($base64);

        // create folder if it doesn't exist
        $dir = new Folder(WWW_ROOT . 'tmp', true);

        //generate temp file
        $temp_file = tempnam($dir->path, '');

        // store image to tempfile
        file_put_contents($temp_file, $data);

        $this->temp_path = $temp_file;
    }

    /**
     * Checks if file doesent exist and stors filepath to $this->target_path
     *
     * Increments numer of file by one, of a file with the same name already
     * exists.
     *
     * @param string $dir
     * @param string $filename
     */
    public function setTargetPath(string $dir, string $filename)
    {
        $filename = $this->_sanitizeFilename($filename);
        $target = $dir . DS . $filename;

        // Make sure we dont overwrite anything
        while (file_exists($target)) {
            // increment filename by 1
            $filename = $this->_incrementFilename($filename);
            // set new upload path
            $target = $dir . DS . $filename;
        }

        $this->target_path = $target;
    }

    /**
     * Strip out everything except a-z, A-Z, 0-9, hyphens, underscores and dots
     *
     * @param string $filename
     *
     * @return string
     */
    private function _sanitizeFilename(string $filename)
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9\-\._]/', '', $filename));
    }

    /**
     * Increments last number of given filename by one
     *
     * @param string $filename
     *
     * @return string
     */
    private function _incrementFilename(string $filename)
    {
        preg_match('/.*\.([a-zA-Z0-9]+)$/', $filename, $ext);
        preg_match('/.*?([0-9]*)\.[a-zA-Z0-9]+$/', $filename, $numb);
        $ext = empty($ext[1]) ? '' : (string)$ext[1];
        $numb = empty($numb[1]) ? '' : (int)$numb[1];

        $name = str_replace($numb . '.' . $ext, '', $filename);

        if (empty($numb)) {
            $numb = 1;
        } else {
            $numb++;
        }

        return $name . $numb . '.' . $ext;

    }

    /**
     * Checks if it's an JPEG oder PNG image and if its not oversize
     *
     * @return mixed true on success else error string
     */
    public function validate()
    {
        $temp_path = $this->temp_path;
        $allowed_mime = $this->allowed_mime;
        $allowed_ext = $this->allowed_ext;
        $max_file_size = $this->max_file_size; // bytes
        $target_file = $this->target_path;

        // Check file extension
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, $allowed_ext)) {
            return __('Only JPG, JPEG & PNG files are allowed.');
        }

        // Check if image file is a actual image or fake image
        $check = getimagesize($temp_path);
        if (false === $check) {
            return __('Only JPG, JPEG & PNG files are allowed.');
        } else {
            if (!in_array($check['mime'], $allowed_mime)) {
                return __('Only JPG, JPEG & PNG files are allowed.');
            }
        }

        // Check file size
        $filesize = filesize($temp_path);
        if ($filesize > $max_file_size) {
            return __('Max file size ({0}MB) exceeded', round($max_file_size / (1024 * 1024)));
        }

        return true;
    }

    /**
     * Moves the temp image to its target path defined by $this->setTargetPath()
     *
     * @return mixed true on success else error string
     */
    public function saveRawImage()
    {
        $temp_path = $this->temp_path;
        $target_path = $this->target_path;

        // Actually save the image
        if (rename($temp_path, $target_path)) {
            return true;
        } else {
            return __('Sorry, there was an error uploading your file.');
        }
    }

    /**
     * Saves a gradient image to the gradients folder and sets path. Cleans up folder after 24h
     *
     * @param \Imagick $image
     *
     * @return bool true if everything went well
     */
    public function saveGradient(\Imagick $image)
    {
        $format = 'jpeg';

        // set target image path
        $dir = new Folder(WWW_ROOT . 'gradients', true);
        $prefix = $image->getimageWidth() . 'x' . $image->getimageHeight() . '_';
        $filename = uniqid($prefix, true) . '.' . $format;
        $this->setTargetPath($dir->path, $filename);

        // delete old gradients
        $seconds = 86400; // 24h
        $this->_deleteOldFiles($dir->path, $seconds);

        // save
        $image->setImageFormat($format);

        return $image->writeImage($this->getPath());
    }

    /**
     * Deletes all files in $dir oder than $sec seconds
     *
     * @param string $dir path to directory
     * @param int $sec time files should be kept in seconds
     */
    private function _deleteOldFiles(string $dir, int $sec)
    {
        // cycle through all files in the directory
        foreach (glob($dir . DS . "*") as $file) {
            // delete file if it is older than $sec
            if (filemtime($file) < time() - $sec) {
                unlink($file);
            }
        }
    }

    /**
     * Returns image path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->target_path;
    }
}