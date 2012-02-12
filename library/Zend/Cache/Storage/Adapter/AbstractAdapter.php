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
    SplObjectStorage,
    stdClass,
    Traversable,
    Zend\Cache\Exception,
    Zend\Cache\Storage\Adapter,
    Zend\Cache\Storage\Capabilities,
    Zend\Cache\Storage\Event,
    Zend\Cache\Storage\ExceptionEvent,
    Zend\Cache\Storage\Plugin,
    Zend\Cache\Storage\PostEvent,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAdapter implements Adapter
{
    /**
     * The used EventManager if any
     *
     * @var null|EventManager
     */
    protected $events = null;

    /**
     * The plugin registry
     *
     * @var SplObjectStorage Registered plugins
     */
    protected $pluginRegistry;

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
     * @var mixed
     */
    protected $options;

    /**
     * Is a statement active
     *
     * @var bool
     */
    protected $stmtActive = false;

    /**
     * List of keys used for the active statement
     *
     * @var null|array
     */
    protected $stmtKeys = null;

    /**
     * Options used on starting the active statement
     *
     * @var null|array
     */
    protected $stmtOptions = null;

    /**
     * Constructor
     *
     * @param  null|array|Traversable|AdapterOptions $options
     * @throws Exception
     * @return void
     */
    public function __construct($options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }
    }

    /**
     * Destructor
     *
     * detach all registered plugins to free
     * event handles of event manager
     *
     *
     * @return void
     */
    public function __destruct()
    {
        foreach ($this->getPlugins() as $plugin) {
            $this->removePlugin($plugin);
        }
    }

    /* configuration */

    /**
     * Set options.
     *
     * @param  array|Traversable|AdapterOptions $options
     * @return AbstractAdapter
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!$options instanceof AdapterOptions) {
            $options = new AdapterOptions($options);
        }

        $this->options = $options;
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
            $this->setOptions(new AdapterOptions());
        }
        return $this->options;
    }

    /**
     * Enable/Disable caching.
     *
     * Alias of setWritable and setReadable.
     *
     * @see    setWritable()
     * @see    setReadable()
     * @param  boolean $flag
     * @return AbstractAdapter
     */
    public function setCaching($flag)
    {
        $flag    = (bool) $flag;
        $options = $this->getOptions();
        $options->setWritable($flag);
        $options->setReadable($flag);
        return $this;
    }

    /**
     * Get caching enabled.
     *
     * Alias of getWritable and getReadable.
     *
     * @see    getWritable()
     * @see    getReadable()
     * @return boolean
     */
    public function getCaching()
    {
        $options = $this->getOptions();
        return ($options->getWritable() && $options->getReadable());
    }

    /* Event/Plugin handling */

    /**
     * Set event manager instance
     *
     * @param  EventCollection $events
     * @return AbstractAdapter
     */
    public function setEventManager(EventCollection $events)
    {
        $this->events = $events;
        return $this;
    }

    /**
     * Get the event manager
     *
     * @return EventManager
     */
    public function events()
    {
        if ($this->events === null) {
            $this->setEventManager(new EventManager(array(
                __CLASS__,
                get_called_class(),
            )));
        }
        return $this->events;
    }

    /**
     * Trigger an pre event and return the event response collection
     *
     * @param  string $eventName
     * @param  ArrayObject $args
     * @return \Zend\EventManager\ResponseCollection All handler return values
     */
    protected function triggerPre($eventName, ArrayObject $args)
    {
        return $this->events()->trigger(new Event($eventName . '.pre', $this, $args));
    }

    /**
     * Triggers the PostEvent and return the result value.
     *
     * @param  string $eventName
     * @param  ArrayObject $args
     * @param  mixed $result
     * @return mixed
     */
    protected function triggerPost($eventName, ArrayObject $args, &$result)
    {
        $postEvent = new PostEvent($eventName . '.post', $this, $args, $result);
        $eventRs   = $this->events()->trigger($postEvent);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        return $postEvent->getResult();
    }

    /**
     * Trigger an exception event
     *
     * If the ExceptionEvent has the flag "throwException" enabled throw the
     * exception after trigger else return the result.
     *
     * @param  string $eventName
     * @param  ArrayObject $args
     * @param  \Exception $exception
     * @throws Exception
     * @return mixed
     */
    protected function triggerException($eventName, ArrayObject $args, \Exception $exception)
    {
        $exceptionEvent = new ExceptionEvent($eventName . '.exception', $this, $args, $exception);
        $eventRs        = $this->events()->trigger($exceptionEvent);

        if ($exceptionEvent->getThrowException()) {
            throw $exceptionEvent->getException();
        }

        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        return $exceptionEvent->getResult();
    }

    /**
     * Check if a plugin is registered
     *
     * @param  Plugin $plugin
     * @return boolean
     */
    public function hasPlugin(Plugin $plugin)
    {
        $registry = $this->getPluginRegistry();
        return $registry->contains($plugin);
    }

    /**
     * Register a plugin
     *
     * @param  Plugin $plugin
     * @return AbstractAdapter Fluent interface
     * @throws Exception\LogicException
     */
    public function addPlugin(Plugin $plugin)
    {
        $registry = $this->getPluginRegistry();
        if ($registry->contains($plugin)) {
            throw new Exception\LogicException(sprintf(
                'Plugin of type "%s" already registered',
                get_class($plugin)
            ));
        }

        $plugin->attach($this->events());
        $registry->attach($plugin);

        return $this;
    }

    /**
     * Unregister an already registered plugin
     *
     * @param  Plugin $plugin
     * @return AbstractAdapter Fluent interface
     * @throws Exception\LogicException
     */
    public function removePlugin(Plugin $plugin)
    {
        $registry = $this->getPluginRegistry();
        if ($registry->contains($plugin)) {
            $plugin->detach($this->events());
            $registry->detach($plugin);
        }
        return $this;
    }

    /**
     * Get all registered plugins
     *
     * @return SplObjectStorage
     */
    public function getPlugins()
    {
        return $this->getPluginRegistry();
    }

    /* reading */

    /**
     * Get items
     *
     * @param  array $keys
     * @param  array $options
     * @return array
     */
    public function getItems(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getReadable()) {
            return array();
        }

        $ret = array();
        foreach ($keys as $key) {
            try {
                $value = $this->getItem($key, $options);
                if ($value !== false) {
                    $ret[$key] = $value;
                }
            } catch (Exception\ItemNotFoundException $e) {
                // ignore missing items
            }
        }

        return $ret;
    }

    /**
     * Checks if adapter has an item
     *
     * @param  string $key
     * @param  array $options
     * @return bool
     */
    public function hasItem($key, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return false;
        }

        try {
            $ret = ($this->getItem($key, $options) !== false);
        } catch (Exception\ItemNotFoundException $e) {
            $ret = false;
        }

        return $ret;
    }

    /**
     * Checks if adapter has items
     *
     * @param  array $keys
     * @param  array $options
     * @return array
     */
    public function hasItems(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return array();
        }

        $ret = array();
        foreach ($keys as $key) {
            if ($this->hasItem($key, $options)) {
                $ret[] = $key;
            }
        }

        return $ret;
    }

    /**
     * Get Metadatas
     *
     * @param  array $keys
     * @param  array $options
     * @return array
     */
    public function getMetadatas(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return array();
        }

        $ret = array();
        foreach ($keys as $key) {
            try {
                $meta = $this->getMetadata($key, $options);
                if ($meta !== false) {
                    $ret[$key] = $meta;
                }
            } catch (Exception\ItemNotFoundException $e) {
                // ignore missing items
            }
        }

        return $ret;
    }

    /* writing */

    /**
     * Set items
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return bool
     */
    public function setItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->setItem($key, $value, $options) && $ret;
        }

        return $ret;
    }

    /**
     * Add an item
     *
     * @param  string|int $key
     * @param  mixed $value
     * @param  array $options
     * @return bool
     * @throws Exception\RuntimeException
     */
    public function addItem($key, $value, array $options = array())
    {
        if ($this->hasItem($key, $options)) {
            throw new Exception\RuntimeException("Key '{$key}' already exists");
        }
        return $this->setItem($key, $value, $options);
    }

    /**
     * Add items
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return bool
     */
    public function addItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->addItem($key, $value, $options) && $ret;
        }

        return $ret;
    }

    /**
     * Replace an item
     *
     * @param  string|int $key
     * @param  mixed $value
     * @param  array $options
     * @return bool
     * @throws Exception\ItemNotFoundException
     */
    public function replaceItem($key, $value, array $options = array())
    {
        if (!$this->hasItem($key, $options)) {
            throw new Exception\ItemNotFoundException("Key '{$key}' doen't exists");
        }
        return $this->setItem($key, $value, $options);
    }

    /**
     * Replace items
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return bool
     */
    public function replaceItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->replaceItem($key, $value, $options) && $ret;
        }

        return $ret;
    }

    /**
     * Check and set item
     *
     * @param  mixed  $token
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return bool
     */
    public function checkAndSetItem($token, $key, $value, array $options = array())
    {
        $oldValue = $this->getItem($key, $options);
        if ($oldValue != $token) {
            return false;
        }

        return $this->setItem($key, $value, $options);
    }

    /**
     * Touch an item
     *
     * @param  string|int $key
     * @param  array $options
     * @return bool
     */
    public function touchItem($key, array $options = array())
    {
        $classOptions = $this->getOptions();
        if (!$classOptions->getWritable() || !$classOptions->getReadable()) {
           return false;
       }

        // do not test validity on reading
        $optsNoValidate = array('ttl' => 0) + $options;

        $value = $this->getItem($key, $optsNoValidate);
        if ($value === false) {
            // add an empty item
            return $this->addItem($key, '', $options);
        } else {
            // rewrite item to update mtime/ttl
            if (!isset($options['tags'])) {
                $info = $this->getMetadata($key, $optsNoValidate);
                if (isset($info['tags'])) {
                    $options['tags'] = & $info['tags'];
                }
            }

            // rewrite item
            return $this->replaceItem($key, $value, $options);
        }
    }

    /**
     * Touch items
     *
     * @param  array $keys
     * @param  array $options
     * @return bool
     */
    public function touchItems(array $keys, array $options = array())
    {
        // Don't check readable because not all adapters needs to read the item before
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $ret = true;
        foreach ($keys as $key) {
            $ret = $this->touchItem($key, $options) && $ret;
        }
        return $ret;
    }

    /**
     * Remove items
     *
     * @param  array $keys
     * @param  array $options
     * @return bool
     */
    public function removeItems(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $ret = true;
        foreach ($keys as $key) {
            $ret = $this->removeItem($key, $options) && $ret;
        }

        return $ret;
    }

    /**
     * Increment an item
     *
     * @param  string|int $key
     * @param  int|float $value
     * @param  array $options
     * @return bool|int
     */
    public function incrementItem($key, $value, array $options = array())
    {
        $classOptions = $this->getOptions();
        if (!$classOptions->getWritable() || !$classOptions->getReadable()) {
            return false;
        }

        $value = (int) $value;
        $get   = (int) $this->getItem($key, $options);
        $this->setItem($key, $get + $value, $options);
        return $get + $value;
    }

    /**
     * Increment items
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return bool
     */
    public function incrementItems(array $keyValuePairs, array $options = array())
    {
        // Don't check readable because not all adapters needs read the value before
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->incrementItem($key, $value, $options) && $ret;
        }
        return $ret;
    }

    /**
     * Decrement an item
     *
     * @param  string|int $key
     * @param  int|float $value
     * @param  array $options
     * @return bool|int
     */
    public function decrementItem($key, $value, array $options = array())
    {
        $classOptions = $this->getOptions();
        if (!$classOptions->getWritable() || !$classOptions->getReadable()) {
            return false;
        }

        $value = (int) $value;
        $get   = (int) $this->getItem($key, $options);
        $this->setItem($key, $get - $value, $options);
        return $get - $value;
    }

    /**
     * Decrement items
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return bool
     */
    public function decrementItems(array $keyValuePairs, array $options = array())
    {
        // Don't check readable because not all adapters needs read the value before
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->decrementItem($key, $value, $options) && $ret;
        }
        return $ret;
    }

    /* non-blocking */

    /**
     * Get delayed
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-live (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - select <array> optional
     *    - An array of the information the returned item contains
     *      (Default: array('key', 'value'))
     *  - callback <callback> optional
     *    - An result callback will be invoked for each item in the result set.
     *    - The first argument will be the item array.
     *    - The callback does not have to return anything.
     *
     * @param  array $keys
     * @param  array $options
     * @return bool
     * @throws Exception\InvalidArgumentException|Exception\RuntimeException
     */
    public function getDelayed(array $keys, array $options = array())
    {
        if ($this->stmtActive) {
            throw new Exception\RuntimeException('Statement already in use');
        }

        if (!$this->getOptions()->getReadable()) {
            return false;
        } elseif (!$keys) {
            // empty statement
            return true;
        }

        $this->normalizeOptions($options);
        if (!isset($options['select'])) {
            $options['select'] = array('key', 'value');
        }

        $this->stmtOptions = array_merge($this->getOptions()->toArray(), $options);
        $this->stmtKeys    = $keys;
        $this->stmtActive  = true;

        if (isset($options['callback'])) {
            $callback = $options['callback'];
            if (!is_callable($callback, false)) {
                throw new Exception\InvalidArgumentException('Invalid callback');
            }

            while ( ($item = $this->fetch()) !== false) {
                call_user_func($callback, $item);
            }
        }

        return true;
    }

    /**
     * Find
     *
     * @param  int $mode
     * @param  array $options
     * @throws Exception\UnsupportedMethodCallException
     */
    public function find($mode = self::MATCH_ACTIVE, array $options = array())
    {
        throw new Exception\UnsupportedMethodCallException('find isn\'t supported by this adapter');
    }

    /**
     * Fetch
     *
     * @return array|bool
     */
    public function fetch()
    {
        if (!$this->stmtActive) {
            return false;
        }

        $options = $this->stmtOptions;

        do {
            $key = array_shift($this->stmtKeys);
            if ($key === null) {
                break;
            }

            $item  = array();
            $value = $info = $exist = null;
            foreach ($options['select'] as $select) {
                if ($select == 'key') {
                    $item['key'] = $key;
                } elseif ($select == 'value') {
                    $value = $this->getItem($key, $options);
                    if ($value === false) {
                        $exist = false;
                        break;
                    }
                    $exist = true;
                    $item['value'] = $value;
                } else {
                    if ($info === null) {
                        $info = $this->getMetadata($key, $options);
                        if ($info === false) {
                            $exist = false;
                            break;
                        }
                        $exist = true;
                    }
                    $item[$select] = isset($info[$select]) ? $info[$select] : null;
                }
            }

            // goto next if not exists
            if ($exist === false
                || ($exist === null && !$this->hasItem($key, $options))
            ) {
                continue;
            }

            return $item;
        } while (true);

        // clear statement
        $this->stmtActive  = false;
        $this->stmtKeys    = null;
        $this->stmtOptions = null;

        return false;
    }

    /**
     * Fetch all
     *
     * @return array
     */
    public function fetchAll()
    {
        $rs = array();
        while (($item = $this->fetch()) !== false) {
            $rs[] = $item;
        }
        return $rs;
    }

    /* cleaning */

    /**
     * Clear
     *
     * @param  int $mode
     * @param  array $options
     * @throws Exception\RuntimeException
     */
    public function clear($mode = self::MATCH_EXPIRED, array $options = array())
    {
        throw new Exception\RuntimeException(
            "This adapter doesn't support to clear items off all namespaces"
        );
    }

    /**
     * Clear by namespace
     *
     * @param  int $mode
     * @param  array $options
     * @throws Exception\RuntimeException
     */
    public function clearByNamespace($mode = self::MATCH_EXPIRED, array $options = array())
    {
        throw new Exception\RuntimeException(
            "This adapter doesn't support to clear items by namespace"
        );
    }

    /**
     * Optimize
     *
     * @param  array $options
     * @return bool
     */
    public function optimize(array $options = array())
    {
        return true;
    }

    /* status */

    /**
     * Get capabilities of this adapter
     *
     * @return Capabilities
     */
    public function getCapabilities()
    {
        if ($this->capabilities === null) {
            $this->capabilityMarker = new stdClass();
            $this->capabilities     = new Capabilities($this->capabilityMarker);
        }
        return $this->capabilities;
    }

    /* internal */

    /**
     * Validates and normalizes the $options argument
     *
     * @param array $options
     */
    protected function normalizeOptions(array &$options)
    {
        $baseOptions = $this->getOptions();

        // ttl
        if (isset($options['ttl'])) {
            $this->normalizeTtl($options['ttl']);
        } else {
            $options['ttl'] = $baseOptions->getTtl();
        }

        // namespace
        if (isset($options['namespace'])) {
            $this->normalizeNamespace($options['namespace']);
        } else {
            $options['namespace'] = $baseOptions->getNamespace();
        }

        // ignore_missing_items
        if (isset($options['ignore_missing_items'])) {
            $options['ignore_missing_items'] = (bool) $options['ignore_missing_items'];
        } else {
            $options['ignore_missing_items'] = $baseOptions->getIgnoreMissingItems();
        }

        // tags
        if (isset($options['tags'])) {
            $this->normalizeTags($options['tags']);
        } else {
            $options['tags'] = null;
        }

        // select
        if (isset($options['select'])) {
            $this->normalizeSelect($options['select']);
        } else {
            $options['select'] = array('key', 'value');
        }
    }

    /**
     * Validates and normalize a TTL.
     *
     * @param  int|float $ttl
     * @throws Exception\InvalidArgumentException
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

    /**
     * Validates and normalize a namespace.
     *
     * @param  string $namespace
     * @throws Exception\InvalidArgumentException
     */
    protected function normalizeNamespace(&$namespace)
    {
        $namespace = (string) $namespace;

        if ($namespace === '') {
            throw new Exception\InvalidArgumentException('Empty namespaces are not allowed');
        } elseif (($p = $this->getOptions()->getNamespacePattern()) && !preg_match($p, $namespace)) {
            throw new Exception\InvalidArgumentException(
                "The namespace '{$namespace}' doesn't match against pattern '{$p}'"
            );
        }
    }

    /**
     * Validates and normalize tags array
     *
     * @param  array $tags
     * @throws Exception\InvalidArgumentException
     */
    protected function normalizeTags(&$tags)
    {
        if (!is_array($tags)) {
            throw new Exception\InvalidArgumentException('Tags have to be an array');
        }

        foreach ($tags as &$tag) {
            $tag = (string) $tag;
            if ($tag === '') {
                throw new Exception\InvalidArgumentException('Empty tags are not allowed');
            }
        }

        $tags = array_values(array_unique($tags));
    }

    /**
     * Validates and normalize select array
     *
     * @param string[]|string
     */
    protected function normalizeSelect(&$select)
    {
        if (!is_array($select)) {
            $select = array((string) $select);
        } else {
            $select = array_unique($select);
        }
    }

    /**
     * Normalize the matching mode needed on (clear and find)
     *
     * @todo  normalize matching mode with given tags
     * @param int $mode    Matching mode to normalize
     * @param int $default Default matching mode
     */
    protected function normalizeMatchingMode(&$mode, $default, array &$normalizedOptions)
    {
        $mode = (int) $mode;
        if (($mode & self::MATCH_EXPIRED) != self::MATCH_EXPIRED
            && ($mode & self::MATCH_ACTIVE) != self::MATCH_ACTIVE
        ) {
            $mode = $mode | (int) $default;
        }
    }

    /**
     * Validates and normalizes a key
     *
     * @param  string $key
     * @return string
     * @throws Exception\InvalidArgumentException On an invalid key
     */
    protected function normalizeKey(&$key)
    {
        $key = (string) $key;

        if (($p = $this->getOptions()->getKeyPattern()) && !preg_match($p, $key)) {
            throw new Exception\InvalidArgumentException(
                "The key '{$key}' doesn't match agains pattern '{$p}'"
            );
        }
    }

    /**
     * Return registry of plugins
     *
     * @return SplObjectStorage
     */
    protected function getPluginRegistry()
    {
        if (!$this->pluginRegistry instanceof SplObjectStorage) {
            $this->pluginRegistry = new SplObjectStorage();
        }
        return $this->pluginRegistry;
    }
}
