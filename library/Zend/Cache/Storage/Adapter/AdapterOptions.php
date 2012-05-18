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
    Zend\Cache\Exception,
    Zend\Cache\Storage\Event,
    Zend\EventManager\EventsCapableInterface,
    Zend\Stdlib\Options;

/**
 * Unless otherwise marked, all options in this class affect all adapters.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AdapterOptions extends Options
{

    /**
     * The adapter using these options
     *
     * @var null|Filesystem
     */
    protected $adapter;

    /**
     * Validate key against pattern
     *
     * @var string
     */
    protected $keyPattern = '';

    /**
     * Namespace option
     *
     * @var string
     */
    protected $namespace = 'zfcache';

    /**
     * Validate namespace against pattern
     *
     * @var string
     */
    protected $namespacePattern = '';

    /**
     * Readable option
     *
     * @var boolean
     */
    protected $readable = true;

    /**
     * TTL option
     *
     * @var int|float 0 means infinite or maximum of adapter
     */
    protected $ttl = 0;

    /**
     * Writable option
     *
     * @var boolean
     */
    protected $writable = true;

    /**
     * Cast to array
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        $transform = function($letters) {
            $letter = array_shift($letters);
            return '_' . strtolower($letter);
        };
        foreach ($this as $key => $value) {
            $normalizedKey = preg_replace_callback('/([A-Z])/', $transform, $key);
            $array[$normalizedKey] = $value;
        }
        return $array;
    }

    /**
     * Adapter using this instance
     *
     * @param  AdapterInterface|null $adapter
     * @return AdapterOptions
     */
    public function setAdapter(AdapterInterface $adapter = null)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * Set key pattern
     *
     * @param  null|string $pattern
     * @return AdapterOptions
     */
    public function setKeyPattern($pattern)
    {
        $pattern = (string) $pattern;
        if ($this->keyPattern !== $pattern) {
            // validate pattern
            if ($pattern !== '') {
                if (@preg_match($pattern, '') === false) {
                    $err = error_get_last();
                    throw new Exception\InvalidArgumentException("Invalid pattern '{$pattern}': {$err['message']}");
                }
            }

            $this->triggerOptionEvent('key_pattern', $pattern);
            $this->keyPattern = $pattern;
        }

        return $this;
    }

    /**
     * Get key pattern
     *
     * @return string
     */
    public function getKeyPattern()
    {
        return $this->keyPattern;
    }

    /**
     * Set namespace.
     *
     * @param  string $namespace
     * @return AdapterOptions
     */
    public function setNamespace($namespace)
    {
        $namespace = (string)$namespace;
        if ($this->namespace !== $namespace) {
            $pattern = $this->getNamespacePattern();
            if ($pattern && !preg_match($pattern, $namespace)) {
                throw new Exception\InvalidArgumentException(
                    "The namespace '{$namespace}' doesn't match agains pattern '{$pattern}'"
                );
            }

            $this->triggerOptionEvent('namespace', $namespace);
            $this->namespace = $namespace;
        }

        return $this;
    }

    /**
     * Get namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Set namespace pattern
     *
     * @param  null|string $pattern
     * @return AdapterOptions
     */
    public function setNamespacePattern($pattern)
    {
        $pattern = (string) $pattern;
        if ($this->namespacePattern !== $pattern) {
            if ($pattern !== '') {
                // validate pattern
                if (@preg_match($pattern, '') === false) {
                    $err = error_get_last();
                    throw new Exception\InvalidArgumentException("Invalid pattern '{$pattern}': {$err['message']}");

                // validate current namespace
                } elseif (($ns = $this->getNamespace()) && !preg_match($pattern, $ns)) {
                    throw new Exception\RuntimeException(
                        "The current namespace '{$ns}' doesn't match agains pattern '{$pattern}'"
                        . " - please change the namespace first"
                    );
                }
            }

            $this->triggerOptionEvent('namespace_pattern', $pattern);
            $this->namespacePattern = $pattern;
        }

        return $this;
    }

    /**
     * Get namespace pattern
     *
     * @return string
     */
    public function getNamespacePattern()
    {
        return $this->namespacePattern;
    }

    /**
     * Enable/Disable reading data from cache.
     *
     * @param  boolean $flag
     * @return AbstractAdapter
     */
    public function setReadable($flag)
    {
        $flag = (bool) $flag;
        if ($this->readable !== $flag) {
            $this->triggerOptionEvent('readable', $flag);
            $this->readable = $flag;
        }
        return $this;
    }

    /**
     * If reading data from cache enabled.
     *
     * @return boolean
     */
    public function getReadable()
    {
        return $this->readable;
    }

    /**
     * Set time to live.
     *
     * @param  int|float $ttl
     * @return AdapterOptions
     */
    public function setTtl($ttl)
    {
        $this->normalizeTtl($ttl);
        if ($this->ttl !== $ttl) {
            $this->triggerOptionEvent('ttl', $ttl);
            $this->ttl = $ttl;
        }
        return $this;
    }

    /**
     * Get time to live.
     *
     * @return float
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * Enable/Disable writing data to cache.
     *
     * @param  boolean $flag
     * @return AdapterOptions
     */
    public function setWritable($flag)
    {
        $flag = (bool) $flag;
        if ($this->writable !== $flag) {
            $this->triggerOptionEvent('writable', $flag);
            $this->writable = $flag;
        }
        return $this;
    }

    /**
     * If writing data to cache enabled.
     *
     * @return boolean
     */
    public function getWritable()
    {
        return $this->writable;
    }

    /**
     * Triggers an option event if this options instance has a connection to
     * an adapter implements EventsCapableInterface.
     *
     * @param string $optionName
     * @param mixed  $optionValue
     * @return void
     */
    protected function triggerOptionEvent($optionName, $optionValue)
    {
        if ($this->adapter instanceof EventsCapableInterface) {
            $event = new Event('option', $this->adapter, new ArrayObject(array($optionName => $optionValue)));
            $this->adapter->events()->trigger($event);
        }
    }

    /**
     * Validates and normalize a TTL.
     *
     * @param  int|float $ttl
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    protected function normalizeTtl(&$ttl)
    {
        if (!is_int($ttl)) {
            $ttl = (float) $ttl;

            // convert to int if possible
            if ($ttl === (float) (int) $ttl) {
                $ttl = (int) $ttl;
            }
        }

        if ($ttl < 0) {
             throw new Exception\InvalidArgumentException("TTL can't be negative");
        }
    }
}
