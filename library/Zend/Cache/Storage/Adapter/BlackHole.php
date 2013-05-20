<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use stdClass;
use Zend\Cache\Storage\Capabilities;
use Zend\Cache\Storage\StorageInterface;

class BlackHole implements StorageInterface
{
    /**
     * Capabilities of this adapter
     *
     * @var null|Capabilities
     */
    protected $capabilities = null;

    /**
     * Marker to change capabilities
     *
     * @var null|object
     */
    protected $capabilityMarker;

    /**
     * options
     *
     * @var null|AdapterOptions
     */
    protected $options;

    /**
     * Constructor
     *
     * @param  null|array|Traversable|AdapterOptions $options
     */
    public function __construct($options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }
    }

    /**
     * Set options.
     *
     * @param array|Traversable|Adapter\AdapterOptions $options
     * @return StorageInterface Fluent interface
     */
    public function setOptions($options)
    {
        if ($this->options !== $options) {
            if (!$options instanceof AdapterOptions) {
                $options = new AdapterOptions($options);
            }

            if ($this->options) {
                $this->options->setAdapter(null);
            }
            $options->setAdapter($this);
            $this->options = $options;
        }
        return $this;
    }

    /**
     * Get options
     *
     * @return Adapter\AdapterOptions
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new AdapterOptions());
        }
        return $this->options;
    }

    /**
     * Get an item.
     *
     * @param  string  $key
     * @param  bool $success
     * @param  mixed   $casToken
     * @return mixed Data on success, null on failure
     */
    public function getItem($key, & $success = null, & $casToken = null)
    {
        $success = false;
        return null;
    }

    /**
     * Get multiple items.
     *
     * @param  array $keys
     * @return array Associative array of keys and values
     */
    public function getItems(array $keys)
    {
        return array();
    }

    /**
     * Test if an item exists.
     *
     * @param  string $key
     * @return bool
     */
    public function hasItem($key)
    {
        return false;
    }

    /**
     * Test multiple items.
     *
     * @param  array $keys
     * @return array Array of found keys
     */
    public function hasItems(array $keys)
    {
        return array();
    }

    /**
     * Get metadata of an item.
     *
     * @param  string $key
     * @return array|bool Metadata on success, false on failure
     */
    public function getMetadata($key)
    {
        return false;
    }

    /**
     * Get multiple metadata
     *
     * @param  array $keys
     * @return array Associative array of keys and metadata
     */
    public function getMetadatas(array $keys)
    {
        return array();
    }

    /**
     * Store an item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     */
    public function setItem($key, $value)
    {
        return false;
    }

    /**
     * Store multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     */
    public function setItems(array $keyValuePairs)
    {
        return array_keys($keyValuePairs);
    }

    /**
     * Add an item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     */
    public function addItem($key, $value)
    {
        return false;
    }

    /**
     * Add multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     */
    public function addItems(array $keyValuePairs)
    {
        return array_keys($keyValuePairs);
    }

    /**
     * Replace an existing item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     */
    public function replaceItem($key, $value)
    {
        return false;
    }

    /**
     * Replace multiple existing items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     */
    public function replaceItems(array $keyValuePairs)
    {
        return array_keys($keyValuePairs);
    }

    /**
     * Set an item only if token matches
     *
     * It uses the token received from getItem() to check if the item has
     * changed before overwriting it.
     *
     * @param  mixed  $token
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     */
    public function checkAndSetItem($token, $key, $value)
    {
        return false;
    }

    /**
     * Reset lifetime of an item
     *
     * @param  string $key
     * @return bool
     */
    public function touchItem($key)
    {
        return false;
    }

    /**
     * Reset lifetime of multiple items.
     *
     * @param  array $keys
     * @return array Array of not updated keys
     */
    public function touchItems(array $keys)
    {
        return $keys;
    }

    /**
     * Remove an item.
     *
     * @param  string $key
     * @return bool
     */
    public function removeItem($key)
    {
        return false;
    }

    /**
     * Remove multiple items.
     *
     * @param  array $keys
     * @return array Array of not removed keys
     */
    public function removeItems(array $keys)
    {
        return $keys;
    }

    /**
     * Increment an item.
     *
     * @param  string $key
     * @param  int    $value
     * @return int|bool The new value on success, false on failure
     */
    public function incrementItem($key, $value)
    {
        return false;
    }

    /**
     * Increment multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Associative array of keys and new values
     */
    public function incrementItems(array $keyValuePairs)
    {
        return array();
    }

    /**
     * Decrement an item.
     *
     * @param  string $key
     * @param  int    $value
     * @return int|bool The new value on success, false on failure
     */
    public function decrementItem($key, $value)
    {
        return false;
    }

    /**
     * Decrement multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Associative array of keys and new values
     */
    public function decrementItems(array $keyValuePairs)
    {
        return array();
    }

    /**
     * Capabilities of this storage
     *
     * @return Capabilities
     */
    public function getCapabilities()
    {
        if ($this->capabilities === null) {
            // use default capabilities only
            $this->capabilityMarker = new stdClass();
            $this->capabilities     = new Capabilities($this, $this->capabilityMarker);
        }
        return $this->capabilities;
    }
}
