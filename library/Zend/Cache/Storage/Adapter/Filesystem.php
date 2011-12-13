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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use ArrayObject,
    GlobIterator,
    stdClass,
    Zend\Cache\Exception,
    Zend\Cache\Storage,
    Zend\Cache\Storage\Capabilities,
    Zend\Cache\Utils;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Filesystem extends AbstractAdapter
{
    /**
     * GlobIterator used as statement
     *
     * @var GlobIterator|null
     */
    protected $stmtGlob = null;

    /**
     * Matching mode of active statement
     *
     * @var integer|null
     */
    protected $stmtMatch = null;

    /**
     * Buffer vars
     *
     * @var string|null
     */
    protected $lastInfoId = null;

    /**
     * @var array|bool|null
     */
    protected $lastInfoAll = null;

    /**
     * @var array|null
     */
    protected $lastInfo = null;

    /* configuration */

    /**
     * Set options.
     *
     * @param  array|Traversable|FilesystemOptions $options
     * @return FilesystemAdapter
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!is_array($options) 
            && !$options instanceof Traversable 
            && !$options instanceof FilesystemOptions
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array, a Traversable object, or an FilesystemOptions instance; '
                . 'received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }
        $this->options = $options;
        $options->setTarget($this);
        return $this;
    }

    /**
     * Get options.
     *
     * @return AdapterOptions
     * @see setOptions()
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new FilesystemOptions());
        }
        return $this->options;
    }

    /* reading */

    /**
     * Get item
     *
     * @param  $key
     * @param  array $options
     * @return bool|mixed
     */
    public function getItem($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
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

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            $result = $this->internalGetItem($key, $options);
            if (array_key_exists('token', $options)) {
                // use filemtime + filesize as CAS token
                $keyInfo = $this->getKeyInfo($key, $options['namespace']);
                $options['token'] = $keyInfo['mtime'] . filesize($keyInfo['filespec'] . '.dat');
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Get items
     *
     * @param  array $keys
     * @param  array $options
     * @return array|mixed
     */
    public function getItems(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return array();
        }

        $this->normalizeOptions($options);
        // don't throw ItemNotFoundException on getItems
        $options['ignore_missing_items'] = true;

        $args = new ArrayObject(array(
            'keys'    => & $keys,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            $result = array();
            foreach ($keys as $key) {
                if ( ($rs = $this->internalGetItem($key, $options)) !== false) {
                    $result[$key] = $rs;
                }
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Check for an item
     *
     * @param  $key
     * @param  array $options
     * @return bool|mixed
     */
    public function hasItem($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
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

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            $result = $this->internalHasItem($key, $options);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Check for items
     *
     * @param  array $keys
     * @param  array $options
     * @return array|mixed
     */
    public function hasItems(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return array();
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

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            $result = array();
            foreach ($keys as $key) {
                if ( $this->internalHasItem($key, $options) === true ) {
                    $result[] = $key;
                }
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Get metadata
     *
     * @param $key
     * @param array $options
     * @return array|bool|mixed|null
     */
    public function getMetadata($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
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

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            $lastInfoId = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;
            if ($this->lastInfoId == $lastInfoId && $this->lastInfoAll) {
                return $this->lastInfoAll;
            }

            $this->lastInfoAll = $result = $this->internalGetMetadata($key, $options);

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Get metadatas
     *
     * @param array $keys
     * @param array $options
     * @return array|mixed
     */
    public function getMetadatas(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return array();
        }

        $this->normalizeOptions($options);
        // don't throw ItemNotFoundException on getMetadatas
        $options['ignore_missing_items'] = true;

        $args = new ArrayObject(array(
            'keys'    => & $keys,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            $result = array();
            foreach ($keys as $key) {
                $meta = $this->internalGetMetadata($key, $options);
                if ($meta !== false ) {
                    $result[$key] = $meta;
                }
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* writing */

    /**
     * Set item
     *
     * @param $key
     * @param $value
     * @param array $options
     * @return bool|mixed
     */
    public function setItem($key, $value, array $options = array())
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

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            $result = $this->internalSetItem($key, $value, $options);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Set items
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return bool|mixed
     */
    public function setItems(array $keyValuePairs, array $options = array())
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

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            $result = true;
            foreach ($keyValuePairs as $key => $value) {
                $result = $this->internalSetItem($key, $value, $options) && $result;
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Replace an item
     *
     * @param $key
     * @param $value
     * @param array $options
     * @return bool|mixed
     * @throws ItemNotFoundException
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

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            if ( !$this->internalHasItem($key, $options) ) {
                throw new Exception\ItemNotFoundException("Key '{$key}' doesn't exist");
            }

            $result = $this->internalSetItem($key, $value, $options);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Replace items
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return bool|mixed
     * @throws ItemNotFoundException
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

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            $result = true;
            foreach ($keyValuePairs as $key => $value) {
                if ( !$this->internalHasItem($key, $options) ) {
                    throw new Exception\ItemNotFoundException("Key '{$key}' doesn't exist");
                }
                $result = $this->internalSetItem($key, $value, $options) && $result;
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Add an item
     *
     * @param $key
     * @param $value
     * @param array $options
     * @return bool|mixed
     * @throws RuntimeException
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

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            if ( $this->internalHasItem($key, $options) ) {
                throw new Exception\RuntimeException("Key '{$key}' already exist");
            }

            $result = $this->internalSetItem($key, $value, $options);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Add items
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return bool|mixed
     * @throws RuntimeException
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

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            $result = true;
            foreach ($keyValuePairs as $key => $value) {
                if ( $this->internalHasItem($key, $options) ) {
                    throw new Exception\RuntimeException("Key '{$key}' already exist");
                }

                $result = $this->internalSetItem($key, $value, $options) && $result;
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * check and set item
     *
     * @param $token
     * @param $key
     * @param $value
     * @param array $options
     * @return bool|mixed
     * @throws ItemNotFoundException
     */
    public function checkAndSetItem($token, $key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'token'   => & $token,
            'key'     => & $key,
            'value'   => & $value,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            if ( !($keyInfo = $this->getKeyInfo($key, $options['namespace'])) ) {
                if ($options['ignore_missing_items']) {
                    $result = false;
                } else {
                    throw new Exception\ItemNotFoundException("Key '{$key}' not found within namespace '{$options['namespace']}'");
                }
            } else {
                // use filemtime + filesize as CAS token
                $check = $keyInfo['mtime'] . filesize($keyInfo['filespec'] . '.dat');
                if ($token != $check) {
                    $result = false;
                } else {
                    $result = $this->internalSetItem($key, $value, $options);
                }
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Touch an item
     *
     * @param $key
     * @param array $options
     * @return bool|mixed
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

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            $this->internalTouchItem($key, $options);
            $this->lastInfoId = null;

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Touch items
     *
     * @param array $keys
     * @param array $options
     * @return bool|mixed
     */
    public function touchItems(array $keys, array $options = array())
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

            foreach ($keys as $key) {
                $this->internalTouchItem($key, $options);
            }
            $this->lastInfoId = null;

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Remove an item
     *
     * @param $key
     * @param array $options
     * @return bool|mixed
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

            // unlink is not affected by clearstatcache
            $this->internalRemoveItem($key, $options);

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Remove items
     *
     * @param array $keys
     * @param array $options
     * @return bool|mixed
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

            // unlink is not affected by clearstatcache
            foreach ($keys as $key) {
                $this->internalRemoveItem($key, $options);
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* non-blocking */

    /**
     * Find
     *
     * @param int $mode
     * @param array $options
     * @return bool|mixed
     * @throws RuntimeException
     */
    public function find($mode = self::MATCH_ACTIVE, array $options = array())
    {
        if ($this->stmtActive) {
            throw new Exception\RuntimeException('Statement already in use');
        }

        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $this->normalizeMatchingMode($mode, self::MATCH_ACTIVE, $options);
        $options = array_merge($baseOptions->toArray(), $options);
        $args = new ArrayObject(array(
            'mode'    => & $mode,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            if ($baseOptions->getClearStatCache()) {
                clearstatcache();
            }

            try {
                $prefix = $options['namespace'] . $baseOptions->getNamespaceSeparator();
                $find = $options['cache_dir']
                    . str_repeat(\DIRECTORY_SEPARATOR . $prefix . '*', $options['dir_level'])
                    . \DIRECTORY_SEPARATOR . $prefix . '*.dat';
                $glob = new GlobIterator($find);

                $this->stmtActive  = true;
                $this->stmtGlob    = $glob;
                $this->stmtMatch   = $mode;
                $this->stmtOptions = $options;
            } catch (\Exception $e) {
                throw new Exception\RuntimeException('Instantiating glob iterator failed', 0, $e);
            }

            $result = true;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Fetch
     *
     * @return bool|mixed
     */
    public function fetch()
    {
        if (!$this->stmtActive) {
            return false;
        }

        $args = new ArrayObject();

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            if ($this->stmtGlob !== null) {
                $result = $this->fetchByGlob();

                if ($result === false) {
                    // clear statement
                    $this->stmtActive  = false;
                    $this->stmtGlob    = null;
                    $this->stmtMatch   = null;
                    $this->stmtOptions = null;
                }
            } else {
                $result = parent::fetch();
            }

            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* cleaning */

    /**
     * Clear
     *
     * @param int $mode
     * @param array $options
     * @return mixed
     */
    public function clear($mode = self::MATCH_EXPIRED, array $options = array())
    {
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

            $result = $this->clearByPrefix('', $mode, $options);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Clear by namespace
     *
     * @param int $mode
     * @param array $options
     * @return mixed
     */
    public function clearByNamespace($mode = self::MATCH_EXPIRED, array $options = array())
    {
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

            $prefix = $options['namespace'] . $this->getOptions()->getNamespaceSeparator();
            $result = $this->clearByPrefix($prefix, $mode, $options);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Optimize
     *
     * @param array $options
     * @return bool|mixed
     */
    public function optimize(array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            if ( ($dirLevel = $this->getDirLevel()) ) {
                // removes only empty directories
                $this->rmDir($this->getCacheDir(), $options['namespace'] . $baseOptions->getNamespaceSeparator());
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
     * @return mixed
     */
    public function getCapabilities()
    {
        $args = new ArrayObject();

        try {
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
                            'NULL'     => 'string',
                            'boolean'  => 'string',
                            'integer'  => 'string',
                            'double'   => 'string',
                            'string'   => true,
                            'array'    => false,
                            'object'   => false,
                            'resource' => false,
                        ),
                        'supportedMetadata'  => array('mtime', 'filespec'),
                        'maxTtl'             => 0,
                        'staticTtl'          => false,
                        'ttlPrecision'       => 1,
                        'expiredRead'        => true,
                        'maxKeyLength'       => 251, // 255 - strlen(.dat | .ifo)
                        'namespaceIsPrefix'  => true,
                        'namespaceSeparator' => $this->getOptions()->getNamespaceSeparator(),
                        'iterable'           => true,
                        'clearAllNamespaces' => true,
                        'clearByNamespace'   => true,
                    )
                );

                // set dynamic capibilities
                $this->updateCapabilities();
            }

            $result = $this->capabilities;
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /**
     * Get capacity
     *
     * @param array $options
     * @return mixed
     */
    public function getCapacity(array $options = array())
    {
        $args = new ArrayObject();

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = Utils::getDiskCapacity($this->getCacheDir());
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $e);
        }
    }

    /* internal */

    /**
     * Set key value pair
     *
     * @param $key
     * @param $value
     * @param array $options
     * @return bool
     * @throws RuntimeException
     */
    protected function internalSetItem($key, $value, array &$options)
    {
        $baseOptions = $this->getOptions();
        $oldUmask = null;

        $lastInfoId = $options['namespace'] . $baseOptions->getNamespaceSeparator() . $key;
        if ($this->lastInfoId == $lastInfoId) {
            $filespec = $this->lastInfo['filespec'];
            // if lastKeyInfo is available I'm sure that the cache directory exist
        } else {
            $filespec = $this->getFileSpec($key, $options['namespace']);
            if ($this->getDirLevel() > 0) {
                $path = dirname($filespec);
                if (!file_exists($path)) {
                    $oldUmask = umask($baseOptions->getDirUmask());
                    if ( !@mkdir($path, 0777, true) ) {
                        // reset umask on exception
                        umask($oldUmask);

                        // throw exception with last error message
                        $lastErr = error_get_last();
                        throw new Exception\RuntimeException($lastErr['message']);
                    }
                }
            }
        }

        $info = null;
        if ($baseOptions->getReadControl()) {
            $info['hash'] = Utils::generateHash($this->getReadControlAlgo(), $data, true);
            $info['algo'] = $baseOptions->getReadControlAlgo();
        }

        if (isset($options['tags']) && $options['tags']) {
            $info['tags'] = array_values(array_unique($options['tags']));
        }

        try {
            if ($oldUmask !== null) { // $oldUmask could be defined on set directory_umask
                umask($baseOptions->getFileUmask());
            } else {
                $oldUmask = umask($baseOptions->getFileUmask());
            }

            $ret = $this->putFileContent($filespec . '.dat', $value);
            if ($ret && $info) {
                // Don't throw exception if writing of info file failed
                // -> only return false
                try {
                    $ret = $this->putFileContent($filespec . '.ifo', serialize($info));
                } catch (\Exception $e) {
                    $ret = false;
                }
            }

            $this->lastInfoId = null;

            // reset file_umask
            umask($oldUmask);

            return $ret;

        } catch (\Exception $e) {
            // reset umask on exception
            umask($oldUmask);
            throw $e;
        }
    }

    /**
     * Remove a key
     *
     * @param $key
     * @param array $options
     * @throws ItemNotFoundException
     */
    protected function internalRemoveItem($key, array &$options)
    {
        $filespec = $this->getFileSpec($key, $options['namespace']);

        if (!$options['ignore_missing_items'] && !file_exists($filespec . '.dat')) {
            throw new Exception\ItemNotFoundException("Key '{$key}' with file '{$filespec}.dat' not found");
        }

        $this->unlink($filespec . '.dat');
        $this->unlink($filespec . '.ifo');
        $this->lastInfoId = null;
    }

    /**
     * Get by key
     *
     * @param $key
     * @param array $options
     * @return bool|string
     * @throws \Exception|ItemNotFoundException|UnexpectedValueException
     */
    protected function internalGetItem($key, array &$options)
    {
        if ( !$this->internalHasItem($key, $options)
            || !($keyInfo = $this->getKeyInfo($key, $options['namespace']))
        ) {
            if ($options['ignore_missing_items']) {
                return false;
            } else {
                throw new Exception\ItemNotFoundException(
                    "Key '{$key}' not found within namespace '{$options['namespace']}'"
                );
            }
        }

        $baseOptions = $this->getOptions();
        try {
            $data = $this->getFileContent($keyInfo['filespec'] . '.dat');

            if ($baseOptions->getReadControl()) {
                if ( ($info = $this->readInfoFile($keyInfo['filespec'] . '.ifo'))
                    && isset($info['hash'], $info['algo'])
                ) {
                    $hashData = Utils::generateHash($info['algo'], $data, true);
                    if ($hashData != $info['hash']) {
                        throw new Exception\UnexpectedValueException(
                            'ReadControl: Stored hash and computed hash don\'t match'
                        );
                    }
                }
            }

            return $data;

        } catch (\Exception $e) {
            try {
                // remove cache file on exception
                $this->internalRemoveItem($key, $options);
            } catch (\Exception $tmp) {} // do not throw remove exception on this point

            throw $e;
        }
    }

    /**
     * Checks for a key
     *
     * @param $key
     * @param array $options
     * @return bool
     */
    protected function internalHasItem($key, array &$options)
    {
        $keyInfo = $this->getKeyInfo($key, $options['namespace']);
        if (!$keyInfo) {
            return false; // missing or corrupted cache data
        }

        if ( !$options['ttl']      // infinite lifetime
          || time() < ($keyInfo['mtime'] + $options['ttl'])  // not expired
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get info by key
     *
     * @param $key
     * @param array $options
     * @return array|bool
     * @throws ItemNotFoundException
     */
    protected function internalGetMetadata($key, array &$options) 
    {
        $keyInfo = $this->getKeyInfo($key, $options['namespace']);
        if (!$keyInfo) {
            if ($options['ignore_missing_items']) {
                return false;
            } else {
                throw new Exception\ItemNotFoundException("Key '{$key}' not found within namespace '{$options['namespace']}'");
            }
        }

        if ( ($info = $this->readInfoFile($keyInfo['filespec'] . '.ifo')) ) {
            return $keyInfo + $info;
        }

        return $keyInfo;
    }

    /**
     * Touch a key
     *
     * @param $key
     * @param array $options
     * @return bool
     * @throws ItemNotFoundException|RuntimeException
     */
    protected function internalTouchItem($key, array &$options)
    {
        $keyInfo = $this->getKeyInfo($key, $options['namespace']);
        if (!$keyInfo) {
            if ($options['ignore_missing_items']) {
                return false;
            } else {
                throw new Exception\ItemNotFoundException("Key '{$key}' not found within namespace '{$options['namespace']}'");
            }
        }

        if ( !@touch($keyInfo['filespec'] . '.dat') ) {
            $err = error_get_last();
            throw new Exception\RuntimeException($err['message']);
        }
    }

    /**
     * Fetch by glob
     *
     * @return array|bool
     */
    protected function fetchByGlob()
    {
        $options = $this->stmtOptions;
        $mode    = $this->stmtMatch;

        $prefix  = $options['namespace'] . $this->getOptions()->getNamespaceSeparator();
        $prefixL = strlen($prefix);

        do {
            try {
                $valid = $this->stmtGlob->valid();
            } catch (\LogicException $e) {
                // @link https://bugs.php.net/bug.php?id=55701
                // GlobIterator throws LogicException with message
                // 'The parent constructor was not called: the object is in an invalid state'
                $valid = false;
            }
            if (!$valid) {
                return false;
            }

            $item = array();
            $meta = null;

            $current = $this->stmtGlob->current();
            $this->stmtGlob->next();

            $filename = $current->getFilename();
            if ($prefix !== '') {
                if (substr($filename, 0, $prefixL) != $prefix) {
                    continue;
                }

                // remove prefix and suffix (.dat)
                $key = substr($filename, $prefixL, -4);
            } else {
                // remove suffix (.dat)
                $key = substr($filename, 0, -4);
            }

            // if MATCH_ALL mode do not check expired
            if (($mode & self::MATCH_ALL) != self::MATCH_ALL) {
                $mtime = $current->getMTime();

                // if MATCH_EXPIRED -> filter not expired items
                if (($mode & self::MATCH_EXPIRED) == self::MATCH_EXPIRED) {
                    if ( time() < ($mtime + $options['ttl']) ) {
                        continue;
                    }

                // if MATCH_ACTIVE -> filter expired items
                } else {
                    if ( time() >= ($mtime + $options['ttl']) ) {
                        continue;
                    }
                }
            }

            // check tags only if one of the tag matching mode is selected
            if (($mode & 070) > 0) {

                $meta = $this->internalGetMetadata($key, $options);

                // if MATCH_TAGS mode -> check if all given tags available in current cache
                if (($mode & self::MATCH_TAGS) == self::MATCH_TAGS ) {
                    if (!isset($meta['tags']) || count(array_diff($opts['tags'], $meta['tags'])) > 0) {
                        continue;
                    }

                // if MATCH_NO_TAGS mode -> check if no given tag available in current cache
                } elseif( ($mode & self::MATCH_NO_TAGS) == self::MATCH_NO_TAGS ) {
                    if (isset($meta['tags']) && count(array_diff($opts['tags'], $meta['tags'])) != count($opts['tags'])) {
                        continue;
                    }

                // if MATCH_ANY_TAGS mode -> check if any given tag available in current cache
                } elseif ( ($mode & self::MATCH_ANY_TAGS) == self::MATCH_ANY_TAGS ) {
                    if (!isset($meta['tags']) || count(array_diff($opts['tags'], $meta['tags'])) == count($opts['tags'])) {
                        continue;
                    }

                }
            }

            foreach ($options['select'] as $select) {
                if ($select == 'key') {
                    $item['key'] = $key;
                } else if ($select == 'value') {
                    $item['value'] = $this->getFileContent($current->getPathname());
                } else if ($select != 'key') {
                    if ($meta === null) {
                        $meta = $this->internalGetMetadata($key, $options);
                    }
                    $item[$select] = isset($meta[$select]) ? $meta[$select] : null;
                }
            }

            return $item;
        } while (true);
    }

    /**
     * Clear by prefix
     *
     * @param $prefix
     * @param $mode
     * @param array $opts
     * @return bool
     * @throws RuntimeException
     */
    protected function clearByPrefix($prefix, $mode, array &$opts)
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $ttl = $opts['ttl'];

        if ($baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        try {
            $find = $baseOptions->getCacheDir()
                . str_repeat(\DIRECTORY_SEPARATOR . $prefix . '*', $baseOptions->getDirLevel())
                . \DIRECTORY_SEPARATOR . $prefix . '*.dat';
            $glob = new GlobIterator($find);
        } catch (\Exception $e) {
            throw new Exception\RuntimeException('Instantiating GlobIterator failed', 0, $e);
        }

        $time = time();

        foreach ($glob as $entry) {

            // if MATCH_ALL mode do not check expired
            if (($mode & self::MATCH_ALL) != self::MATCH_ALL) {

                $mtime = $entry->getMTime();
                if (($mode & self::MATCH_EXPIRED) == self::MATCH_EXPIRED) {
                    if ( $time <= ($mtime + $ttl) ) {
                        continue;
                    }

                // if Zend_Cache::MATCH_ACTIVE mode selected do not remove expired data
                } else {
                    if ( $time >= ($mtime + $ttl) ) {
                        continue;
                    }
                }
            }

            // remove file suffix (*.dat)
            $pathnameSpec = substr($entry->getPathname(), 0, -4);

            ////////////////////////////////////////
            // on this time all expire tests match
            ////////////////////////////////////////

            // check tags only if one of the tag matching mode is selected
            if (($mode & 070) > 0) {

                $info = $this->readInfoFile($filespec . '.ifo');

                // if MATCH_TAGS mode -> check if all given tags available in current cache
                if (($mode & self::MATCH_TAGS) == self::MATCH_TAGS ) {
                    if (!isset($info['tags']) 
                        || count(array_diff($opts['tags'], $info['tags'])) > 0
                    ) {
                        continue;
                    }

                // if MATCH_NO_TAGS mode -> check if no given tag available in current cache
                } elseif(($mode & self::MATCH_NO_TAGS) == self::MATCH_NO_TAGS) {
                    if (isset($info['tags']) 
                        && count(array_diff($opts['tags'], $info['tags'])) != count($opts['tags'])
                    ) {
                        continue;
                    }

                // if MATCH_ANY_TAGS mode -> check if any given tag available in current cache
                } elseif ( ($mode & self::MATCH_ANY_TAGS) == self::MATCH_ANY_TAGS ) {
                    if (!isset($info['tags']) 
                        || count(array_diff($opts['tags'], $info['tags'])) == count($opts['tags'])
                    ) {
                        continue;
                    }
                }
            }

            ////////////////////////////////////////
            // on this time all tests match
            ////////////////////////////////////////

            $this->unlink($pathnameSpec . '.dat'); // delete data file
            $this->unlink($pathnameSpec . '.ifo'); // delete info file
        }

        return true;
    }

    /**
     * Removes directories recursive by namespace
     *
     * @param  string $dir    Directory to delete
     * @param  string $prefix Namespace + Separator
     * @return bool
     */
    protected function rmDir($dir, $prefix)
    {
        $glob = glob(
            $dir . \DIRECTORY_SEPARATOR . $prefix  . '*',
            \GLOB_ONLYDIR | \GLOB_NOESCAPE | \GLOB_NOSORT
        );
        if (!$glob) {
            // On some systems glob returns false even on empty result
            return true;
        }

        $ret = true;
        foreach ($glob as $subdir) {
            // ignore not empty directories
            // skip removing current directory if removing of sub-directory failed
            $ret = $this->rmDir($subdir, $prefix) && @rmdir($subdir);
        }
        return $ret;
    }

    /**
     * Get an array of information about the cache key.
     * NOTE: returns false if cache doesn't hit.
     *
     * @param  string $key
     * @param  string $ns
     * @return array|boolean
     */
    protected function getKeyInfo($key, $ns)
    {
        $lastInfoId = $ns . $this->getOptions()->getNamespaceSeparator() . $key;
        if ($this->lastInfoId == $lastInfoId) {
            return $this->lastInfo;
        }

        $filespec = $this->getFileSpec($key, $ns);

        if ( ($filemtime = @filemtime($filespec . '.dat')) === false ) {
            return false;
        }

        $this->lastInfoId  = $lastInfoId;
        $this->lastInfoAll = null;
        $this->lastInfo    = array(
            'filespec' => $filespec,
            'mtime'    => $filemtime,
        );

        if (!$this->getNoCtime()) {
            $this->lastInfo['ctime'] = filectime($filespec . '.dat');
        }

        if (!$this->getNoAtime()) {
            $this->lastInfo['atime'] = fileatime($filespec . '.dat');
        }

        return $this->lastInfo;
    }

    /**
     * Get file spec of the given key and namespace
     *
     * @param  string $key
     * @param  string $ns
     * @return string
     */
    protected function getFileSpec($key, $ns)
    {
        $options    = $this->getOptions();
        $prefix     = $ns . $options->getNamespaceSeparator();
        $lastInfoId = $prefix . $key;
        if ($this->lastInfoId == $lastInfoId) {
            return $this->lastInfo['filespec'];
        }

        $path  = $options->getCacheDir();
        $level = $options->getDirLevel();
        if ( $level > 0 ) {
            // create up to 256 directories per directory level
            $hash = md5($key);
            for ($i = 0, $max = ($level * 2); $i < $max; $i+= 2) {
                $path .= \DIRECTORY_SEPARATOR . $prefix . $hash[$i] . $hash[$i+1];
            }
        }

        return $path . \DIRECTORY_SEPARATOR . $prefix . $key;
    }

    /**
     * Read info file
     *
     * @param string $file
     * @return array|boolean The info array or false if file wasn't found
     * @throws Exception\RuntimeException
     */
    protected function readInfoFile($file) 
    {
        if (!file_exists($file)) {
            return false;
        }

        $info = @unserialize($this->getFileContent($file));
        if (!is_array($info)) {
           $err = error_get_last();
           throw new Exception\RuntimeException("Corrupted info file '{$file}': {$err['message']}");
        }

        return $info;
    }

    /**
     * Read a complete file
     *
     * @param  string $file File complete path
     * @return string
     * @throws Exception\RuntimeException
     */
    protected function getFileContent($file)
    {
        // if file locking enabled -> file_get_contents can't be used
        if ($this->getOptions()->getFileLocking()) {
            $fp = @fopen($file, 'rb');
            if ($fp === false) {
                $lastErr = error_get_last();
                throw new Exception\RuntimeException($lastErr['message']);
            }

            if (!flock($fp, \LOCK_SH)) {
                $lastErr = error_get_last();
                throw new Exception\RuntimeException($lastErr['message']);
            }

            $result = @stream_get_contents($fp);
            if ($result === false) {
                $lastErr = error_get_last();
                @flock($fp, \LOCK_UN);
                @fclose($fp);
                throw new Exception\RuntimeException($lastErr['message']);
            }

            flock($fp, \LOCK_UN);
            fclose($fp);

        // if file locking disabled -> file_get_contents can be used
        } else {
            $result = @file_get_contents($file, false);
            if ($result === false) {
                $lastErr = error_get_last();
                throw new Exception\RuntimeException($lastErr['message']);
            }
        }

        return $result;
    }

    /**
     * Write content to a file
     *
     * @param  string $file  File complete path
     * @param  string $data  Data to write
     * @return bool
     * @throws Exception\RuntimeException
     */
    protected function putFileContent($file, $data)
    {
        $options  = $this->getOptions();
        $locking  = $options->getFileLocking();
        $blocking = $locking ? $options->getFileBlocking() : false;

        if ($locking && !$blocking) {
            $fp = @fopen($file, 'cb');
            if (!$fp) {
                $lastErr = error_get_last();
                throw new Exception\RuntimeException($lastErr['message']);
            }

            if(!flock($fp, \LOCK_EX | \LOCK_NB)) {
                // file is locked by another process -> abort writing
                fclose($fp);
                return false;
            }

            if (!ftruncate($fp, 0)) {
                $lastErr = error_get_last();
                throw new Exception\RuntimeException($lastErr['message']);
            }

            if (!fwrite($fp, $data)) {
                 $lastErr = error_get_last();
                throw new Exception\RuntimeException($lastErr['message']);
            }

            flock($fp, \LOCK_UN);
            fclose($fp);
        } else {
            $flags = 0;
            if ($locking) {
                $flags = $flags | \LOCK_EX;
            }

            if ( @file_put_contents($file, $data, $flags) === false ) {
                $lastErr = error_get_last();
                throw new Exception\RuntimeException($lastErr['message']);
            }
        }

        return true;
    }

    /**
     * Unlink a file
     *
     * @param string $file
     * @return void
     * @throw RuntimeException
     */
    protected function unlink($file) 
    {
        if (!@unlink($file)) {
            // only throw exception if file still exists after deleting
            if (file_exists($file)) {
                $lastErr = error_get_last();
                throw new Exception\RuntimeException($lastErr['message']);
            }
        }
    }

    /**
     * Update dynamic capabilities only if already created
     *
     * @return void
     */
    protected function updateCapabilities()
    {
        if ($this->capabilities) {
            $options = $this->getOptions();

            // update namespace separator
            $this->capabilities->setNamespaceSeparator(
                $this->capabilityMarker,
                $options->getNamespaceSeparator()
            );

            // update metadata capabilities
            $metadata = array('mtime', 'filespec');

            if (!$options->getNoCtime()) {
                $metadata[] = 'ctime';
            }

            if (!$options->getNoAtime()) {
                $metadata[] = 'atime';
            }

            $this->capabilities->setSupportedMetadata(
                $this->capabilityMarker,
                $metadata
            );
        }
    }
}
