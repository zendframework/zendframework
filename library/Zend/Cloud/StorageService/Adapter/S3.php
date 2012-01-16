<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend\Cloud\StorageService
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * namespace
 */
namespace Zend\Cloud\StorageService\Adapter;

use Zend\Cloud\StorageService\Adapter,
    Zend\Cloud\StorageService\Exception,
    Zend\Service\Amazon\S3\S3 as AmazonS3;

/**
 * S3 adapter for unstructured cloud storage.
 *
 * @category   Zend
 * @package    Zend\Cloud\StorageService
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class S3 implements Adapter
{
    /*
     * Options array keys for the S3 adapter.
     */
    const BUCKET_NAME      = 'bucket_name';
    const BUCKET_AS_DOMAIN = 'bucket_as_domain?';
    const FETCH_STREAM     = 'fetch_stream';
    const METADATA         = 'metadata';

    /**
     * AWS constants
     */
    const AWS_ACCESS_KEY   = 'aws_accesskey';
    const AWS_SECRET_KEY   = 'aws_secretkey';

    /**
     * S3 service instance.
     * @var Zend\Service\Amazon\S3\S3
     */
    protected $_s3;
    protected $_defaultBucketName = null;
    protected $_defaultBucketAsDomain = false;

    /**
     * Constructor
     *
     * @param  array|Zend\Config\Config $options
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException('Invalid options provided');
        }

        if (!isset($options[self::AWS_ACCESS_KEY]) || !isset($options[self::AWS_SECRET_KEY])) {
            throw new Exception\InvalidArgumentException('AWS keys not specified!');
        }

        try {
            $this->_s3 = new AmazonS3($options[self::AWS_ACCESS_KEY],
                                      $options[self::AWS_SECRET_KEY]);
        } catch (Zend\Service\Amazon\S3\Exception  $e) {
            throw new Exception\RuntimeException('Error on create: '.$e->getMessage(), $e->getCode(), $e);
        }

        if (isset($options[self::HTTP_ADAPTER])) {
            $this->_s3->getHttpClient()->setAdapter($options[self::HTTP_ADAPTER]);
        }

        if (isset($options[self::BUCKET_NAME])) {
            $this->_defaultBucketName = $options[self::BUCKET_NAME];
        }

        if (isset($options[self::BUCKET_AS_DOMAIN])) {
            $this->_defaultBucketAsDomain = $options[self::BUCKET_AS_DOMAIN];
        }
    }

    /**
     * Get an item from the storage service.
     *
     * @TODO Support streams
     *
     * @param  string $path
     * @param  array $options
     * @return string
     */
    public function fetchItem($path, $options = array())
    {
        $fullPath = $this->_getFullPath($path, $options);
        try {
            if (!empty($options[self::FETCH_STREAM])) {
                return $this->_s3->getObjectStream($fullPath, $options[self::FETCH_STREAM]);
            } else {
                return $this->_s3->getObject($fullPath);
            }
        } catch (Zend\Service\Amazon\S3\Exception  $e) {
            throw new Exception\RuntimeException('Error on fetch: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Store an item in the storage service.
     *
     * WARNING: This operation overwrites any item that is located at
     * $destinationPath.
     *
     * @TODO Support streams
     *
     * @param string $destinationPath
     * @param string|resource $data
     * @param  array $options
     * @return void
     */
    public function storeItem($destinationPath, $data, $options = array())
    {
        try {
            $fullPath = $this->_getFullPath($destinationPath, $options);
            return $this->_s3->putObject(
                $fullPath,
                $data,
                empty($options[self::METADATA]) ? null : $options[self::METADATA]
            );
        } catch (Zend\Service\Amazon\S3\Exception  $e) {
            throw new Exception\RuntimeException('Error on store: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Delete an item in the storage service.
     *
     * @param  string $path
     * @param  array $options
     * @return void
     */
    public function deleteItem($path, $options = array())
    {
        try {
            $this->_s3->removeObject($this->_getFullPath($path, $options));
        } catch (Zend\Service\Amazon\S3\Exception  $e) {
            throw new Exception\RuntimeException('Error on delete: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Copy an item in the storage service to a given path.
     *
     * WARNING: This operation is *very* expensive for services that do not
     * support copying an item natively.
     *
     * @TODO Support streams for those services that don't support natively
     *
     * @param  string $sourcePath
     * @param  string $destination path
     * @param  array $options
     * @return void
     */
    public function copyItem($sourcePath, $destinationPath, $options = array())
    {
        try {
            // TODO We *really* need to add support for object copying in the S3 adapter
            $item = $this->fetch($this->_getFullPath(sourcePath), $options);
            $this->storeItem($item, $destinationPath, $options);
        } catch (Zend\Service\Amazon\S3\Exception  $e) {
            throw new Exception\RunTimeException('Error on copy: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Move an item in the storage service to a given path.
     *
     * @TODO Support streams for those services that don't support natively
     *
     * @param  string $sourcePath
     * @param  string $destination path
     * @param  array $options
     * @return void
     */
    public function moveItem($sourcePath, $destinationPath, $options = array())
    {
        try {
            $fullSourcePath = $this->_getFullPath($sourcePath, $options);
            $fullDestPath   = $this->_getFullPath($destinationPath, $options);
            return $this->_s3->moveObject(
                $fullSourcePath,
                $fullDestPath,
                empty($options[self::METADATA]) ? null : $options[self::METADATA]
            );
        } catch (Zend\Service\Amazon\S3\Exception  $e) {
            throw new Exception\RuntimeException('Error on move: '.$e->getMessage(), $e->getCode(), $e);
        }
     }

    /**
     * Rename an item in the storage service to a given name.
     *
     *
     * @param  string $path
     * @param  string $name
     * @param  array $options
     * @return void
     */
    public function renameItem($path, $name, $options = null)
    {
        throw new Exception\OperationNotAvailableException('Rename not implemented');
    }

    /**
     * List items in the given directory in the storage service
     *
     * The $path must be a directory
     *
     *
     * @param  string $path Must be a directory
     * @param  array $options
     * @return array A list of item names
     */
    public function listItems($path, $options = null)
    {
        try {
            // TODO Support 'prefix' parameter for Zend\Service\Amazon\S3\S3::getObjectsByBucket()
            return $this->_s3->getObjectsByBucket($this->_defaultBucketName);
        } catch (Zend\Service\Amazon\S3\Exception  $e) {
            throw new Exception\RuntimeException('Error on list: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get a key/value array of metadata for the given path.
     *
     * @param  string $path
     * @param  array $options
     * @return array
     */
    public function fetchMetadata($path, $options = array())
    {
        try {
            return $this->_s3->getInfo($this->_getFullPath($path, $options));
        } catch (Zend\Service\Amazon\S3\Exception  $e) {
            throw new Exception\RuntimeException('Error on fetch: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Store a key/value array of metadata at the given path.
     * WARNING: This operation overwrites any metadata that is located at
     * $destinationPath.
     *
     * @param  string $destinationPath
     * @param  array $options
     * @return void
     */
    public function storeMetadata($destinationPath, $metadata, $options = array())
    {
        throw new Exception\OperationNotAvailableException('Storing separate metadata is not supported, use storeItem() with \'metadata\' option key');
    }

    /**
     * Delete a key/value array of metadata at the given path.
     *
     * @param  string $path
     * @param  array $options
     * @return void
     */
    public function deleteMetadata($path)
    {
        throw new Exception\OperationNotAvailableException('Deleting metadata not supported');
    }

    /**
     * Get full path, including bucket, for an object
     *
     * @param  string $path
     * @param  array $options
     * @return void
     */
    protected function _getFullPath($path, $options)
    {
        if (isset($options[self::BUCKET_NAME])) {
            $bucket = $options[self::BUCKET_NAME];
        } else if (isset($this->_defaultBucketName)) {
            $bucket = $this->_defaultBucketName;
        } else {
            throw new Exception\InvalidArgumentException('Bucket name must be specified for S3 adapter.');
        }

        if (isset($options[self::BUCKET_AS_DOMAIN])) {
            // TODO: support bucket domain names
            throw new Exception\OperationNotAvailableException('The S3 adapter does not currently support buckets in domain names.');
        }

        return trim($bucket) . '/' . trim($path);
    }

    /**
     * Get the concrete client.
     * @return Zend\Service\Amazon\S3\S3
     */
    public function getClient()
    {
         return $this->_s3;
    }
}
