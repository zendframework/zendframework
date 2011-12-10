<?php

namespace Zend\Cache\Storage\Adapter;
use Zend\Cache\Storage\Adapter,
    Zend\Cache\Storage\Plugin,
    Zend\Cache\Storage\Event,
    Zend\Cache\Storage\PostEvent,
    Zend\Cache\Storage\ExceptionEvent,
    Zend\Cache\Storage\Capabilities,
    Zend\Cache\Exception\RuntimeException,
    Zend\Cache\Exception\LogicException,
    Zend\Cache\Exception\InvalidArgumentException,
    Zend\Cache\Exception\UnsupportedMethodCallException,
    Zend\Cache\Exception\ItemNotFoundException,
    Zend\Cache\Exception\MissingKeyException,
    Zend\EventManager\EventManager;

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
     * @var array Array of registered plugins
     */
    protected $pluginRegistry = array();

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
     * Writable option
     *
     * @var boolean
     */
    protected $writable = true;

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
     * Validate key against pattern
     *
     * @var string
     */
    protected $keyPattern = '';

    /**
     * Ignore missing items
     *
     * @var boolean
     */
    protected $ignoreMissingItems = true;

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
     * @param array|Traversable $options
     * @see setOptions()
     * @return void
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
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
     * @param array|Traversable $options
     * @return AbstractAdapter
     * @see getOptions()
     */
    public function setOptions($options)
    {
        if (!($options instanceof Traversable) && !is_array($options)) {
            throw new InvalidArgumentException(
                'Options must be an array or an instance of Traversable'
            );
        }

        foreach ($options as $option => $value) {
            $method = 'set'
                    . str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($option))));
            $this->{$method}($value);
        }

        return $this;
    }

    /**
     * Get options.
     *
     * @return array
     * @see setOptions()
     */
    public function getOptions()
    {
        return array(
            'writable'             => $this->getWritable(),
            'readable'             => $this->getReadable(),
            'caching'              => $this->getCaching(),
            'ttl'                  => $this->getTtl(),
            'namespace'            => $this->getNamespace(),
            'namespace_pattern'    => $this->getNamespacePattern(),
            'key_pattern'          => $this->getKeyPattern(),
            'ignore_missing_items' => $this->getIgnoreMissingItems(),
        );
    }

    /**
     * Enable/Disable writing data to cache.
     *
     * @param boolean $flag
     * @return AbstractAdapter
     */
    public function setWritable($flag)
    {
        $this->writable = (bool)$flag;
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
     * Enable/Disable reading data from cache.
     *
     * @param boolean $flag
     * @return AbstractAdapter
     */
    public function setReadable($flag)
    {
        $this->readable = (bool)$flag;
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
     * Enable/Disable caching.
     * Alias of setWritable and setReadable.
     *
     * @see setWritable
     * @see setReadable
     * @param boolean $flag
     * @return AbstractAdapter
     */
    public function setCaching($flag)
    {
        $flag = (bool)$flag;
        $this->setWritable($flag);
        $this->setReadable($flag);
        return $this;
    }

    /**
     * Get caching enabled.
     * Alias of getWritable and getReadable.
     *
     * @see getWritable
     * @see getReadable
     * @return boolean
     */
    public function getCaching()
    {
        return ($this->getWritable() && $this->getReadable());
    }

    /**
     * Set time to life.
     *
     * @param int|float $ttl
     * @return AbstractAdapter
     */
    public function setTtl($ttl)
    {
        $this->normalizeTtl($ttl);
        $this->ttl = $ttl;
        return $this;
    }

    /**
     * Get time to life.
     *
     * @return float
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * Set namespace.
     *
     * @param string $namespace
     * @return AbstractAdapter
     */
    public function setNamespace($namespace)
    {
        $nameapace = (string)$namespace;
        if ($namespace === '') {
            throw new InvalidArgumentException('No namespace given');
        }

        if ( ($pattern = $this->getNamespacePattern())
          && !preg_match($pattern, $namespace)) {
            throw new InvalidArgumentException(
                "The namespace '{$namespace}' doesn't match agains pattern '{$pattern}'"
            );
        }
        $this->namespace = (string)$namespace;
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
     * @param null|string $pattern
     * @return AbstractAdapter
     */
    public function setNamespacePattern($pattern)
    {
        if (($pattern = (string)$pattern) === '') {
            $this->namespacePattern = '';
        } else {
            // validate pattern
            if (@preg_match($pattern, '') === false) {
                $err = error_get_last();
                throw new InvalidArgumentException("Invalid pattern '{$pattern}': {$err['message']}");

            // validate current namespace
            } elseif (($ns = $this->getNamespace()) && !preg_match($pattern, $ns)) {
                throw new RuntimeException(
                    "The current namespace '{$ns}' doesn't match agains pattern '{$pattern}'"
                  . " - please change the namespace first"
                );
            }

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
     * Set key pattern
     *
     * @param null|string $pattern
     * @return AbstractAdapter
     */
    public function setKeyPattern($pattern)
    {
        if (($pattern = (string)$pattern) === '') {
            $this->keyPattern = '';
        } else {
            // validate pattern
            if (@preg_match($pattern, '') === false) {
                $err = error_get_last();
                throw new InvalidArgumentException("Invalid pattern '{$pattern}': {$err['message']}");
            }

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
     * Enables or disables ignoring of missing items.
     *
     * - If enabled and a missing item was requested:
     *   - getItem, getMetadata: return false
     *   - removeItem[s]: return true
     *   - incrementItem[s], decrementItem[s]: add a new item with 0 as base
     *   - touchItem[s]: add new empty item
     *
     * - If disabled and a missing item was requested:
     *   - getItem, getMetadata, incrementItem[s], decrementItem[s], touchItem[s]
     *     throws ItemNotFoundException
     *
     * @param boolean $flag
     * @return Adapter
     */
    public function setIgnoreMissingItems($flag)
    {
        $this->ignoreMissingItems = (bool)$flag;
        return $this;
    }

    /**
     * Ignore missing items
     *
     * @return boolean
     * @see setIgnoreMissingItems() to get more information
     */
    public function getIgnoreMissingItems()
    {
        return $this->ignoreMissingItems;
    }

    /* Event/Plugin handling */

    /**
     * Get the event manager
     *
     * @return Zend\EventManager\EventManager
     */
    public function events()
    {
        if ($this->events === null) {
            $this->events = new EventManager(array(__CLASS__, get_class($this)));
        }
        return $this->events;
    }

    /**
     * Trigger an pre event and return the event response collection
     *
     * @param string $eventName
     * @param ArrayObject $args
     * @return Zend\EventManager\ResponseCollection All handler return values
     */
    protected function triggerPre($eventName, \ArrayObject $args)
    {
        return $this->events()->trigger(new Event($eventName . '.pre', $this, $args));
    }

    /**
     * Triggers the PostEvent and return the result value.
     *
     * @param string $eventName
     * @param ArrayObject $args
     * @param mixed $result
     * @return mixed
     */
    protected function triggerPost($eventName, \ArrayObject $args, &$result)
    {
        $postEvent = new PostEvent($eventName . '.post', $this, $args, $result);
        $eventRs = $this->events()->trigger($postEvent);
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
     * @param string $eventName
     * @param ArrayObject $args
     * @param Exception $exception
     * @throws Exception
     * @return mixed
     */
    protected function triggerException($eventName, \ArrayObject $args, \Exception $exception)
    {
        $exceptionEvent = new ExceptionEvent($eventName . '.exception', $this, $args, $exception);
        $eventRs = $this->events()->trigger($exceptionEvent);

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
     * @param Plugin $plugin
     * @return boolean
     */
    public function hasPlugin(Plugin $plugin)
    {
        return in_array($plugin, $this->pluginRegistry, true);
    }

    /**
     * Register a plugin
     *
     * @param Plugin $plugin
     * @return Adapter Fluent interface
     * @throws LogicException
     */
    public function addPlugin(Plugin $plugin)
    {
        if (in_array($plugin, $this->pluginRegistry, true)) {
            throw new LogicException('Plugin already registered');
        }

        $plugin->attach($this->events());
        $this->pluginRegistry[] = $plugin;

        return $this;
    }

    /**
     * Unregister an already registered plugin
     *
     * @param Plugin $plugin
     * @return Adapter Fluent interface
     * @throws LogicException
     */
    public function removePlugin(Plugin $plugin)
    {
        $pluginRegistryIndex = array_search($plugin, $this->pluginRegistry, true);
        if ($pluginRegistryIndex === false) {
            throw new LogicException('Plugin not registered');
        }

        $plugin->detach($this->events());
        unset($this->pluginRegistry[$pluginRegistryIndex]);

        return $this;
    }

    /**
     * Get all registered plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->pluginRegistry;
    }

    /* reading */

    /**
     * Get items
     *
     * @param array $keys
     * @param array $options
     * @return array
     */
    public function getItems(array $keys, array $options = array())
    {
        if (!$this->getReadable()) {
            return array();
        }

        $ret = array();
        foreach ($keys as $key) {
            try {
                $value = $this->getItem($key, $options);
                if ($value !== false) {
                    $ret[$key] = $value;
                }
            } catch (ItemNotFoundException $e) {
                // ignore missing items
            }
        }

        return $ret;
    }

    /**
     * Checks if adapter has an item
     *
     * @param string $key
     * @param array $options
     * @return bool
     */
    public function hasItem($key, array $options = array())
    {
        if (!$this->getReadable()) {
            return false;
        }

        try {
            $ret = ($this->getItem($key, $options) !== false);
        } catch (ItemNotFoundException $e) {
            $ret = false;
        }

        return $ret;
    }

    /**
     * Checks if adapter has items
     *
     * @param array $keys
     * @param array $options
     * @return array
     */
    public function hasItems(array $keys, array $options = array())
    {
        if (!$this->getReadable()) {
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
     * @param array $keys
     * @param array $options
     * @return array
     */
    public function getMetadatas(array $keys, array $options = array())
    {
        if (!$this->getReadable()) {
            return array();
        }

        $ret = array();
        foreach ($keys as $key) {
            try {
                $meta = $this->getMetadata($key, $options);
                if ($meta !== false) {
                    $ret[$key] = $meta;
                }
            } catch (ItemNotFoundException $e) {
                // ignore missing items
            }
        }

        return $ret;
    }

    /* writing */

    /**
     * Set items
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return bool
     */
    public function setItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getWritable()) {
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
     * @param $key
     * @param $value
     * @param array $options
     * @return bool
     * @throws RuntimeException
     */
    public function addItem($key, $value, array $options = array())
    {
        if ($this->hasItem($key, $options)) {
            throw new RuntimeException("Key '{$key}' already exists");
        }
        return $this->setItem($key, $value, $options);
    }

    /**
     * Add items
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return bool
     */
    public function addItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getWritable()) {
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
     * @param $key
     * @param $value
     * @param array $options
     * @return bool
     * @throws ItemNotFoundException
     */
    public function replaceItem($key, $value, array $options = array())
    {
        if (!$this->hasItem($key, $options)) {
            throw new ItemNotFoundException("Key '{$key}' doen't exists");
        }
        return $this->setItem($key, $value, $options);
    }

    /**
     * Replace items
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return bool
     */
    public function replaceItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getWritable()) {
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
     * @param $token
     * @param $key
     * @param $value
     * @param array $options
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
     * @param $key
     * @param array $options
     * @return bool
     */
    public function touchItem($key, array $options = array())
    {
        if (!$this->getWritable() || !$this->getReadable()) {
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
     * @param array $keys
     * @param array $options
     * @return bool
     */
    public function touchItems(array $keys, array $options = array())
    {
        // Don't check readable because not all adapters needs to read the item before
        if (!$this->getWritable()) {
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
     * @param array $keys
     * @param array $options
     * @return bool
     */
    public function removeItems(array $keys, array $options = array())
    {
        if (!$this->getWritable()) {
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
     * @param $key
     * @param $value
     * @param array $options
     * @return bool|int
     */
    public function incrementItem($key, $value, array $options = array())
    {
       if (!$this->getWritable() || !$this->getReadable()) {
           return false;
       }

       $value = (int)$value;
       $get = (int)$this->getItem($key, $options);
       $this->setItem($key, $get + $value, $options);
       return $get + $value;
    }

    /**
     * Increment items
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return bool
     */
    public function incrementItems(array $keyValuePairs, array $options = array())
    {
        // Don't check readable because not all adapters needs read the value before
        if (!$this->getWritable()) {
            return false;
        }

        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->incrementItems($key, $value, $options) && $ret;
        }
        return $ret;
    }

    /**
     * Decrement an item
     *
     * @param $key
     * @param $value
     * @param array $options
     * @return bool|int
     */
    public function decrementItem($key, $value, array $options = array())
    {
        if (!$this->getWritable() || !$this->getReadable()) {
            return false;
        }

        $value = (int)$value;
        $get = (int)$this->getItem($key, $options);
        $this->setItem($key, $get - $value, $options);
        return $get - $value;
    }

    /**
     * Decrement items
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return bool
     */
    public function decrementItems(array $keyValuePairs, array $options = array())
    {
        // Don't check readable because not all adapters needs read the value before
        if (!$this->getWritable()) {
            return false;
        }

        $ret = true;
        foreach ($keyValuePairs as $key => $value) {
            $ret = $this->decrementMulti($key, $value, $options) && $ret;
        }
        return $ret;
    }

    /* non-blocking */

    /**
     * Get delayed
     *
     * @param array $keys
     * @param array $options
     * @return bool
     * @throws InvalidArgumentException|RuntimeException
     */
    public function getDelayed(array $keys, array $options = array())
    {
        if ($this->stmtActive) {
            throw new RuntimeException('Statement already in use');
        }

        if (!$this->getReadable()) {
            return false;
        } elseif (!$keys) {
            // empty statement
            return true;
        }

        $this->normalizeOptions($options);

        if (!isset($options['select'])) {
            $options['select'] = array('key', 'value');
        }

        $this->stmtOptions = array_merge($this->getOptions(), $options);
        $this->stmtKeys    = $keys;
        $this->stmtActive  = true;

        if (isset($options['callback'])) {
            $callback = $options['callback'];
            if (!is_callable($callback, false)) {
                throw new InvalidArgumentException('Invalid callback');
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
     * @param int $mode
     * @param array $options
     * @throws UnsupportedMethodCallException
     */
    public function find($mode = self::MATCH_ACTIVE, array $options = array())
    {
        throw new UnsupportedMethodCallException('find isn\'t supported by this adapter');
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
            if ( $exist === false
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
        while ( ($item = $this->fetch()) !== false ) {
            $rs[] = $item;
        }
        return $rs;
    }

    /* cleaning */

    /**
     * Clear
     *
     * @param int $mode
     * @param array $options
     * @throws RuntimeException
     */
    public function clear($mode = self::MATCH_EXPIRED, array $options = array())
    {
        throw new RuntimeException(
            "This adapter doesn't support to clear items off all namespaces"
        );
    }

    /**
     * Clear by namespace
     *
     * @param int $mode
     * @param array $options
     * @throws RuntimeException
     */
    public function clearByNamespace($mode = self::MATCH_EXPIRED, array $options = array())
    {
        throw new RuntimeException(
            "This adapter doesn't support to clear items by namespace"
        );
    }

    /**
     * Optimize
     *
     * @param array $options
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
            $this->capabilityMarker = new \stdClass();
            $this->capabilities = new Capabilities($this->capabilityMarker);
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
        // ttl
        if (isset($options['ttl'])) {
            $this->normalizeTtl($options['ttl']);
        } else {
            $options['ttl'] = $this->getTtl();
        }

        // namespace
        if (isset($options['namespace'])) {
            $this->normalizeNamespace($options['namespace']);
        } else {
            $options['namespace'] = $this->getNamespace();
        }

        // ignore_missing_items
        if (isset($options['ignore_missing_items'])) {
            $options['ignore_missing_items'] = (bool)$options['ignore_missing_items'];
        } else {
            $options['ignore_missing_items'] = $this->getIgnoreMissingItems();
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
     * @param int|float $ttl
     * @throws InvalidArgumentException
     */
    protected function normalizeTtl(&$ttl)
    {
        if (!is_int($ttl)) {
            $ttl = (float)$ttl;

            // convert to int if possible
            if ($ttl === (float)(int)$ttl) {
                $ttl = (int)$ttl;
            }
        }

        if ($ttl < 0) {
             throw new InvalidArgumentException("TTL can't be negative");
        }
    }

    /**
     * Validates and normalize a namespace.
     *
     * @param string $namespace
     * @throws InvalidArgumentException
     */
    protected function normalizeNamespace(&$namespace)
    {
        $namespace = (string)$namespace;

        if ($namespace === '') {
            throw new InvalidArgumentException('Empty namespaces are nor allowed');
        } elseif (($p = $this->getNamespacePattern()) && !preg_match($p, $namespace)) {
            throw new InvalidArgumentException(
                "The namespace '{$namespace}' doesn't match agains pattern '{$p}'"
            );
        }
    }

    /**
     * Validates and normalize tags array
     *
     * @param array $tags
     * @throws Zend\Cache\InvalidArgumentException
     */
    protected function normalizeTags(&$tags)
    {
        if (!is_array($tags)) {
            throw new InvalidArgumentException('Tags have to be an array');
        }

        foreach ($tags as &$tag) {
            $tag = (string)$tag;
            if ($tag === '') {
                throw new InvalidArgumentException('Empty tags are not allowed');
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
            $select = array((string)$select);
        } else {
            $select = array_unique($select);
        }
    }

    /**
     * Normalize the matching mode needed on (clear and find)
     *
     * @param int $mode    Matching mode to normalize
     * @param int $default Default matching mode
     */
    protected function normalizeMatchingMode(&$mode, $default, array &$normalizedOptions)
    {
        $mode = (int)$mode;
        if ( ($mode & self::MATCH_EXPIRED) != self::MATCH_EXPIRED
          && ($mode & self::MATCH_ACTIVE) != self::MATCH_ACTIVE) {
            $mode = $mode | (int)$default;
        }

        // TODO: normalize matching mode with given tags
    }

    /**
     * Validates and normalizes a key
     *
     * @param string $key
     * @return string
     * @throws InvalidArgumentException On an invalid key
     */
    protected function normalizeKey(&$key)
    {
        $key = (string)$key;

        if (($p = $this->getKeyPattern()) && !preg_match($p, $key)) {
            throw new InvalidArgumentException("The key '{$key}' doesn't match agains pattern '{$p}'");
        }
    }

}
