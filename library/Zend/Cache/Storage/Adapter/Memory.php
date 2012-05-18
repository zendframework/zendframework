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
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use ArrayObject,
    stdClass,
    Zend\Cache\Exception,
    Zend\Cache\Storage\Capabilities,
    Zend\Cache\Storage\Adapter\MemoryOptions,
    Zend\Cache\Utils;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Memory extends AbstractAdapter
{
    /**
     * Data Array
     *
     * Format:
     * array(
     *     <NAMESPACE> => array(
     *         <KEY> => array(
     *             0 => <VALUE>
     *             1 => <MICROTIME>
     *             2 => <TAGS>
     *         )
     *     )
     * )
     *
     * @var array
     */
    protected $data = array();

    /**
     * Set options.
     *
     * @param  array|\Traversable|MemoryOptions $options
     * @return Memory
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!$options instanceof MemoryOptions) {
            $options = new MemoryOptions($options);
        }

        return parent::setOptions($options);
    }

    /**
     * Get options.
     *
     * @return MemoryOptions
     * @see setOptions()
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new MemoryOptions());
        }
        return $this->options;
    }

    /* reading */

    /**
     * Internal method to get an item.
     *
     * Options:
     *  - ttl <int>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string  $normalizedKey
     * @param  array   $normalizedOptions
     * @param  boolean $success
     * @param  mixed   $casToken
     * @return mixed Data on success, null on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetItem(& $normalizedKey, array & $normalizedOptions, & $success = null, & $casToken = null)
    {
        $ns      = $normalizedOptions['namespace'];
        $success = isset($this->data[$ns][$normalizedKey]);
        if ($success) {
            $data = & $this->data[$ns][$normalizedKey];
            $ttl  = $normalizedOptions['ttl'];
            if ($ttl && microtime(true) >= ($data[1] + $ttl) ) {
                $success = false;
            }
        }

        if (!$success) {
            return null;
        }

        $casToken = $data[0];
        return $data[0];
    }

    /**
     * Internal method to get multiple items.
     *
     * Options:
     *  - ttl <int>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $normalizedKeys
     * @param  array $normalizedOptions
     * @return array Associative array of keys and values
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetItems(array & $normalizedKeys, array & $normalizedOptions)
    {
        $ns = $normalizedOptions['namespace'];
        if (!isset($this->data[$ns])) {
            return array();
        }

        $data = & $this->data[$ns];
        $ttl  = $normalizedOptions['ttl'];

        $result = array();
        foreach ($normalizedKeys as $normalizedKey) {
            if (isset($data[$normalizedKey])) {
                if (!$ttl || microtime(true) < ($data[$normalizedKey][1] + $ttl) ) {
                    $result[$normalizedKey] = $data[$normalizedKey][0];
                }
            }
        }

        return $result;
    }

    /**
     * Internal method to test if an item exists.
     *
     * Options:
     *  - ttl <int>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     */
    protected function internalHasItem(& $normalizedKey, array & $normalizedOptions)
    {
        $ns = $normalizedOptions['namespace'];
        if (!isset($this->data[$ns][$normalizedKey])) {
            return false;
        }

        // check if expired
        $ttl = $normalizedOptions['ttl'];
        if ($ttl && microtime(true) >= ($this->data[$ns][$normalizedKey][1] + $ttl) ) {
            return false;
        }

        return true;
    }

    /**
     * Internal method to test multiple items.
     *
     * Options:
     *  - ttl <int>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $keys
     * @param  array $options
     * @return array Array of found keys
     * @throws Exception\ExceptionInterface
     */
    protected function internalHasItems(array & $normalizedKeys, array & $normalizedOptions)
    {
        $ns = $normalizedOptions['namespace'];
        if (!isset($this->data[$ns])) {
            return array();
        }

        $data = & $this->data[$ns];
        $ttl  = $normalizedOptions['ttl'];

        $result = array();
        foreach ($normalizedKeys as $normalizedKey) {
            if (isset($data[$normalizedKey])) {
                if (!$ttl || microtime(true) < ($data[$normalizedKey][1] + $ttl) ) {
                    $result[] = $normalizedKey;
                }
            }
        }

        return $result;
    }

    /**
     * Get metadata of an item.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return array|boolean Metadata on success, false on failure
     * @throws Exception\ExceptionInterface
     *
     * @triggers getMetadata.pre(PreEvent)
     * @triggers getMetadata.post(PostEvent)
     * @triggers getMetadata.exception(ExceptionEvent)
     */
    protected function internalGetMetadata(& $normalizedKey, array & $normalizedOptions)
    {
        if (!$this->internalHasItem($normalizedKey, $normalizedOptions)) {
            return false;
        }

        $ns = $normalizedOptions['namespace'];
        return array(
            'mtime' => $this->data[$ns][$normalizedKey][1],
            'tags'  => $this->data[$ns][$normalizedKey][2],
        );
    }

    /* writing */

    /**
     * Internal method to store an item.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *  - tags <array>
     *    - An array of tags
     *
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     */
    protected function internalSetItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        if (!$this->hasFreeCapacity()) {
            $memoryLimit = $this->getOptions()->getMemoryLimit();
            throw new Exception\OutOfCapacityException(
                "Memory usage exceeds limit ({$memoryLimit})."
            );
        }

        $ns = $normalizedOptions['namespace'];
        $this->data[$ns][$normalizedKey] = array($value, microtime(true), $normalizedOptions['tags']);

        return true;
    }

    /**
     * Internal method to store multiple items.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *  - tags <array>
     *    - An array of tags
     *
     * @param  array $normalizedKeyValuePairs
     * @param  array $normalizedOptions
     * @return array Array of not stored keys
     * @throws Exception\ExceptionInterface
     */
    protected function internalSetItems(array & $normalizedKeyValuePairs, array & $normalizedOptions)
    {
        if (!$this->hasFreeCapacity()) {
            $memoryLimit = $this->getOptions()->getMemoryLimit();
            throw new Exception\OutOfCapacityException(
                'Memory usage exceeds limit ({$memoryLimit}).'
            );
        }

        $ns = $normalizedOptions['namespace'];
        if (!isset($this->data[$ns])) {
            $this->data[$ns] = array();
        }

        $data = & $this->data[$ns];
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            $data[$normalizedKey] = array($value, microtime(true), $normalizedOptions['tags']);
        }

        return array();
    }

    /**
     * Add an item.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *  - tags <array>
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception\ExceptionInterface
     */
    protected function internalAddItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        if (!$this->hasFreeCapacity()) {
            $memoryLimit = $this->getOptions()->getMemoryLimit();
            throw new Exception\OutOfCapacityException(
                "Memory usage exceeds limit ({$memoryLimit})."
            );
        }

        $ns = $normalizedOptions['namespace'];
        if (isset($this->data[$ns][$normalizedKey])) {
            return false;
        }

        $this->data[$ns][$normalizedKey] = array($value, microtime(true), $normalizedOptions['tags']);
        return true;
    }

    /**
     * Internal method to add multiple items.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *  - tags <array>
     *    - An array of tags
     *
     * @param  array $normalizedKeyValuePairs
     * @param  array $normalizedOptions
     * @return array Array of not stored keys
     * @throws Exception\ExceptionInterface
     */
    protected function internalAddItems(array & $normalizedKeyValuePairs, array & $normalizedOptions)
    {
        if (!$this->hasFreeCapacity()) {
            $memoryLimit = $this->getOptions()->getMemoryLimit();
            throw new Exception\OutOfCapacityException(
                'Memory usage exceeds limit ({$memoryLimit}).'
            );
        }

        $ns = $normalizedOptions['namespace'];
        if (!isset($this->data[$ns])) {
            $this->data[$ns] = array();
        }

        $result = array();
        $data   = & $this->data[$ns];
        $now    = microtime(true);
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            if (isset($data[$normalizedKey])) {
                $result[] = $normalizedKey;
            } else {
                $data[$normalizedKey] = array($value, $now, $normalizedOptions['tags']);
            }
        }

        return $result;
    }

    /**
     * Internal method to replace an existing item.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *  - tags <array>
     *    - An array of tags
     *
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     */
    protected function internalReplaceItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $ns = $normalizedOptions['namespace'];
        if (!isset($this->data[$ns][$normalizedKey])) {
            return false;
        }
        $this->data[$ns][$normalizedKey] = array($value, microtime(true), $normalizedOptions['tags']);

        return true;
    }

    /**
     * Internal method to replace multiple existing items.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *  - tags <array>
     *    - An array of tags
     *
     * @param  array $normalizedKeyValuePairs
     * @param  array $normalizedOptions
     * @return array Array of not stored keys
     * @throws Exception\ExceptionInterface
     */
    protected function internalReplaceItems(array & $normalizedKeyValuePairs, array & $normalizedOptions)
    {
        $ns = $normalizedOptions['namespace'];
        if (!isset($this->data[$ns])) {
            return array_keys($normalizedKeyValuePairs);
        }

        $result = array();
        $data   = & $this->data[$ns];
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            if (!isset($data[$normalizedKey])) {
                $result[] = $normalizedKey;
            } else {
                $data[$normalizedKey] = array($value, microtime(true), $normalizedOptions['tags']);
            }
        }

        return $result;
    }

    /**
     * Internal method to reset lifetime of an item
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     */
    protected function internalTouchItem(& $normalizedKey, array & $normalizedOptions)
    {
        $ns = $normalizedOptions['namespace'];

        if (!isset($this->data[$ns][$normalizedKey])) {
            return false;
        }

        $this->data[$ns][$normalizedKey][1] = microtime(true);
        return true;
    }

    /**
     * Internal method to remove an item.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     */
    protected function internalRemoveItem(& $normalizedKey, array & $normalizedOptions)
    {
        $ns = $normalizedOptions['namespace'];
        if (!isset($this->data[$ns][$normalizedKey])) {
            return false;
        }

        unset($this->data[$ns][$normalizedKey]);

        // remove empty namespace
        if (!$this->data[$ns]) {
            unset($this->data[$ns]);
        }

        return true;
    }

    /**
     * Internal method to increment an item.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  int    $value
     * @param  array  $normalizedOptions
     * @return int|boolean The new value on success, false on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalIncrementItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $ns   = $normalizedOptions['namespace'];
        $data = & $this->data[$ns];
        if (isset($data[$normalizedKey])) {
            $data[$normalizedKey][0]+= $value;
            $data[$normalizedKey][1] = microtime(true);
            $newValue = $data[$normalizedKey][0];
        } else {
            // initial value
            $newValue             = $value;
            $data[$normalizedKey] = array($newValue, microtime(true), null);
        }

        return $newValue;
    }

    /**
     * Internal method to decrement an item.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  int    $value
     * @param  array  $normalizedOptions
     * @return int|boolean The new value on success, false on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalDecrementItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $ns   = $normalizedOptions['namespace'];
        $data = & $this->data[$ns];
        if (isset($data[$normalizedKey])) {
            $data[$normalizedKey][0]-= $value;
            $data[$normalizedKey][1] = microtime(true);
            $newValue = $data[$normalizedKey][0];
        } else {
            // initial value
            $newValue             = -$value;
            $data[$normalizedKey] = array($newValue, microtime(true), null);
        }

        return $newValue;
    }

    /* find */

    /**
     * internal method to find items.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-live
     *  - namespace <string>
     *    - The namespace to use
     *  - tags <array>
     *    - Tags to search for used with matching modes of
     *      Adapter::MATCH_TAGS_*
     *
     * @param  int   $normalizedMode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    fetch()
     * @see    fetchAll()
     */
    protected function internalFind(& $normalizedMode, array & $normalizedOptions)
    {
        if ($this->stmtActive) {
            throw new Exception\RuntimeException('Statement already in use');
        }

        $tags      = & $normalizedOptions['tags'];
        $emptyTags = $keys = array();
        foreach ($this->data[ $normalizedOptions['namespace'] ] as $key => &$item) {

            // compare expired / active
            if (($normalizedMode & self::MATCH_ALL) != self::MATCH_ALL) {

                // if MATCH_EXPIRED -> filter active items
                if (($normalizedMode & self::MATCH_EXPIRED) == self::MATCH_EXPIRED) {
                    if ($this->internalHasItem($key, $normalizedOptions)) {
                        continue;
                    }

                // if MATCH_ACTIVE -> filter expired items
                } else {
                    if (!$this->internalHasItem($key, $normalizedOptions)) {
                        continue;
                    }
                }
            }

            // compare tags
            if ($tags !== null) {
                $tagsStored = isset($item[2]) ? $item[2] : $emptyTags;

                if ( ($normalizedMode & self::MATCH_TAGS_OR) == self::MATCH_TAGS_OR ) {
                    $matched = (count(array_diff($tags, $tagsStored)) != count($tags));
                } elseif ( ($normalizedMode & self::MATCH_TAGS_AND) == self::MATCH_TAGS_AND ) {
                    $matched = (count(array_diff($tags, $tagsStored)) == 0);
                }

                // negate
                if ( ($normalizedMode & self::MATCH_TAGS_NEGATE) == self::MATCH_TAGS_NEGATE ) {
                    $matched = !$matched;
                }

                if (!$matched) {
                    continue;
                }
            }

            $keys[] = $key;
        }

        // don't check expiry on fetch
        $normalizedOptions['ttl'] = 0;

        $this->stmtKeys    = $keys;
        $this->stmtOptions = $normalizedOptions;
        $this->stmtActive  = true;

        return true;
    }

    /**
     * Internal method to fetch the next item from result set
     *
     * @return array|boolean The next item or false
     * @throws Exception\ExceptionInterface
     */
    protected function internalFetch()
    {
        if (!$this->stmtActive) {
            return false;
        }

        $options = & $this->stmtOptions;

        // get the next valid item
        do {
            $key = array_shift($this->stmtKeys);
            if ($key === null) {
                break;
            }

            if (!$this->internalHasItem($key, $options)) {
                continue;
            }

            break;
        } while (true);

        // free statement after last item
        if (!$key) {
            $this->stmtActive  = false;
            $this->stmtKeys    = null;
            $this->stmtOptions = null;

            return false;
        }

        $ref    = & $this->data[ $options['namespace'] ][$key];
        $result = array();
        foreach ($options['select'] as $select) {
            if ($select == 'key') {
                $result['key'] = $key;
            } elseif ($select == 'value') {
                $result['value'] = $ref[0];
            } elseif ($select == 'mtime') {
                $result['mtime'] = $ref[1];
            } elseif ($select == 'tags') {
                $result['tags'] = $ref[2];
            } else {
                $result[$select] = null;
            }
        }

        return $result;
    }

    /* cleaning */

    /**
     * Internal method to clear items off all namespaces.
     *
     * @param  int   $normalizedMode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    clearByNamespace()
     */
    protected function internalClear(& $normalizedMode, array & $normalizedOptions)
    {
        if (!$normalizedOptions['tags'] && ($normalizedMode & self::MATCH_ALL) == self::MATCH_ALL) {
            $this->data = array();
        } else {
            foreach ($this->data as & $data) {
                $this->clearNamespacedDataArray($data, $normalizedMode, $normalizedOptions);
            }
        }

        return true;
    }

    /**
     * Clear items by namespace.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *  - tags <array>
     *    - Tags to search for used with matching modes of Adapter::MATCH_TAGS_*
     *
     * @param  int   $normalizedMode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    clear()
     */
    protected function internalClearByNamespace(& $normalizedMode, array & $normalizedOptions)
    {
        if (isset($this->data[ $normalizedOptions['namespace'] ])) {
            if (!$normalizedOptions['tags'] && ($normalizedMode & self::MATCH_ALL) == self::MATCH_ALL) {
                unset($this->data[ $normalizedOptions['namespace'] ]);
            } else {
                $this->clearNamespacedDataArray($this->data[ $normalizedOptions['namespace'] ], $normalizedMode, $normalizedOptions);
            }
        }

        return true;
    }

    /* status */

    /**
     * Internal method to get capabilities of this adapter
     *
     * @return Capabilities
     */
    protected function internalGetCapabilities()
    {
        if ($this->capabilities === null) {
            $this->capabilityMarker = new stdClass();
                $this->capabilities = new Capabilities(
                $this,
                $this->capabilityMarker,
                array(
                    'supportedDatatypes' => array(
                        'NULL'     => true,
                        'boolean'  => true,
                        'integer'  => true,
                        'double'   => true,
                        'string'   => true,
                        'array'    => true,
                        'object'   => true,
                        'resource' => true,
                    ),
                    'supportedMetadata' => array(
                        'mtime',
                        'tags',
                    ),
                    'maxTtl'             => PHP_INT_MAX,
                    'staticTtl'          => false,
                    'tagging'            => true,
                    'ttlPrecision'       => 0.05,
                    'expiredRead'        => true,
                    'maxKeyLength'       => 0,
                    'namespaceIsPrefix'  => false,
                    'namespaceSeparator' => '',
                    'iterable'           => true,
                    'clearAllNamespaces' => true,
                    'clearByNamespace'   => true,
                )
            );
        }

        return $this->capabilities;
    }

    /**
     * Internal method to get storage capacity.
     *
     * @param  array $normalizedOptions
     * @return array|boolean Associative array of capacity, false on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetCapacity(array & $normalizedOptions)
    {
        $total = $this->getOptions()->getMemoryLimit();
        $free  = $total - (float) memory_get_usage(true);
        return array(
            'total' => $total,
            'free'  => ($free >= 0) ? $free : 0,
        );
    }

    /* internal */

    /**
     * Has the memory adapter storage free capacity
     * to store items
     *
     * Similar logic as getCapacity() but without triggering
     * events and returns boolean.
     *
     * @return boolean
     */
    protected function hasFreeCapacity()
    {
        $total = $this->getOptions()->getMemoryLimit();

        // check memory limit disabled
        if ($total <= 0) {
            return true;
        }

        $free  = $total - (float) memory_get_usage(true);
        return ($free > 0);
    }

    /**
     * Internal method to run a clear command
     * on a given data array which doesn't contain namespaces.
     *
     * Options:
     *   - ttl  <float>  required
     *   - tags <array>  required
     *
     * @param array $data
     * @param int $mode
     * @param array $options
     */
    protected function clearNamespacedDataArray(array &$data, $mode, array &$options)
    {
        $tags = &$options['tags'];
        $time = microtime(true);
        $ttl  = $options['ttl'];

        $emptyTags = $keys = array();
        foreach ($data as $key => &$item) {

            // compare expired / active
            if (($mode & self::MATCH_ALL) != self::MATCH_ALL) {

                // if MATCH_EXPIRED mode selected don't match active items
                if (($mode & self::MATCH_EXPIRED) == self::MATCH_EXPIRED) {
                    if ($ttl == 0 || $time <= ($item[1]+$ttl) ) {
                        continue;
                    }

                // if MATCH_ACTIVE mode selected don't match expired items
                } elseif ($ttl > 0 && $time >= ($item[1]+$ttl)) {
                    continue;
                }
            }

            // compare tags
            if ($tags !== null) {
                $tagsStored = isset($item[2]) ? $item[2] : $emptyTags;

                if ( ($mode & self::MATCH_TAGS_OR) == self::MATCH_TAGS_OR ) {
                    $matched = (count(array_diff($tags, $tagsStored)) != count($tags));
                } elseif ( ($mode & self::MATCH_TAGS_AND) == self::MATCH_TAGS_AND ) {
                    $matched = (count(array_diff($tags, $tagsStored)) == 0);
                }

                // negate
                if ( ($mode & self::MATCH_TAGS_NEGATE) == self::MATCH_TAGS_NEGATE ) {
                    $matched = !$matched;
                }

                if (!$matched) {
                    continue;
                }
            }

            unset($data[$key]);
        }
    }
}
