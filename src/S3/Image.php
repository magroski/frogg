<?php

namespace Frogg\S3;

use Frogg\{
    S3, Upload, Validator
};

class Image
{

    private $img;

    /** @var \Frogg\Upload */
    private $handle;

    public $s3;

    #Are set only when the image comes from an URL
    private $width     = 0;
    private $height    = 0;
    private $signature = 0;

    private $_new_name;

    /**
     * @param string $accessKey Amazon S3 access key
     * @param string $secretKey Amazon S3 secret key
     * @param string $bucket    Amazon S3 bucket name
     */
    public function __construct($accessKey, $secretKey, $bucket)
    {
        $this->s3 = new S3($accessKey, $secretKey, $bucket);
    }

    /**
     * Load an image from a form file input
     *
     * @param bool $name File input name attribute
     *
     * @return void -
     */
    public function getFromInput($name = false)
    {
        if ($name) {
            $this->img       = $_FILES[$name];
            $this->handle    = new Upload($this->img);
            $this->_new_name = $this->s3->sanitizeFilename(uniqid("", true) . $this->handle->file_src_name);
        }
    }

    /**
     * Load an image from a system path
     *
     * @param bool $name File name
     * @param bool $path File path
     *
     * @return void -
     */
    public function getFromPath($name = false, $path = false)
    {
        if ($path && $name) {
            $this->img       = $path . '/' . $name;
            $this->handle    = new Upload($this->img);
            $this->_new_name = $this->s3->sanitizeFilename(uniqid("", true) . $this->handle->file_src_name);
        }
    }

