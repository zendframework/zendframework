<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace Zend\Cloud\StorageService\Adapter;

/**
 * Common interface for unstructured cloud storage.
 *
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage StorageService
 */
interface AdapterInterface
{
    // HTTP adapter to use for connections
    const HTTP_ADAPTER = 'http_adapter';

    /**
     * Get an item from the storage service.
     *
     * @param  string $path
     * @param  array $options
     * @return mixed
     */
    public function fetchItem($path, $options = null);

    /**
     * Store an item in the storage service.
     * WARNING: This operation overwrites any item that is located at
     * $destinationPath.
     * @param string $destinationPath
     * @param mixed  $data
     * @param  array $options
     * @return boolean
     */
    public function storeItem($destinationPath,
                              $data,
                              $options = null);

    /**
     * Delete an item in the storage service.
     *
     * @param  string $path
     * @param  array $options
     * @return void
     */
    public function deleteItem($path, $options = null);

    /**
     * Copy an item in the storage service to a given path.
     *
     * The $destinationPath must be a directory.
     *
     * @param  string $sourcePath
     * @param  string $destination path
     * @param  array $options
     * @return void
     */
    public function copyItem($sourcePath, $destinationPath, $options = null);

    /**
     * Move an item in the storage service to a given path.
     *
     * The $destinationPath must be a directory.
     *
     * @param  string $sourcePath
     * @param  string $destination path
     * @param  array $options
     * @return void
     */
    public function moveItem($sourcePath, $destinationPath, $options = null);

    /**
     * Rename an item in the storage service to a given name.
     *
     * @param  string $path
     * @param  string $name
     * @param  array $options
     * @return void
     */
    public function renameItem($path, $name, $options = null);

    /**
     * List items in the given directory in the storage service
     *
     * The $path must be a directory
     *
     * @param  string $path Must be a directory
     * @param  array $options
     * @return array A list of item names
     */
    public function listItems($path, $options = null);

    /**
     * Get a key/value array of metadata for the given path.
     *
     * @param  string $path
     * @param  array $options
     * @return array
     */
    public function fetchMetadata($path, $options = null);

    /**
     * Store a key/value array of metadata at the given path.
     * WARNING: This operation overwrites any metadata that is located at
     * $destinationPath.
     *
     * @param  string $destinationPath
     * @param  array $options
     * @return void
     */
    public function storeMetadata($destinationPath, $metadata, $options = null);

    /**
     * Delete a key/value array of metadata at the given path.
     *
     * @param  string $path
     * @param  array $options
     * @return void
     */
    public function deleteMetadata($path);

    /**
     * Get the concrete client.
     */
    public function getClient();
}
