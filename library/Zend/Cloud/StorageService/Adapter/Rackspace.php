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
 * @package    Zend_Cloud_StorageService
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cloud\StorageService\Adapter;

use Traversable,
    Zend\Cloud\StorageService\Adapter,
    Zend\Cloud\StorageService\Exception,
    Zend\Service\Rackspace\Exception as RackspaceException,
    Zend\Service\Rackspace\Files as RackspaceFile,
    Zend\Stdlib\ArrayUtils;

/**
 * Adapter for Rackspace cloud storage
 *
 * @category   Zend
 * @package    Zend_Cloud_StorageService
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Rackspace implements AdapterInterface
{
    const USER                = 'user';
    const API_KEY             = 'key';
    const REMOTE_CONTAINER    = 'container';
    const DELETE_METADATA_KEY = 'ZF_metadata_deleted';
    
    /**
     * The Rackspace adapter
     * @var RackspaceFile
     */
    protected $rackspace;

    /**
     * Container in which files are stored
     * @var string
     */
    protected $container = 'default';
    
    /**
     * Constructor
     *
     * @param  array|Traversable $options
     * @return void
     */
    function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options) || empty($options)) {
            throw new Exception\InvalidArgumentException('Invalid options provided');
        }

        try {
            $this->rackspace = new RackspaceFile($options[self::USER], $options[self::API_KEY]);
        } catch (RackspaceException $e) {
            throw new Exception\RuntimeException('Error on create: '.$e->getMessage(), $e->getCode(), $e);
        }
        
        if (isset($options[self::HTTP_ADAPTER])) {
            $this->rackspace->getHttpClient()->setAdapter($options[self::HTTP_ADAPTER]);
        }
        if (!empty($options[self::REMOTE_CONTAINER])) {
            $this->container = $options[self::REMOTE_CONTAINER];
        }    
    }

     /**
     * Get an item from the storage service.
     *
     * @param  string $path
     * @param  array $options
     * @return mixed
     */
    public function fetchItem($path, $options = null)
    {
        $item = $this->rackspace->getObject($this->container,$path, $options);
        if (!$this->rackspace->isSuccessful() && ($this->rackspace->getErrorCode()!='404')) {
            throw new Exception\RuntimeException('Error on fetch: '.$this->rackspace->getErrorMsg());
        }
        if (!empty($item)) {
            return $item->getContent();
        } else {
            return false;
        }
    }

    /**
     * Store an item in the storage service.
     * 
     * @param  string $destinationPath
     * @param  mixed $data
     * @param  array $options
     * @return void
     */
    public function storeItem($destinationPath, $data, $options = null)
    {
        $this->rackspace->storeObject($this->container,$destinationPath,$data,$options);
        if (!$this->rackspace->isSuccessful()) {
            throw new Exception\RuntimeException('Error on store: '.$this->rackspace->getErrorMsg());
        }
    }

    /**
     * Delete an item in the storage service.
     *
     * @param  string $path
     * @param  array $options
     * @return void
     */
    public function deleteItem($path, $options = null)
    {
        $this->rackspace->deleteObject($this->container,$path);
        if (!$this->rackspace->isSuccessful()) {
            throw new Exception\RuntimeException('Error on delete: '.$this->rackspace->getErrorMsg());
        }
    }

    /**
     * Copy an item in the storage service to a given path.
     *
     * @param  string $sourcePath
     * @param  string $destination path
     * @param  array $options
     * @return void
     */
    public function copyItem($sourcePath, $destinationPath, $options = null)
    {
        $this->rackspace->copyObject($this->container,$sourcePath,$this->container,$destinationPath,$options);
        if (!$this->rackspace->isSuccessful()) {
            throw new Exception\RuntimeException('Error on copy: '.$this->rackspace->getErrorMsg());
        }
    }

    /**
     * Move an item in the storage service to a given path.
     * WARNING: This operation is *very* expensive for services that do not
     * support moving an item natively.
     *
     * @param  string $sourcePath
     * @param  string $destination path
     * @param  array $options
     * @return void
     */
    public function moveItem($sourcePath, $destinationPath, $options = null)
    {
        try {
            $this->copyItem($sourcePath, $destinationPath, $options);
        } catch (Exception\RuntimeException $e) {
            throw new Exception\RuntimeException('Error on move: '.$e->getMessage());
        }    
        try {
            $this->deleteItem($sourcePath);
        } catch (Exception\RuntimeException $e) {
            $this->deleteItem($destinationPath);
            throw new Exception\RuntimeException('Error on move: '.$e->getMessage());
        }    
    }

    /**
     * Rename an item in the storage service to a given name.
     * 
     * @param  string $path
     * @param  string $name
     * @param  array $options
     * @return void
     */
    public function renameItem($path, $name, $options = null)
    {
        throw new Exception\OperationNotAvailableException('Renaming not implemented');
    }

    /**
     * Get a key/value array of metadata for the given path.
     *
     * @param  string $path
     * @param  array $options
     * @return array An associative array of key/value pairs specifying the metadata for this object.
     *                  If no metadata exists, an empty array is returned.
     */
    public function fetchMetadata($path, $options = null)
    {
        $result = $this->rackspace->getMetadataObject($this->container,$path);
        if (!$this->rackspace->isSuccessful()) {
            throw new Exception\RuntimeException('Error on fetch metadata: '.$this->rackspace->getErrorMsg());
        }
        $metadata = array();
        if (isset($result['metadata'])) {
            $metadata =  $result['metadata'];
        }
        // delete the self::DELETE_METADATA_KEY - this is a trick to remove all
        // the metadata information of an object (see deleteMetadata). 
        // Rackspace doesn't have an API to remove the metadata of an object
        unset($metadata[self::DELETE_METADATA_KEY]);
        return $metadata;
    }

    /**
     * Store a key/value array of metadata at the given path.
     * WARNING: This operation overwrites any metadata that is located at
     * $destinationPath.
     *
     * @param  string $destinationPath
     * @param  array  $metadata        associative array specifying the key/value pairs for the metadata.
     * @param  array  $options
     * @return void
     */
    public function storeMetadata($destinationPath, $metadata, $options = null)
    {
        $this->rackspace->setMetadataObject($this->container, $destinationPath, $metadata);
        if (!$this->rackspace->isSuccessful()) {
            throw new Exception\RuntimeException('Error on store metadata: '.$this->rackspace->getErrorMsg());
        }
     }

    /**
     * Delete a key/value array of metadata at the given path.
     *
     * @param  string $path
     * @param  array $metadata - An associative array specifying the key/value pairs for the metadata
     *                           to be deleted.  If null, all metadata associated with the object will
     *                           be deleted.
     * @param  array $options
     * @return void
     */
    public function deleteMetadata($path, $metadata = null, $options = null)
    {
        if (empty($metadata)) {
            $newMetadata = array(self::DELETE_METADATA_KEY => true);
            try {
                $this->storeMetadata($path, $newMetadata);
            } catch (Exception\RuntimeException $e) {
                throw new Exception\RuntimeException('Error on delete metadata: '.$e->getMessage());
            }
        } else {
            try {
                $oldMetadata = $this->fetchMetadata($path);
            } catch (Exception\RuntimeException $e) {
                throw new Exception\RuntimeException('Error on delete metadata: '.$e->getMessage());
            }
            $newMetadata = array_diff_assoc($oldMetadata, $metadata);
            try {
                $this->storeMetadata($path, $newMetadata);
            } catch (Exception\RuntimeException $e) {
                throw new Exception\RuntimeException('Error on delete metadata: '.$e->getMessage());
            }
        }
    }

    /*
     * Recursively traverse all the folders and build an array that contains
     * the path names for each folder.
     *
     * @param  string $path        folder path to get the list of folders from.
     * @param  array& $resultArray reference to the array that contains the path names
     *                             for each folder.
     * @return void
     */
    private function getAllFolders($path, &$resultArray)
    {
        if (!empty($path)) {
            $options = array (
                'prefix'    => $path
            );
        }    
        $files = $this->rackspace->getObjects($this->container, $options);
        if (!$this->rackspace->isSuccessful()) {
            throw new Exception\RuntimeException('Error on get all folders: '.$this->rackspace->getErrorMsg());
        }
        $resultArray = array();
        foreach ($files as $file) {
            $resultArray[dirname($file->getName())] = true;
        }
        $resultArray = array_keys($resultArray);
    }

    /**
     * Return an array of the items contained in the given path.  The items
     * returned are the files or objects that in the specified path.
     *
     * @param  string $path
     * @param  array  $options
     * @return array
     */
    public function listItems($path, $options = null)
    {
        if (!empty($path)) {
            $options = array (
                'prefix'    => $path
            );
        }   
        
        $files = $this->rackspace->getObjects($this->container,$options);
        if (!$this->rackspace->isSuccessful()) {
            throw new Exception\RuntimeException('Error on list items: '.$this->rackspace->getErrorMsg());
        }
        $resultArray = array();
        if (!empty($files)) {
            foreach ($files as $file) {
                $resultArray[] = $file->getName();
            }
        }    
        return $resultArray;
    }

    /**
     * Get the concrete client.
     *
     * @return RackspaceFile
     */
    public function getClient()
    {
         return $this->rackspace;
    }
}