    /**
     * Load an image from a given url
     *
     * @param string $url  File url
     * @param bool   $name Optional name of the destiny file
     *
     * @return bool
     */
    public function getFromURL($url, $name = false)
    {
        $tmp_path = sys_get_temp_dir() . '/';

        if (Validator::validate(Validator::V_LINK, $url)) {
            $image = getimagesize($url);
            switch ($image['mime']) {
                case 'image/gif':
                case 'image/png':
                case 'image/bmp':
                case 'image/jpeg':
                    if ($image['mime'] == 'image/gif') {
                        $type = '.gif';
                    }
                    if ($image['mime'] == 'image/png') {
                        $type = '.png';
                    }
                    if ($image['mime'] == 'image/bmp') {
                        $type = '.bmp';
                    }
                    if ($image['mime'] == 'image/jpeg') {
                        $type = '.jpg';
                    }

                    if (!$name) {
                        $name = uniqid('post-') . $type;
                    } else {
                        $name .= '-' . uniqid("", true) . $type;
                    }
                    file_put_contents($tmp_path . $name, file_get_contents($url));

                    $this->getFromPath($name, sys_get_temp_dir());
                    $this->width  = $image[0];
                    $this->height = $image[1];
                    return true;
                default:
                    return false;
            }
        }
        return false;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function convertJpeg()
    {
        $this->handle->image_convert = 'jpeg';
    }

    public function setJpegQuality($quality)
    {
        $this->convertJpeg();
        $this->handle->jpeg_quality = $quality;
    }

    /**
     * Returns the image signature
     */
    public function getSignature()
    {
        return puzzle_compress_cvec($this->signature);
    }

    /**
     * Sets whether the image format will be converted or not
     * @param $format
     */
    public function setImageConvert($format)
    {
        $this->handle->image_convert = $format;
    }

    /**
     * @param mixed $url URL of the image
     * @return bool
     */
    public static function checkMinImageSize($url)
    {
        $tmp_path = sys_get_temp_dir() . '/';

        if (!Validator::validate(Validator::V_LINK, $url)) {
            return false;
        }

        $image = getimagesize($url);

        return ($image[0] > 100 && $image[1] > 100);
    }

    /**
     * @param string $filename name of the file
     * @param string $path     path to the file
     *
     * @return bool
     */
    public function delete($filename, $path)
    {
        return $this->s3->deleteFile(rtrim($path, '/') . '/' . $filename);
    }

    /**
     * Sets the file name for the new image
     *
     * @param $name
     */
    public function setName($name)
    {
        $this->handle->file_dst_name_body = $name;
    }

    public function setNewName($name)
    {
        $this->_new_name = $this->s3->sanitizeFilename($name);
    }

    /**
     * Save the current image on the desired path
     *
     * @param string $path File system path to save the image to
     */
    public function save($path = 'i')
    {
        $tmp_path = sys_get_temp_dir() . '/';
        $this->handle->process($tmp_path);

        $this->s3->sendFile($tmp_path . $this->handle->file_dst_name, $path, $this->_new_name);
        unlink($tmp_path . $this->handle->file_dst_name);

        return $this->_new_name;
    }

    /**
     * Save the current image with fixed width
     *
     * @param string $width the width of the new image
     * @param string $path  File system path to save the image to
     */
    public function saveFixedWidth($width, $path = 'i')
    {
        $this->handle->image_resize  = true;
        $this->handle->image_ratio_y = true;
        $this->handle->image_x       = $width;

        $tmp_path = sys_get_temp_dir() . '/';
        $this->handle->process($tmp_path);

        $this->s3->sendFile($tmp_path . $this->handle->file_dst_name, $path, $this->_new_name);
        unlink($tmp_path . $this->handle->file_dst_name);

        return $this->_new_name;
    }

    /**
     * Save the current image with fixed height
     *
     * @param string $height the height of the new image
     * @param string $path   File system path to save the image to
     */
    public function saveFixedHeight($height, $path = 'i')
    {
        $this->handle->image_resize  = true;
        $this->handle->image_ratio_x = true;
        $this->handle->image_y       = $height;

        $tmp_path = sys_get_temp_dir() . '/';
        $this->handle->process($tmp_path);

        $this->s3->sendFile($tmp_path . $this->handle->file_dst_name, $path, $this->_new_name);
        unlink($tmp_path . $this->handle->file_dst_name);

        return $this->_new_name;
    }

    /**
     * Save the current image with max width and height keeping ratio
     *
     * @param int    $width  max width of the image
     * @param int    $height max height of the image
     * @param string $path   File system path to save the image to
     */
    public function saveMaxWidthHeight($width, $height = 20000, $path = 'i')
    {
        $this->handle->image_resize = true;
        $this->handle->image_ratio  = true;
        $this->handle->image_x      = $width;
        $this->handle->image_y      = $height;

        $tmp_path = sys_get_temp_dir() . '/';
        $this->handle->process($tmp_path);

        $this->s3->sendFile($tmp_path . $this->handle->file_dst_name, $path, $this->_new_name);
        unlink($tmp_path . $this->handle->file_dst_name);

        return $this->_new_name;
    }

    /**
     * Save the current image with fixed width and height cropping the exceeding.
     *
     * @param int    $width  width of the thumbnail
     * @param int    $height height of the thumbnail
     * @param string $path   File system path to save the image to
     */
    public function saveThumb($width, $height, $path = 'i')
    {
        $this->handle->image_resize     = true;
        $this->handle->image_ratio_crop = true;
        $this->handle->image_x          = $width;
        $this->handle->image_y          = $height;

        $tmp_path = sys_get_temp_dir() . '/';
        $this->handle->process($tmp_path);
        $this->s3->sendFile($tmp_path . $this->handle->file_dst_name, $path, $this->_new_name);
        unlink($tmp_path . $this->handle->file_dst_name);

        return $this->_new_name;
    }

    /**
     * Checks whether a file is an image
     *
     * @param string $name Name of the $_FILES[] field to be checked
     *
     * @return bool
     */
    public static function isImage($name)
    {
        if (isset($_FILES[$name])) {
            $tempFile = $_FILES[$name]['tmp_name'];
            if (!empty($tempFile) && file_exists($tempFile)) {
                $image = getimagesize($tempFile);
                switch ($image['mime']) {
                    case 'image/gif':
                    case 'image/png':
                    case 'image/bmp':
                    case 'image/tiff':
                    case 'image/jpeg':
                        return true;
                }
            }
        }

        if (file_exists($name)) {
            $image = getimagesize($name);
            switch ($image['mime']) {
                case 'image/gif':
                case 'image/png':
                case 'image/bmp':
                case 'image/tiff':
                case 'image/jpeg':
                    return true;
            }
        }

        return false;
    }

    /**
     * Returns the image width ans height
     *
     * @param string $name Name of the $_FILES[] field to be checked
     *
     * @return array|bool
     */
    public static function getImageSize($name)
    {
        if (isset($_FILES[$name])) {
            $tempFile = $_FILES[$name]['tmp_name'];
            if (!empty($tempFile) && file_exists($tempFile)) {
                $size = getimagesize($tempFile);

                return $size;
            }
        }

        return getimagesize($name);
    }
}