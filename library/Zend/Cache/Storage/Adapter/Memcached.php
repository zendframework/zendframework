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
    Memcached as MemcachedResource,
    MemcachedException,
    stdClass,
    Traversable,
    Zend\Cache\Exception,
    Zend\Cache\Storage\Event,
    Zend\Cache\Storage\CallbackEvent,
    Zend\Cache\Storage\Capabilities;

/**
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Storage
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @todo       Implement the find() method
 */
class Memcached extends AbstractAdapter
{
    /**
     * Major version of ext/memcached
     *
     * @var null|int
     */
    protected static $extMemcachedMajorVersion;

    /**
     * Memcached instance
     *
     * @var MemcachedResource
     */
    protected $memcached;

    /**
     * Constructor
     *
     * @param  null|array|Traversable|MemcachedOptions $options
     * @throws Exception
     * @return void
     */
    public function __construct($options = null)
    {
        if (static::$extMemcachedMajorVersion === null) {
            $v = (string) phpversion('memcached');
            static::$extMemcachedMajorVersion = ($v !== '') ? (int)$v[0] : 0;
        }

        if (static::$extMemcachedMajorVersion < 1) {
            throw new Exception\ExtensionNotLoadedException('Need ext/memcached version >= 1.0.0');
        }

        $this->memcached = new MemcachedResource();

        parent::__construct($options);

        // It's ok to add server as soon as possible because
        // ext/memcached auto-connects to the server on first use
        $options = $this->getOptions();

        $servers = $options->getServers();
        if (!$servers) {
            $options->addServer('127.0.0.1', 11211);
            $servers = $options->getServers();
        }
        $this->memcached->addServers($servers);
    }

    /* options */

    /**
     * Set options.
     *
     * @param  array|Traversable|MemcachedOptions $options
     * @return Memcached
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!$options instanceof MemcachedOptions) {
            $options = new MemcachedOptions($options);
        }

        $this->options = $options;

        // Set memcached options, using options map to map to Memcached constants
        $map = $options->getOptionsMap();
        foreach ($options->toArray() as $key => $value) {
            if (!array_key_exists($key, $map)) {
                // skip keys for which there are not equivalent options
                continue;
            }
            $this->memcached->setOption($map[$key], $value);
        }

        return $this;
    }

    /**
     * Get options.
     *
     * @return MemcachedOptions
     * @see setOptions()
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new MemcachedOptions());
        }
        return $this->options;
    }

    /* reading */

    /**
     * Internal method to get an item.
     *
     * Options:
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
        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);

        if (array_key_exists('token', $normalizedOptions)) {
            $result = $this->memcached->get($normalizedKey, null, $normalizedOptions['token']);
        } else {
            $result = $this->memcached->get($normalizedKey);
        }

        if ($result === false) {
            if (($rsCode = $this->memcached->getResultCode()) != 0
                && ($rsCode != MemcachedResource::RES_NOTFOUND || !$normalizedOptions['ignore_missing_items'])
            ) {
                throw $this->getExceptionByResultCode($rsCode);
            }
        }

        return $result;
    }

    /**
     * Internal method to get multiple items.
     *
     * Options:
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
        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);

        $result = $this->memcached->getMulti($normalizedKeys);
        if ($result === false) {
            throw $this->getExceptionByResultCode($this->memcached->getResultCode());
        }

        return $result;
    }

    /**
     * Internal method to test if an item exists.
     *
     * Options:
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
        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);

        $value = $this->memcached->get($normalizedKey);
        if ($value === false) {
            $rsCode = $this->memcached->getResultCode();
            if ($rsCode == MemcachedResource::RES_SUCCESS) {
                return true;
            } elseif ($rsCode == MemcachedResource::RES_NOTFOUND) {
                return false;
            } else {
                throw $this->getExceptionByResultCode($rsCode);
            }
        }

        return true;
    }

    /**
     * Internal method to test multiple items.
     *
     * Options:
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
        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);

        $result = $this->memcached->getMulti($normalizedKeys);
        if ($result === false) {
            throw $this->getExceptionByResultCode($this->memcached->getResultCode());
        }

        foreach ($result as $key => & $value) {
            $value = true;
        }

        return $result;
    }

    /**
     * Get metadata of multiple items
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use
     *
     * @param  array $normalizedKeys
     * @param  array $normalizedOptions
     * @return array
     * @throws Exception
     *
     * @triggers getMetadatas.pre(PreEvent)
     * @triggers getMetadatas.post(PostEvent)
     * @triggers getMetadatas.exception(ExceptionEvent)
     */
    protected function internalGetMetadatas(array & $normalizedKeys, array & $normalizedOptions)
    {
        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);

