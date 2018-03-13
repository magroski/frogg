<?php

namespace Frogg;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;

/**
 *
 * Amazon S3 abstraction
 */
class S3
{

    private $obj;
    private $bucket_name;

    /**
     *
     * Default constructor for class Frogg_S3
     *
     * @param string $access_key  AWS Access Key ID
     * @param string $secret_key  AWS Secret Access Key
     * @param string $bucket_name Bucket name
     */
    public function __construct($access_key, $secret_key, $bucket_name = "")
    {
        $credentialsInst = new Credentials($access_key, $secret_key);
        $this->obj       = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-1',
            'credentials' => $credentialsInst,
        ]);

        $this->bucket_name = $bucket_name;
    }

    /**
     *
     * Upload a file
     *
     * @param string $source Full path to the file, including the filename
     *
     * @param bool   $path
     * @param bool   $filename
     * @param bool   $contentType
     *
     * @return boolean
     */
    public function sendFile($source, $path = false, $filename = false, $contentType = false)
    {
        $full_path = '';
        $dest_path = '';

        try {
            $filename = ($filename) ? $filename : basename($source);
            $filename = $this->sanitizeFilename($filename);

            if ($path) {
                $full_path = $this->bucket_name.'/'.$path.'/'.$filename;
                $dest_path = $path.'/'.$filename;
            } else {
                $full_path = $this->bucket_name.'/'.$filename;
                $dest_path = $filename;
            }
            $objectInfo = [
                'Bucket'     => $this->bucket_name,
                'Key'        => $dest_path,
                'SourceFile' => $source,
                'ACL'        => 'public-read',
            ];

            if ($contentType) {
                $objectInfo['ContentType'] = $contentType;
            }
            $this->obj->putObject($objectInfo);
        } catch (\Exception $e) {
            error_log($e);

            return false;
        }

        return $full_path;
    }

    /**
     *
     * Delete a file
     *
     * @param string $file_path Url path to the file
     *
     * @return boolean
     */
    public function deleteFile($file_path)
    {
        try {
            $this->obj->deleteObject(['Bucket' => $this->bucket_name, 'Key' => $file_path,]);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     *
     * Set the bucket name
     *
     * @param string $bucket_name Bucket name
     */
    public function setBucketName($bucket_name)
    {
        $this->bucket_name = $bucket_name;
    }

    /**
     *
     * Sanitizes the filename removing not allowed characters by Amazon S3
     *
     * @param string $file_name Filename
     *
     * @return string Sanitized filename
     */
    public function sanitizeFilename($file_name)
    {
        return str_replace(["\\", "_", ":", " ", "+"], "-", $file_name);
    }
}