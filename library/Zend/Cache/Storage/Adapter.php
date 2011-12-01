<?php

namespace Zend\Cache\Storage;

interface Adapter
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

    /**
     * Constructor
     *
     * @param array|Traversable $options
     */
    public function __construct($options = array());

    /* configuration */

    /**
     * Set options.
     *
     * @param array|Traversable $options
     * @return Zend\Cache\Storage\Adapter
     */
    public function setOptions($options);

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions();

    /* reading */

    /**
     * Get an item.
     *
     * @param string $key
     * @param array $options
     * @return mixed Data on success and false on failure
     * @throws Zend\Cache\Exception
     */
    public function getItem($key, array $options = array());

    /**
     * Get multiple items.
     *
     * @param array $keys
     * @param array $options
     * @return array Assoziative array of existing keys and values
     * @throws Zend\Cache\Exception
     */
    public function getItems(array $keys, array $options = array());

    /**
     * Test if an item exists.
     *
     * @param string $key
     * @param array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function hasItem($key, array $options = array());

    /**
     * Test multiple items.
     *
     * @param array $keys
     * @param array $options
     * @return array Assoziative array of existing keys
     * @throws Zend\Cache\Exception
     */
    public function hasItems(array $keys, array $options = array());

    /**
     * Get metadata of an item.
     *
     * @param string $key
     * @param array $options
     * @return array|boolean Metadata or false on failure
     * @throws Zend\Cache\Exception
     */
    public function getMetadata($key, array $options = array());

    /**
     * Get multiple metadata
     *
     * @param array $keys
     * @param array $options
     * @return array Assoziative array of existing cache ids and its metadata
     * @throws Zend\Cache\Exception
     */
    public function getMetadatas(array $keys, array $options = array());

    /* writing */

    /**
     * Store an item.
     *
     * @param string $key
     * @param mixed $value
     * @param array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function setItem($key, $value, array $options = array());

    /**
     * Store multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function setItems(array $keyValuePairs, array $options = array());

    /**
     * Add an item.
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function addItem($key, $value, array $options = array());

    /**
     * Add multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function addItems(array $keyValuePairs, array $options = array());

    /**
     * Replace an item.
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function replaceItem($key, $value, array $options = array());

    /**
     * Replace multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function replaceItems(array $keyValuePairs, array $options = array());

    /**
     * Set item only if token matches
     *
     * @param mixed       $token
     * @param string|null $key
     * @param mixed       $value
     * @param array       $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function checkAndSetItem($token, $key, $value, array $options = array());

    /**
     * Reset lifetime of an item
     *
     * @param string $key
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws Zend\Cache\Exception
     */
    public function touchItem($key, array $options = array());

    /**
     * Reset lifetime of multiple items.
     *
     * @param array $keys
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws Zend\Cache\Exception
     */
    public function touchItems(array $keys, array $options = array());

    /**
     * Remove an item.
     *
     * @param string $key
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws Zend\Cache\Exception
     */
    public function removeItem($key, array $options = array());

    /**
     * Remove multiple items.
     *
     * @param array $keys
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws Zend\Cache\Exception
     */
    public function removeItems(array $keys, array $options = array());

    /**
     * Increment an item.
     *
     * @param string $key
     * @param int $value
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws Zend\Cache\Exception
     */
    public function incrementItem($key, $value, array $options = array());

    /**
     * Increment multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws Zend\Cache\Exception
     */
    public function incrementItems(array $keyValuePairs, array $options = array());

    /**
     * Decrement an item.
     *
     * @param string $key
     * @param int $value
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws Zend\Cache\Exception
     */
    public function decrementItem($key, $value, array $options = array());

    /**
     * Decrement multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws Zend\Cache\Exception
     */
    public function decrementItems(array $keyValuePairs, array $options = array());

    /* non-blocking */

    /**
     * Request multiple items.
     *
     * @param array $keys
     * @param array $options
     * @return boolean true on success or fale on failure
     * @throws Zend\Cache\Exception
     */
    public function getDelayed(array $keys, array $options = array());

    /**
     * Find items.
     *
     * @param int $mode Matching mode (Value of Zend\Cache\Storage\Adapter::MATCH_*)
     * @param array $options
     * @return boolean  true on success or fale on failure
     * @throw Zend\Cache\Exception
     */
    public function find($mode = self::MATCH_ACTIVE, array $options = array());

    /**
     * Fetches the next item from result set
     *
     * @return array|boolean The next item or false
     * @throws Zend\Cache\Exception
     */
    public function fetch();

    /**
     * Returns all items of result set.
     *
     * @return array The result set as array containing all items
     * @throws Zend\Cache\Exception
     */
    public function fetchAll();

    /* cleaning */

    /**
     * Clear items off all namespaces.
     *
     * @param int $mode Matching mode (Value of Zend\Cache\Storage\Adapter::MATCH_*)
     * @param array $options
     * @return boolean True on success or false on failure
     * @throw Zend\Cache\Exception
     */
    public function clear($mode = self::MATCH_EXPIRED, array $options = array());

    /**
     * Clear items by namespace.
     *
     * @param int $mode Matching mode (Value of Zend\Cache\Storage\Adapter::MATCH_*)
     * @param array $options
     * @return boolean True on success or false on failure
     * @throw Zend\Cache\Exception
     */
    public function clearByNamespace($mode = self::MATCH_EXPIRED, array $options = array());

    /**
     * Optimize adapter storage.
     *
     * @param array $options
     * @return boolean true on success or false on failure
     * @throws Zend\Cache\Exception
     */
    public function optimize(array $options = array());

    /* status */

    /**
     * Capabilities of this storage
     *
     * @return Zend\Cache\Storage\Capabilities
     */
    public function getCapabilities();

    /**
     * Get storage capacity.
     *
     * @param array $options
     * @return array|boolean Capacity as array or false on failure
     * @throw Zend\Cache\Exception
     */
    public function getCapacity(array $options = array());

}