        $result = $this->memcached->getMulti($normalizedKeys);
        if ($result === false) {
            throw $this->getExceptionByResultCode($this->memcached->getResultCode());
        }

        foreach ($result as $key => & $value) {
            $value = array();
        }

        return $result;
    }

    /* writing */

    /**
     * Internal method to store an item.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
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
        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);

        $expiration = $this->expirationTime($normalizedOptions['ttl']);
        if (!$this->memcached->set($normalizedKey, $value, $expiration)) {
            throw $this->getExceptionByResultCode($this->memcached->getResultCode());
        }

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
        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);

        $expiration = $this->expirationTime($normalizedOptions['ttl']);
        if (!$this->memcached->setMulti($normalizedKeyValuePairs, $expiration)) {
            throw $this->getExceptionByResultCode($this->memcached->getResultCode());
        }

        return true;
    }

    /**
     * Add an item.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-live
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception
     */
    protected function internalAddItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);

        $expiration = $this->expirationTime($normalizedOptions['ttl']);
        if (!$this->memcached->add($normalizedKey, $value, $expiration)) {
            throw $this->getExceptionByResultCode($this->memcached->getResultCode());
        }

        return true;
    }

    /**
     * Internal method to replace an existing item.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception
     */
    protected function internalReplaceItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);

        $expiration = $this->expirationTime($normalizedOptions['ttl']);
        if (!$this->memcached->replace($normalizedKey, $value, $expiration)) {
            throw $this->getExceptionByResultCode($this->memcached->getResultCode());
        }

        return true;
    }

    /**
     * Internal method to set an item only if token matches
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *  - tags <array>
     *    - An array of tags
     *
     * @param  mixed  $token
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception
     * @see    getItem()
     * @see    setItem()
     */
    protected function internalCheckAndSetItem(& $token, & $normalizedKey, & $value, array & $normalizedOptions)
    {
        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);

        $expiration = $this->expirationTime($normalizedOptions['ttl']);
        $result     = $this->memcached->cas($token, $normalizedKey, $value, $expiration);

        if ($result === false) {
            $rsCode = $this->memcached->getResultCode();
            if ($rsCode !== 0 && $rsCode != MemcachedResource::RES_DATA_EXISTS) {
                throw $this->getExceptionByResultCode($rsCode);
            }
        }


        return $result;
    }

    /**
     * Internal method to remove an item.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *  - ignore_missing_items <boolean>
     *    - Throw exception on missing item
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception
     */
    protected function internalRemoveItem(& $normalizedKey, array & $normalizedOptions)
    {
        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);
        $result = $this->memcached->delete($normalizedKey);

        if ($result === false) {
            $rsCode = $this->memcached->getResultCode();
            if ($rsCode != 0 && ($rsCode != MemcachedResource::RES_NOTFOUND || !$normalizedOptions['ignore_missing_items'])) {
                throw $this->getExceptionByResultCode($rsCode);
            }
        }

        return true;
    }

    /**
     * Internal method to remove multiple items.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *  - ignore_missing_items <boolean>
     *    - Throw exception on missing item
     *
     * @param  array $keys
     * @param  array $options
     * @return boolean
     * @throws Exception
     */
    protected function internalRemoveItems(array & $normalizedKeys, array & $normalizedOptions)
    {
        // support for removing multiple items at once has been added in ext/memcached 2
        if (static::$extMemcachedMajorVersion < 2) {
            return parent::internalRemoveItems($normalizedKeys, $normalizedOptions);
        }

        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);
        $rsCodes = $this->memcached->deleteMulti($normalizedKeys);

        $missingKeys = null;
        foreach ($rsCodes as $key => $rsCode) {
            if ($rsCode !== true && $rsCode != 0) {
                if ($rsCode != MemcachedResource::RES_NOTFOUND) {
                    throw $this->getExceptionByResultCode($rsCode);
                }
                $missingKeys[] = $key;
            }
        }

        if ($missingKeys && !$normalizedOptions['ignore_missing_items']) {
            throw new Exception\ItemNotFoundException(
                "Keys '" . implode("','", $missingKeys) . "' not found within namespace '{$normalizedOptions['namespace']}'"
            );
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
     *  - ignore_missing_items <boolean>
     *    - Throw exception on missing item
     *
     * @param  string $normalizedKey
     * @param  int    $value
     * @param  array  $normalizedOptions
     * @return int|boolean The new value or false on failure
     * @throws Exception
     */
    protected function internalIncrementItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);

        $value    = (int)$value;
        $newValue = $this->memcached->increment($normalizedKey, $value);

        if ($newValue === false) {
            $rsCode = $this->memcached->getResultCode();
            if ($rsCode != 0 && ($rsCode != MemcachedResource::RES_NOTFOUND || !$normalizedOptions['ignore_missing_items'])) {
                throw $this->getExceptionByResultCode($rsCode);
            }

            $newValue   = $value;
            $expiration = $this->expirationTime($normalizedOptions['ttl']);
            if (!$this->memcached->add($normalizedKey, $newValue, $expiration)) {
                throw $this->getExceptionByResultCode($this->memcached->getResultCode());
            }
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
     *  - ignore_missing_items <boolean>
     *    - Throw exception on missing item
     *
     * @param  string $normalizedKey
     * @param  int    $value
     * @param  array  $normalizedOptions
     * @return int|boolean The new value or false on failure
     * @throws Exception
     */
    protected function internalDecrementItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);

        $value    = (int)$value;
        $newValue = $this->memcached->decrement($normalizedKey, $value);

        if ($newValue === false) {
            $rsCode = $this->memcached->getResultCode();
            if ($rsCode != 0 && ($rsCode != MemcachedResource::RES_NOTFOUND || !$normalizedOptions['ignore_missing_items'])) {
                throw $this->getExceptionByResultCode($rsCode);
            }

            $newValue   = -$value;
            $expiration = $this->expirationTime($normalizedOptions['ttl']);
            if (!$this->memcached->add($normalizedKey, $newValue, $expiration)) {
                throw $this->getExceptionByResultCode($this->memcached->getResultCode());
            }
        }

        return $newValue;
    }

    /* non-blocking */

    /**
     * Internal method to request multiple items.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-live
     *  - namespace <string>
     *    - The namespace to use
     *  - select <array>
     *    - An array of the information the returned item contains
     *  - callback <callback> optional
     *    - An result callback will be invoked for each item in the result set.
     *    - The first argument will be the item array.
     *    - The callback does not have to return anything.
     *
     * @param  array $normalizedKeys
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception
     * @see    fetch()
     * @see    fetchAll()
     */
    protected function internalGetDelayed(array & $normalizedKeys, array & $normalizedOptions)
    {
        if ($this->stmtActive) {
            throw new Exception\RuntimeException('Statement already in use');
        }

        if (isset($normalizedOptions['callback']) && !is_callable($normalizedOptions['callback'], false)) {
            throw new Exception\InvalidArgumentException('Invalid callback');
        }

        $this->memcached->setOption(MemcachedResource::OPT_PREFIX_KEY, $normalizedOptions['namespace']);

        // redirect callback
        if (isset($normalizedOptions['callback'])) {
            $cb = function (MemcachedResource $memc, array & $item) use (& $normalizedOptions) {
                $select = & $normalizedOptions['select'];

                // handle selected key
                if (!in_array('key', $select)) {
                    unset($item['key']);
                }

                // handle selected value
                if (!in_array('value', $select)) {
                    unset($item['value']);
                }

                call_user_func($normalizedOptions['callback'], $item);
            };

            if (!$this->memcached->getDelayed($normalizedKeys, false, $cb)) {
                throw $this->getExceptionByResultCode($this->memcached->getResultCode());
            }
        } else {
            if (!$this->memcached->getDelayed($normalizedKeys)) {
                throw $this->getExceptionByResultCode($this->memcached->getResultCode());
            }

            $this->stmtActive  = true;
            $this->stmtOptions = & $normalizedOptions;
        }

        return true;
    }

    /**
     * Internal method to fetch the next item from result set
     *
     * @return array|boolean The next item or false
     * @throws Exception
     */
    protected function internalFetch()
    {
        if (!$this->stmtActive) {
            return false;
        }

        $result = $this->memcached->fetch();
        if (!empty($result)) {
            $select = & $this->stmtOptions['select'];

            // handle selected key
            if (!in_array('key', $select)) {
                unset($result['key']);
            }

            // handle selected value
            if (!in_array('value', $select)) {
                unset($result['value']);
            }

        } else {
            // clear stmt
            $this->stmtActive  = false;
            $this->stmtOptions = null;
        }

        return $result;
    }

    /**
     * Internal method to return all items of result set.
     *
     * @return array The result set as array containing all items
     * @throws Exception
     * @see    fetch()
     */
    protected function internalFetchAll()
    {
        $result = $this->memcached->fetchAll();
        if ($result === false) {
            throw new Exception\RuntimeException("Memcached::fetchAll() failed");
        }

        $select = $this->stmtOptions['select'];
        foreach ($result as & $elem) {
            if (!in_array('key', $select)) {
                unset($elem['key']);
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
     * @throws Exception
     * @see    clearByNamespace()
     */
    protected function internalClear(& $normalizedMode, array & $normalizedOptions)
    {
        if (!$this->memcached->flush()) {
            throw $this->getExceptionByResultCode($this->memcached->getResultCode());
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
            $this->capabilities     = new Capabilities(
                $this->capabilityMarker,
                array(
                    'supportedDatatypes' => array(
                        'NULL'     => true,
                        'boolean'  => true,
                        'integer'  => true,
                        'double'   => true,
                        'string'   => true,
                        'array'    => true,
                        'object'   => 'object',
                        'resource' => false,
                    ),
                    'supportedMetadata'  => array(),
                    'maxTtl'             => 0,
                    'staticTtl'          => true,
                    'tagging'            => false,
                    'ttlPrecision'       => 1,
                    'useRequestTime'     => false,
                    'expiredRead'        => false,
                    'maxKeyLength'       => 255,
                    'namespaceIsPrefix'  => true,
                    'iterable'           => false,
                    'clearAllNamespaces' => true,
                    'clearByNamespace'   => false,
                )
            );
        }

        return $this->capabilities;
    }

    /**
     * Internal method to get storage capacity.
     *
     * @param  array $normalizedOptions
     * @return array|boolean Capacity as array or false on failure
     * @throws Exception
     */
    protected function internalGetCapacity(array & $normalizedOptions)
    {
        $stats = $this->memcached->getStats();
        if ($stats === false) {
            throw $this->getExceptionByResultCode($this->memcached->getResultCode());
        }

        $mem = array_pop($stats);
        return array(
            'free'  => $mem['limit_maxbytes'] - $mem['bytes'],
            'total' => $mem['limit_maxbytes'],
        );
    }

    /* internal */

    /**
     * Get expiration time by ttl
     *
     * Some storage commands involve sending an expiration value (relative to
     * an item or to an operation requested by the client) to the server. In
     * all such cases, the actual value sent may either be Unix time (number of
     * seconds since January 1, 1970, as an integer), or a number of seconds
     * starting from current time. In the latter case, this number of seconds
     * may not exceed 60*60*24*30 (number of seconds in 30 days); if the
     * expiration value is larger than that, the server will consider it to be
     * real Unix time value rather than an offset from current time.
     *
     * @param int $ttl
     * @return int
     */
    protected function expirationTime($ttl)
    {
        if ($ttl > 2592000) {
            return time() + $ttl;
        }
        return $ttl;
    }

    /**
     * Generate exception based of memcached result code
     *
     * @param int $code
     * @return Exception\RuntimeException|Exception\ItemNotFoundException
     * @throws Exception\InvalidArgumentException On success code
     */
    protected function getExceptionByResultCode($code)
    {
        switch ($code) {
            case MemcachedResource::RES_SUCCESS:
                throw new Exception\InvalidArgumentException(
                    "The result code '{$code}' (SUCCESS) isn't an error"
                );

            case MemcachedResource::RES_NOTFOUND:
            case MemcachedResource::RES_NOTSTORED:
                return new Exception\ItemNotFoundException($this->memcached->getResultMessage());

            default:
                return new Exception\RuntimeException($this->memcached->getResultMessage());
        }
    }
}
