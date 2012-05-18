<?php
/**
 * Zend Framework
 *
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
 * @package    Zend_Cache
 * @subpackage Storage_Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage_Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface AdapterInterface
{
    /**
     * Match expired items
     *
     * @var int
     */
    const MATCH_EXPIRED = 01;

    /**
     * Match active items
     *
     * @var int
     */
    const MATCH_ACTIVE = 02;

    /**
     * Match active and expired items
     *
     * @var int
     */
    const MATCH_ALL = 03;

    /**
     * Match tag(s) using OR operator
     *
     * @var int
     */
    const MATCH_TAGS_OR = 000;

    /**
     * Match tag(s) using AND operator
     *
     * @var int
     */
    const MATCH_TAGS_AND = 010;

    /**
     * Negate tag match
     *
     * @var int
     */
    const MATCH_TAGS_NEGATE = 020;

    /**
     * Match tag(s) using OR operator and negates result
     *
     * @var int
     */
    const MATCH_TAGS_OR_NOT = 020;

    /**
     * Match tag(s) using AND operator and negates result
     *
     * @var int
     */
    const MATCH_TAGS_AND_NOT = 030;

    /* configuration */

    /**
     * Set options.
     *
     * @param array|Traversable|Adapter\AdapterOptions $options
     * @return AdapterInterface
     */
    public function setOptions($options);

    /**
     * Get options
     *
     * @return Adapter\AdapterOptions
     */
    public function getOptions();

    /* reading */

    /**
     * Get an item.
     *
     * @param  string  $key
     * @param  array   $options
     * @param  boolean $success
     * @param  mixed   $casToken
     * @return mixed Data on success, null on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getItem($key, array $options = array(), & $success = null, & $casToken = null);

    /**
     * Get multiple items.
     *
     * @param  array $keys
     * @param  array $options
     * @return array Associative array of keys and values
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getItems(array $keys, array $options = array());

    /**
     * Test if an item exists.
     *
     * @param  string $key
     * @param  array $options
     * @return boolean
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function hasItem($key, array $options = array());

    /**
     * Test multiple items.
     *
     * @param  array $keys
     * @param  array $options
     * @return array Array of found keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function hasItems(array $keys, array $options = array());

    /**
     * Get metadata of an item.
     *
     * @param  string $key
     * @param  array $options
     * @return array|boolean Metadata on success, false on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getMetadata($key, array $options = array());

    /**
     * Get multiple metadata
     *
     * @param  array $keys
     * @param  array $options
     * @return array Associative array of keys and metadata
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getMetadatas(array $keys, array $options = array());

    /* writing */

    /**
     * Store an item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function setItem($key, $value, array $options = array());

    /**
     * Store multiple items.
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return array Array of not stored keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function setItems(array $keyValuePairs, array $options = array());

    /**
     * Add an item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function addItem($key, $value, array $options = array());

    /**
     * Add multiple items.
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return array Array of not stored keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function addItems(array $keyValuePairs, array $options = array());

    /**
     * Replace an existing item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function replaceItem($key, $value, array $options = array());

    /**
     * Replace multiple existing items.
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return array Array of not stored keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function replaceItems(array $keyValuePairs, array $options = array());

    /**
     * Set an item only if token matches
     *
     * It uses the token received from getItem() to check if the item has
     * changed before overwriting it.
     *
     * @param  mixed  $token
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws \Zend\Cache\Exception\ExceptionInterface
     * @see    getItem()
     * @see    setItem()
     */
    public function checkAndSetItem($token, $key, $value, array $options = array());

    /**
     * Reset lifetime of an item
     *
     * @param  string $key
     * @param  array  $options
     * @return boolean
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function touchItem($key, array $options = array());

    /**
     * Reset lifetime of multiple items.
     *
     * @param  array $keys
     * @param  array $options
     * @return array Array of not updated keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function touchItems(array $keys, array $options = array());

    /**
     * Remove an item.
     *
     * @param  string $key
     * @param  array  $options
     * @return boolean
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function removeItem($key, array $options = array());

    /**
     * Remove multiple items.
     *
     * @param  array $keys
     * @param  array $options
     * @return array Array of not removed keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function removeItems(array $keys, array $options = array());

    /**
     * Increment an item.
     *
     * @param  string $key
     * @param  int    $value
     * @param  array  $options
     * @return int|boolean The new value on success, false on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function incrementItem($key, $value, array $options = array());

    /**
     * Increment multiple items.
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return array Associative array of keys and new values
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function incrementItems(array $keyValuePairs, array $options = array());

    /**
     * Decrement an item.
     *
     * @param  string $key
     * @param  int    $value
     * @param  array  $options
     * @return int|boolean The new value on success, false on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function decrementItem($key, $value, array $options = array());

    /**
     * Decrement multiple items.
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return array Associative array of keys and new values
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function decrementItems(array $keyValuePairs, array $options = array());

    /* non-blocking */

    /**
     * Request multiple items.
     *
     * @param  array $keys
     * @param  array $options
     * @return boolean
     * @throws \Zend\Cache\Exception\ExceptionInterface
     * @see    fetch()
     * @see    fetchAll()
     */
    public function getDelayed(array $keys, array $options = array());

    /**
     * Find items.
     *
     * @param  int   $mode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws \Zend\Cache\Exception\ExceptionInterface
     * @see    fetch()
     * @see    fetchAll()
     */
    public function find($mode = self::MATCH_ACTIVE, array $options = array());

    /**
     * Fetches the next item from result set
     *
     * @return array|boolean The next item or false
     * @throws \Zend\Cache\Exception\ExceptionInterface
     * @see    fetchAll()
     */
    public function fetch();

    /**
     * Returns all items of result set.
     *
     * @return array The result set as array containing all items
     * @throws \Zend\Cache\Exception\ExceptionInterface
     * @see    fetch()
     */
    public function fetchAll();

    /* cleaning */

    /**
     * Clear items off all namespaces.
     *
     * @param  int   $mode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws \Zend\Cache\Exception\ExceptionInterface
     * @see    clearByNamespace()
     */
    public function clear($mode = self::MATCH_EXPIRED, array $options = array());

    /**
     * Clear items by namespace.
     *
     * @param  int   $mode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws \Zend\Cache\Exception\ExceptionInterface
     * @see    clear()
     */
    public function clearByNamespace($mode = self::MATCH_EXPIRED, array $options = array());

    /**
     * Optimize adapter storage.
     *
     * @param  array $options
     * @return boolean
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function optimize(array $options = array());

    /* status */

    /**
     * Capabilities of this storage
     *
     * @return Capabilities
     */
    public function getCapabilities();

    /**
     * Get storage capacity.
     *
     * @param  array $options
     * @return array|boolean Associative array of capacity, false on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getCapacity(array $options = array());
}
