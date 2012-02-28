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
     * @param  array|Traversable|MemoryOptions $options
     * @return Memory
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!$options instanceof MemoryOptions) {
            $options = new MemoryOptions($options);
        }

        $this->options = $options;
        return $this;
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
     *  - ignore_missing_items <boolean>
     *    - Throw exception on missing item or return false
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return mixed Data on success or false on failure
     * @throws Exception
     */
    protected function internalGetItem(& $normalizedKey, array & $normalizedOptions)
    {
        $ns    = $normalizedOptions['namespace'];
        $exist = isset($this->data[$ns][$normalizedKey]);
        if ($exist) {
            $data = & $this->data[$ns][$normalizedKey];
            $ttl  = $normalizedOptions['ttl'];
            if ($ttl && microtime(true) >= ($data[1] + $ttl) ) {
                $exist = false;
            }
        }

        if (!$exist) {
            if (!$normalizedOptions['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException("Key '{$normalizedKey}' not found on namespace '{$ns}'");
            }
            $result = false;
        } else {
            $result = $data[0];
            if (array_key_exists('token', $normalizedOptions)) {
                $normalizedOptions['token'] = $data[0];
            }
        }

        return $result;
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
     * @return array Associative array of existing keys and values
     * @throws Exception
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
     * @throws Exception
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
     * @return array Array of existing keys
     * @throws Exception
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
            if (!$ttl || microtime(true) < ($data[$normalizedKey][1] + $ttl) ) {
                $result[$normalizedKey] = true;
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
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return array|boolean Metadata or false on failure
     * @throws Exception
     *
     * @triggers getMetadata.pre(PreEvent)
     * @triggers getMetadata.post(PostEvent)
     * @triggers getMetadata.exception(ExceptionEvent)
     */
    protected function internalGetMetadata(& $normalizedKey, array & $normalizedOptions)
    {
        if (!$this->internalHasItem($normalizedKey, $normalizedOptions)) {
            if (!$normalizedOptions['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException(
                    "Key '{$normalizedKey}' not found on namespace '{$normalizedOptions['namespace']}'"
                );
            }
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
     *
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception
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
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $normalizedKeyValuePairs
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception
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

        return true;
    }

    /**
     * Add an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception
     *
     * @triggers addItem.pre(PreEvent)
     * @triggers addItem.post(PostEvent)
     * @triggers addItem.exception(ExceptionEvent)
     */
    public function addItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'value'   => & $value,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            if (!$this->hasFreeCapacity()) {
                $memoryLimit = $baseOptions->getMemoryLimit();
                throw new Exception\OutOfCapacityException(
                    'Memory usage exceeds limit ({$memoryLimit}).'
                );
            }

            $ns = $options['namespace'];
            if (isset($this->data[$ns][$key])) {
                throw new Exception\RuntimeException("Key '{$key}' already exists within namespace '$ns'");
            }
            $this->data[$ns][$key] = array($value, microtime(true), $options['tags']);

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Add multiple items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers addItems.pre(PreEvent)
     * @triggers addItems.post(PostEvent)
     * @triggers addItems.exception(ExceptionEvent)
     */
    public function addItems(array $keyValuePairs, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'keyValuePairs' => & $keyValuePairs,
            'options'       => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            if (!$this->hasFreeCapacity()) {
                $memoryLimit = $baseOptions->getMemoryLimit();
                throw new Exception\OutOfCapacityException(
                    'Memory usage exceeds limit ({$memoryLimit}).'
                );
            }

            $ns = $options['namespace'];
            if (!isset($this->data[$ns])) {
                $this->data[$ns] = array();
            }

            $data = & $this->data[$ns];
            foreach ($keyValuePairs as $key => $value) {
                if (isset($data[$key])) {
                    throw new Exception\RuntimeException("Key '{$key}' already exists within namespace '$ns'");
                }
                $data[$key] = array($value, microtime(true), $options['tags']);
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Replace an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception
     *
     * @triggers replaceItem.pre(PreEvent)
     * @triggers replaceItem.post(PostEvent)
     * @triggers replaceItem.exception(ExceptionEvent)
     */
    public function replaceItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'value'   => & $value,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $ns = $options['namespace'];
            if (!isset($this->data[$ns][$key])) {
                throw new Exception\ItemNotFoundException("Key '{$key}' doen't exists within namespace '$ns'");
            }
            $this->data[$ns][$key] = array($value, microtime(true), $options['tags']);

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Replace multiple items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers replaceItems.pre(PreEvent)
     * @triggers replaceItems.post(PostEvent)
     * @triggers replaceItems.exception(ExceptionEvent)
     */
    public function replaceItems(array $keyValuePairs, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'keyValuePairs' => & $keyValuePairs,
            'options'       => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $ns = $options['namespace'];
            if (!isset($this->data[$ns])) {
                throw new Exception\ItemNotFoundException("Namespace '$ns' doesn't exist");
            }

            $data = & $this->data[$ns];
            foreach ($keyValuePairs as $key => $value) {
                if (!isset($data[$key])) {
                    throw new Exception\ItemNotFoundException(
                        "Key '{$key}' doen't exists within namespace '$ns'"
                    );
                }
                $data[$key] = array($value, microtime(true), $options['tags']);
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Reset lifetime of an item
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string $key
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers touchItem.pre(PreEvent)
     * @triggers touchItem.post(PostEvent)
     * @triggers touchItem.exception(ExceptionEvent)
     */
    public function touchItem($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $ns = $options['namespace'];
            if (isset($this->data[$ns][$key])) {
                // update mtime
                $this->data[$ns][$key][1] = microtime(true);
            } else {
                if (!$options['ignore_missing_items']) {
                    throw new Exception\ItemNotFoundException(
                        "Key '{$key}' not found within namespace '{$ns}'"
                    );
                }

                // add an empty item
                $this->data[$ns][$key] = array('', microtime(true), null);
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Remove an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $key
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers removeItem.pre(PreEvent)
     * @triggers removeItem.post(PostEvent)
     * @triggers removeItem.exception(ExceptionEvent)
     */
    public function removeItem($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $ns = $options['namespace'];
            if (isset($this->data[$ns][$key])) {
                unset($this->data[$ns][$key]);

                // remove empty namespace
                if (!$this->data[$ns]) {
                    unset($this->data[$ns]);
                }

            } else {
                if (!$options['ignore_missing_items']) {
                    throw new Exception\ItemNotFoundException("Key '{$key}' not found on namespace '{$ns}'");
                }
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Remove multiple items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  array $keys
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers removeItems.pre(PreEvent)
     * @triggers removeItems.post(PostEvent)
     * @triggers removeItems.exception(ExceptionEvent)
     */
    public function removeItems(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'keys'    => & $keys,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $ns = $options['namespace'];
            if ($options['ignore_missing_items'] === false) {
                if (!isset($this->data[$ns])) {
                    throw new Exception\ItemNotFoundException("Namespace '{$ns}' is empty");
                }

                $data = &$this->data[$ns];

                $missingItems = null;
                foreach ($keys as $key) {
                    if (isset($data[$key])) {
                        unset($data[$key]);
                    } else {
                        $missingItems[] = $key;
                    }
                }

                if ($missingItems) {
                    throw new Exception\ItemNotFoundException(
                        "Keys '" . implode("','", $missingItems) . "' not found on namespace '{$ns}'"
                    );
                }
            } elseif (isset($this->data[$ns])) {
                $data = & $this->data[$ns];
                foreach ($keys as $key) {
                    unset($data[$key]);
                }

                // remove empty namespace
                if (!$data) {
                    unset($this->data[$ns]);
                }
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Increment an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $key
     * @param  int $value
     * @param  array $options
     * @return int|boolean The new value of false on failure
     * @throws Exception
     *
     * @triggers incrementItem.pre(PreEvent)
     * @triggers incrementItem.post(PostEvent)
     * @triggers incrementItem.exception(ExceptionEvent)
     */
    public function incrementItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $value = (int) $value;
        $args  = new ArrayObject(array(
            'key'     => & $key,
            'value'   => & $value,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $ns   = $options['namespace'];
            $data = & $this->data[$ns];
            if (isset($data[$key])) {
                $data[$key][0]+= $value;
                $data[$key][1] = microtime(true);
                $result = $data[$key][0];
            } else {
                if (!$options['ignore_missing_items']) {
                    throw new Exception\ItemNotFoundException(
                        "Key '{$key}' not found within namespace '{$ns}'"
                    );
                }

                // add a new item
                $data[$key] = array($value, microtime(true), null);
                $result = $value;
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Decrement an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $key
     * @param  int $value
     * @param  array $options
     * @return int|boolean The new value or false or failure
     * @throws Exception
     *
     * @triggers decrementItem.pre(PreEvent)
     * @triggers decrementItem.post(PostEvent)
     * @triggers decrementItem.exception(ExceptionEvent)
     */
    public function decrementItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $value = (int) $value;
        $args  = new ArrayObject(array(
            'key'     => & $key,
            'value'   => & $value,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $ns   = $options['namespace'];
            $data = & $this->data[$ns];
            if (isset($data[$key])) {
                $data[$key][0]-= $value;
                $data[$key][1] = microtime(true);
                $result = $data[$key][0];
            } else {
                if (!$options['ignore_missing_items']) {
                    throw new Exception\ItemNotFoundException(
                        "Key '{$key}' not found within namespace '{$ns}'"
                    );
                }

                // add a new item
                $data[$key] = array(-$value, microtime(true), null);
                $result = -$value;
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* find */

    /**
     * Find items.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - Tags to search for used with matching modes of
     *      Zend\Cache\Storage\Adapter::MATCH_TAGS_*
     *
     * @param  int $mode Matching mode (Value of Zend\Cache\Storage\Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws Exception
     * @see fetch()
     * @see fetchAll()
     *
     * @triggers find.pre(PreEvent)
     * @triggers find.post(PostEvent)
     * @triggers find.exception(ExceptionEvent)
     */
    public function find($mode = self::MATCH_ACTIVE, array $options=array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return false;
        }

        if ($this->stmtActive) {
            throw new Exception\RuntimeException('Statement already in use');
        }

        $this->normalizeOptions($options);
        $this->normalizeMatchingMode($mode, self::MATCH_ACTIVE, $options);
        $args = new ArrayObject(array(
            'mode'    => & $mode,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $tags      = & $options['tags'];
            $emptyTags = $keys = array();
            foreach ($this->data[ $options['namespace'] ] as $key => &$item) {

                // compare expired / active
                if (($mode & self::MATCH_ALL) != self::MATCH_ALL) {

                    // if MATCH_EXPIRED -> filter active items
                    if (($mode & self::MATCH_EXPIRED) == self::MATCH_EXPIRED) {
                        if ($this->internalHasItem($key, $options)) {
                            continue;
                        }

                    // if MATCH_ACTIVE -> filter expired items
                    } else {
                        if (!$this->internalHasItem($key, $options)) {
                            continue;
                        }
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

                $keys[] = $key;
            }

            // don't check expiry on fetch
            $options['ttl'] = 0;

            $this->stmtKeys    = $keys;
            $this->stmtOptions = $options;
            $this->stmtActive  = true;

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Fetches the next item from result set
     *
     * @return array|boolean The next item or false
     * @throws Exception
     * @see fetchAll()
     *
     * @triggers fetch.pre(PreEvent)
     * @triggers fetch.post(PostEvent)
     * @triggers fetch.exception(ExceptionEvent)
     */
    public function fetch()
    {
        if (!$this->stmtActive) {
            return false;
        }

        try {
            $args    = new ArrayObject();
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
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
                $ref = & $this->data[ $options['namespace'] ][$key];
                break;
            } while (true);

            // get item data
            if ($key) {
                $item = array();
                foreach ($options['select'] as $select) {
                    if ($select == 'key') {
                        $item['key'] = $key;
                    } elseif ($select == 'value') {
                        $item['value'] = $ref[0];
                    } elseif ($select == 'mtime') {
                        $item['mtime'] = $ref[1];
                    } elseif ($select == 'tags') {
                        $item['tags'] = $ref[2];
                    } else {
                        $item[$select] = null;
                    }
                }

                $result = $item;

            } else {
                // free statement after last item
                $this->stmtActive  = false;
                $this->stmtKeys    = null;
                $this->stmtOptions = null;

                $result = false;
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* cleaning */

    /**
     * Clear items off all namespaces.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - tags <array> optional
     *    - Tags to search for used with matching modes of
     *      Zend\Cache\Storage\Adapter::MATCH_TAGS_*
     *
     * @param  int $mode Matching mode (Value of Zend\Cache\Storage\Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws Exception
     * @see clearByNamespace()
     *
     * @triggers clear.pre(PreEvent)
     * @triggers clear.post(PostEvent)
     * @triggers clear.exception(ExceptionEvent)
     */
    public function clear($mode = self::MATCH_EXPIRED, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeMatchingMode($mode, self::MATCH_EXPIRED, $options);
        $args = new ArrayObject(array(
            'mode'    => & $mode,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            if (!$options['tags'] && ($mode & self::MATCH_ALL) == self::MATCH_ALL) {
                $this->data = array();
            } else {
                foreach ($this->data as &$data) {
                    $this->clearNamespacedDataArray($data, $mode, $options);
                }
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Clear items by namespace.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - Tags to search for used with matching modes of
     *      Zend\Cache\Storage\Adapter::MATCH_TAGS_*
     *
     * @param  int $mode Matching mode (Value of Zend\Cache\Storage\Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     * @see clear()
     *
     * @triggers clearByNamespace.pre(PreEvent)
     * @triggers clearByNamespace.post(PostEvent)
     * @triggers clearByNamespace.exception(ExceptionEvent)
     */
    public function clearByNamespace($mode = self::MATCH_EXPIRED, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeMatchingMode($mode, self::MATCH_EXPIRED, $options);
        $args = new ArrayObject(array(
            'mode'    => & $mode,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            if (isset($this->data[ $options['namespace'] ])) {
                if (!$options['tags'] && ($mode & self::MATCH_ALL) == self::MATCH_ALL) {
                    unset($this->data[ $options['namespace'] ]);
                } else {
                    $this->clearNamespacedDataArray($this->data[ $options['namespace'] ], $mode, $options);
                }
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* status */

    /**
     * Get capabilities
     *
     * @return Capabilities
     *
     * @triggers getCapabilities.pre(PreEvent)
     * @triggers getCapabilities.post(PostEvent)
     * @triggers getCapabilities.exception(ExceptionEvent)
     */
    public function getCapabilities()
    {
        $args = new ArrayObject();

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        if ($this->capabilities === null) {
            $this->capabilityMarker = new stdClass();
                $this->capabilities = new Capabilities(
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

        return $this->triggerPost(__FUNCTION__, $args, $this->capabilities);
    }

    /**
     * Get storage capacity.
     *
     * @param  array $options
     * @return array|boolean Capacity as array or false on failure
     * @throws Exception
     *
     * @triggers getCapacity.pre(PreEvent)
     * @triggers getCapacity.post(PostEvent)
     * @triggers getCapacity.exception(ExceptionEvent)
     */
    public function getCapacity(array $options = array())
    {
        $args = new ArrayObject(array(
            'options' => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $total = $this->getOptions()->getMemoryLimit();
        $free  = $total - (float) memory_get_usage(true);
        $result = array(
            'total' => $total,
            'free'  => ($free >= 0) ? $free : 0,
        );

        return $this->triggerPost(__FUNCTION__, $args, $result);
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
