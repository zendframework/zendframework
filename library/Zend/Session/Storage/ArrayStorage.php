<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-webat this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Session\Storage;

use ArrayObject,
    Zend\Session\Storage as Storable,
    Zend\Session\Exception;

/**
 * Array session storage
 *
 * Defines an ArrayObject interface for accessing session storage, with options 
 * for setting metadata, locking, and marking as immutable.
 *
 * @category   Zend
 * @package    Zend_Session
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ArrayStorage extends ArrayObject implements Storable
{
    /**
     * Is storage marked immutable?
     * @var bool
     */
    protected $_immutable = false;

    /**
     * Constructor
     *
     * Instantiates storage as an ArrayObject, allowing property access.
     * Also sets the initial request access time.
     * 
     * @param  array|ArrayAccess $input 
     * @param  int $flags 
     * @param  string $iteratorClass 
     * @return void
     */
    public function __construct($input = array(), $flags = \ArrayObject::ARRAY_AS_PROPS, $iteratorClass = '\\ArrayIterator')
    {
        parent::__construct($input, $flags, $iteratorClass);
        $this->setMetadata('_REQUEST_ACCESS_TIME', microtime(true));
    }

    /**
     * Retrieve the request access time
     * 
     * @return int
     */
    public function getRequestAccessTime()
    {
        return $this->getMetadata('_REQUEST_ACCESS_TIME');
    }

    /**
     * Set a value in the storage object
     *
     * If the object is marked as immutable, or the object or key is marked as 
     * locked, raises an exception.
     * 
     * @param  string $key 
     * @param  mixed $value 
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if ($this->isImmutable()) {
            throw new Exception\RuntimeException('Cannot set key "' . $key . '" as storage is marked immutable');
        }
        if ($this->isLocked($key)) {
            throw new Exception\RuntimeException('Cannot set key "' . $key . '" due to locking');
        }
        return parent::offsetSet($key, $value);
    }

    /**
     * Lock this storage instance, or a key within it
     *
     * @param  null|int|string $key 
     * @return ArrayStorage
     */
    public function lock($key = null)
    {
        if (null === $key) {
            $this->setMetadata('_READONLY', true);
            return $this;
        }
        if (isset($this[$key])) {
            $this->setMetadata('_LOCKS', array($key => true));
        }

        return $this;
    }

    /**
     * Is the object or key marked as locked?
     * 
     * @param  null|int|string $key 
     * @return bool
     */
    public function isLocked($key = null)
    {
        if ($this->isImmutable()) {
            // immutable trumps all
            return true;
        }

        if (null === $key) {
            // testing for global lock
            return $this->getMetadata('_READONLY');
        }

        $locks    = $this->getMetadata('_LOCKS');
        $readOnly = $this->getMetadata('_READONLY');

        if ($readOnly && !$locks) {
            // global lock in play; all keys are locked
            return true;
        } elseif ($readOnly && $locks) {
            return array_key_exists($key, $locks);
        }

        // test for individual locks
        if (!$locks) {
            return false;
        }
        return array_key_exists($key, $locks);
    }

    /**
     * Unlock an object or key marked as locked
     * 
     * @param  null|int|string $key 
     * @return ArrayStorage
     */
    public function unlock($key = null)
    {
        if (null === $key) {
            // Unlock everything
            $this->setMetadata('_READONLY', false);
            $this->setMetadata('_LOCKS', false);
            return $this;
        }

        $locks = $this->getMetadata('_LOCKS');
        if (!$locks) {
            if (!$this->getMetadata('_READONLY')) {
                return $this;
            }
            $array = $this->toArray();
            $keys  = array_keys($array);
            $locks = array_flip($keys);
            unset($array, $keys);
        }

        if (array_key_exists($key, $locks)) {
            unset($locks[$key]);
            $this->setMetadata('_LOCKS', $locks, true);
        }
        return $this;
    }

    /**
     * Mark the storage container as immutable
     * 
     * @return ArrayStorage
     */
    public function markImmutable()
    {
        $this->_immutable = true;
        return $this;
    }

    /**
     * Is the storage container marked as immutable?
     * 
     * @return bool
     */
    public function isImmutable()
    {
        return $this->_immutable;
    }

    /**
     * Set storage metadata
     *
     * Metadata is used to store information about the data being stored in the
     * object. Some example use cases include:
     * - Setting expiry data
     * - Maintaining access counts
     * - localizing session storage
     * - etc.
     * 
     * @param  string $key 
     * @param  mixed $value 
     * @param  bool $overwriteArray Whether to overwrite or merge array values; by default, merges
     * @return ArrayStorage
     */
    public function setMetadata($key, $value, $overwriteArray = false)
    {
        if ($this->_immutable) {
            throw new Exception\InvalidArgumentException('Cannot set metadata key "' . $key . '" as storage is marked immutable');
        }

        if (!isset($this['__ZF'])) {
            $this['__ZF'] = array();
        }
        if (isset($this['__ZF'][$key]) && is_array($value)) {
            if ($overwriteArray) {
                $this['__ZF'][$key] = $value;
            } elseif (null === $value) {
                // unset($this['__ZF'][$key]) led to "indirect modification...
                // has no effect" errors, so explicitly pulling array and 
                // unsetting key.
                $array = $this['__ZF'];
                unset($array[$key]);
                $this['__ZF'] = $array;
                unset($array);
            } else {
                $this['__ZF'][$key] = array_replace_recursive($this['__ZF'][$key], $value);
            }
        } else {
            if ((null === $value) && isset($this['__ZF'][$key])) {
                // unset($this['__ZF'][$key]) led to "indirect modification...
                // has no effect" errors, so explicitly pulling array and 
                // unsetting key.
                $array = $this['__ZF'];
                unset($array[$key]);
                $this['__ZF'] = $array;
                unset($array);
            } elseif (null !== $value) {
                $this['__ZF'][$key] = $value;
            }
        }
        return $this;
    }

    /**
     * Retrieve metadata for the storage object or a specific metadata key
     *
     * Returns false if no metadata stored, or no metadata exists for the given 
     * key.
     * 
     * @param  null|int|string $key 
     * @return mixed
     */
    public function getMetadata($key = null)
    {
        if (!isset($this['__ZF'])) {
            return false;
        }

        if (null === $key) {
            return $this['__ZF'];
        }

        if (!array_key_exists($key, $this['__ZF'])) {
            return false;
        }

        return $this['__ZF'][$key];
    }

    /**
     * Clear the storage object or a subkey of the object
     * 
     * @param  null|int|string $key 
     * @return ArrayStorage
     */
    public function clear($key = null)
    {
        if ($this->isImmutable()) {
            throw new Exception\RuntimeException('Cannot clear storage as it is marked immutable');
        }
        if (null === $key) {
            $this->exchangeArray(array());
            return $this;
        }

        if (!isset($this[$key])) {
            return $this;
        }

        // Clear key data
        unset($this[$key]);

        // Clear key metadata
        $this->setMetadata($key, null)
             ->unlock($key);

        return $this;
    }

    /**
     * Cast the object to an array
     *
     * Returns data only, no metadata.
     * 
     * @return array
     */
    public function toArray()
    {
        $values = $this->getArrayCopy();
        if (isset($values['__ZF'])) {
            unset($values['__ZF']);
        }
        return $values;
    }

    /**
     * Load the storage from another array
     *
     * Overwrites any data that was previously set.
     * 
     * @param  array $array 
     * @return ArrayStorage
     */
    public function fromArray(array $array)
    {
        $this->exchangeArray($array);
        return $this;
    }
}
