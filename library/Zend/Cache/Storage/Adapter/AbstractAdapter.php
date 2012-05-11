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
    Zend\Cache\Storage\Capabilities,
    Zend\Cache\Storage\Event,
    Zend\Cache\Storage\ExceptionEvent,
    Zend\Cache\Storage\PostEvent,
    Zend\Cache\Storage\Plugin,
    Zend\EventManager\EventManager,
    Zend\EventManager\EventsCapableInterface;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAdapter implements AdapterInterface, EventsCapableInterface
{
    /**
     * The used EventManager if any
     *
     * @var null|EventCollection
     */
    protected $events = null;

    /**
     * Event handles of this adapter
     * @var array
     */
    protected $eventHandles = array();

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
     * @throws Exception\ExceptionInterface
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

        if ($this->eventHandles) {
            $events = $this->events();
            foreach ($this->eventHandles as $handle) {
                $events->detach($handle);
            }
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
        if ($this->options !== $options) {
            if (!$options instanceof AdapterOptions) {
                $options = new AdapterOptions($options);
            }

            if ($this->options) {
                $this->options->setAdapter(null);
            }
            $options->setAdapter($this);
            $this->options = $options;

            $event = new Event('option', $this, new ArrayObject($options->toArray()));
            $this->events()->trigger($event);
        }
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
     * Get the event manager
     *
     * @return EventCollection
     */
    public function events()
    {
        if ($this->events === null) {
            $this->events = new EventManager(array(__CLASS__, get_called_class()));
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
     * @param  string      $eventName
     * @param  ArrayObject $args
     * @param  mixed       $result
     * @return mixed
     */
    protected function triggerPost($eventName, ArrayObject $args, & $result)
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
     * @param  string      $eventName
     * @param  ArrayObject $args
     * @param  mixed       $result
     * @param  \Exception  $exception
     * @throws Exception\ExceptionInterface
     * @return mixed
     */
    protected function triggerException($eventName, ArrayObject $args, & $result, \Exception $exception)
    {
        $exceptionEvent = new ExceptionEvent($eventName . '.exception', $this, $args, $result, $exception);
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
     * @param  Plugin\PluginInterface $plugin
     * @return boolean
     */
    public function hasPlugin(Plugin\PluginInterface $plugin)
    {
        $registry = $this->getPluginRegistry();
        return $registry->contains($plugin);
    }

    /**
     * Register a plugin
     *
     * @param  Plugin\PluginInterface $plugin
     * @param  int                    $priority
     * @return AbstractAdapter Fluent interface
     * @throws Exception\LogicException
     */
    public function addPlugin(Plugin\PluginInterface $plugin, $priority = 1)
    {
        $registry = $this->getPluginRegistry();
        if ($registry->contains($plugin)) {
            throw new Exception\LogicException(sprintf(
                'Plugin of type "%s" already registered',
                get_class($plugin)
            ));
        }

        $plugin->attach($this->events(), $priority);
        $registry->attach($plugin);

        return $this;
    }

    /**
     * Unregister an already registered plugin
     *
     * @param  Plugin\PluginInterface $plugin
     * @return AbstractAdapter Fluent interface
     * @throws Exception\LogicException
     */
    public function removePlugin(Plugin\PluginInterface $plugin)
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
     * Get an item.
     *
     * Options:
     *  - ttl <int> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string  $key
     * @param  array   $options
     * @param  boolean $success
     * @param  mixed   $casToken
     * @return mixed Data on success, null on failure
     * @throws Exception\ExceptionInterface
     *
     * @triggers getItem.pre(PreEvent)
     * @triggers getItem.post(PostEvent)
     * @triggers getItem.exception(ExceptionEvent)
     */
    public function getItem($key, array $options = array(), & $success = null, & $casToken = null)
    {
        if (!$this->getOptions()->getReadable()) {
            $success = false;
            return null;
        }

        $this->normalizeKey($key);
        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'key'      => & $key,
            'options'  => & $options,
            'success'  => & $success,
            'casToken' => & $casToken,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->internalGetItem($args['key'], $args['options'], $args['success'], $args['casToken']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

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
    abstract protected function internalGetItem(& $normalizedKey, array & $normalizedOptions, & $success = null, & $casToken = null);

    /**
     * Get multiple items.
     *
     * Options:
     *  - ttl <int> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $keys
     * @param  array $options
     * @return array Associative array of keys and values
     * @throws Exception\ExceptionInterface
     *
     * @triggers getItems.pre(PreEvent)
     * @triggers getItems.post(PostEvent)
     * @triggers getItems.exception(ExceptionEvent)
     */
    public function getItems(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return array();
        }

        $this->normalizeKeys($keys);
        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'keys'     => & $keys,
            'options'  => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->internalGetItems($args['keys'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = array();
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
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
        $success = null;
        $result  = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $value = $this->internalGetItem($normalizedKey, $normalizedOptions, $success);
            if ($success) {
                $result[$normalizedKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Test if an item exists.
     *
     * Options:
     *  - ttl <int> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string $key
     * @param  array  $options
     * @return boolean
     * @throws Exception\ExceptionInterface
     *
     * @triggers hasItem.pre(PreEvent)
     * @triggers hasItem.post(PostEvent)
     * @triggers hasItem.exception(ExceptionEvent)
     */
    public function hasItem($key, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return false;
        }

        $this->normalizeKey($key);
        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->internalHasItem($args['key'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
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
        $success = null;
        $this->internalGetItem($normalizedKey, $normalizedOptions, $success);
        return $success;
    }

    /**
     * Test multiple items.
     *
     * Options:
     *  - ttl <int> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $keys
     * @param  array $options
     * @return array Array of found keys
     * @throws Exception\ExceptionInterface
     *
     * @triggers hasItems.pre(PreEvent)
     * @triggers hasItems.post(PostEvent)
     * @triggers hasItems.exception(ExceptionEvent)
     */
    public function hasItems(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return array();
        }

        $this->normalizeKeys($keys);
        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'keys'     => & $keys,
            'options'  => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->internalHasItems($args['keys'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = array();
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
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
        $result = array();
        foreach ($normalizedKeys as $normalizedKey) {
            if ($this->internalHasItem($normalizedKey, $normalizedOptions)) {
                $result[] = $normalizedKey;
            }
        }
        return $result;
    }

    /**
     * Get metadata of an item.
     *
     * Options:
     *  - ttl <int> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string $key
     * @param  array  $options
     * @return array|boolean Metadata on success, false on failure
     * @throws Exception\ExceptionInterface
     *
     * @triggers getMetadata.pre(PreEvent)
     * @triggers getMetadata.post(PostEvent)
     * @triggers getMetadata.exception(ExceptionEvent)
     */
    public function getMetadata($key, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return false;
        }

        $this->normalizeKey($key);
        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->internalGetMetadata($args['key'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

    /**
     * Internal method to get metadata of an item.
     *
     * Options:
     *  - ttl <int>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return array|boolean Metadata on success, false on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetMetadata(& $normalizedKey, array & $normalizedOptions)
    {
        if (!$this->internalHasItem($normalizedKey, $normalizedOptions)) {
            return false;
        }

        return array();
    }

    /**
     * Get multiple metadata
     *
     * Options:
     *  - ttl <int> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $keys
     * @param  array $options
     * @return array Associative array of keys and metadata
     * @throws Exception\ExceptionInterface
     *
     * @triggers getMetadatas.pre(PreEvent)
     * @triggers getMetadatas.post(PostEvent)
     * @triggers getMetadatas.exception(ExceptionEvent)
     */
    public function getMetadatas(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return array();
        }

        $this->normalizeKeys($keys);
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

            $result = $this->internalGetMetadatas($args['keys'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = array();
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

    /**
     * Internal method to get multiple metadata
     *
     * Options:
     *  - ttl <int>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $normalizedKeys
     * @param  array $normalizedOptions
     * @return array Associative array of keys and metadata
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetMetadatas(array & $normalizedKeys, array & $normalizedOptions)
    {
        $result = array();
        foreach ($normalizedKeys as $normalizedKey) {
            $metadata = $this->internalGetMetadata($normalizedKey, $normalizedOptions);
            if ($metadata !== false) {
                $result[$normalizedKey] = $metadata;
            }
        }
        return $result;
    }

    /* writing */

    /**
     * Store an item.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception\ExceptionInterface
     *
     * @triggers setItem.pre(PreEvent)
     * @triggers setItem.post(PostEvent)
     * @triggers setItem.exception(ExceptionEvent)
     */
    public function setItem($key, $value, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
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

            $result = $this->internalSetItem($args['key'], $args['value'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

    /**
     * Internal method to store an item.
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
    abstract protected function internalSetItem(& $normalizedKey, & $value, array & $normalizedOptions);

    /**
     * Store multiple items.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return array Array of not stored keys
     * @throws Exception\ExceptionInterface
     *
     * @triggers setItems.pre(PreEvent)
     * @triggers setItems.post(PostEvent)
     * @triggers setItems.exception(ExceptionEvent)
     */
    public function setItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return array_keys($keyValuePairs);
        }

        $this->normalizeKeyValuePairs($keyValuePairs);
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

            $result = $this->internalSetItems($args['keyValuePairs'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = array_keys($keyValuePairs);
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

    /**
     * Internal method to store multiple items.
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
    protected function internalSetItems(array & $normalizedKeyValuePairs, array & $normalizedOptions)
    {
        $failedKeys = array();
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            if (!$this->internalSetItem($normalizedKey, $value, $normalizedOptions)) {
                $failedKeys[] = $normalizedKey;
            }
        }
        return $failedKeys;
    }

    /**
     * Add an item.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception\ExceptionInterface
     *
     * @triggers addItem.pre(PreEvent)
     * @triggers addItem.post(PostEvent)
     * @triggers addItem.exception(ExceptionEvent)
     */
    public function addItem($key, $value, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
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

            $result = $this->internalAddItem($args['key'], $args['value'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

    /**
     * Internal method to add an item.
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
    protected function internalAddItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        if ($this->internalHasItem($normalizedKey, $normalizedOptions)) {
            return false;
        }
        return $this->internalSetItem($normalizedKey, $value, $normalizedOptions);
    }

    /**
     * Add multiple items.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return array Array of not stored keys
     * @throws Exception\ExceptionInterface
     *
     * @triggers addItems.pre(PreEvent)
     * @triggers addItems.post(PostEvent)
     * @triggers addItems.exception(ExceptionEvent)
     */
    public function addItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return array_keys($keyValuePairs);
        }

        $this->normalizeKeyValuePairs($keyValuePairs);
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

            $result = $this->internalAddItems($args['keyValuePairs'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = array_keys($keyValuePairs);
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

    /**
     * Internal method to add multiple items.
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
    protected function internalAddItems(array & $normalizedKeyValuePairs, array & $normalizedOptions)
    {
        $result = array();
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            if (!$this->internalAddItem($normalizedKey, $value, $normalizedOptions)) {
                $result[] = $normalizedKey;
            }
        }
        return $result;
    }

    /**
     * Replace an existing item.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception\ExceptionInterface
     *
     * @triggers replaceItem.pre(PreEvent)
     * @triggers replaceItem.post(PostEvent)
     * @triggers replaceItem.exception(ExceptionEvent)
     */
    public function replaceItem($key, $value, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
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

            $result = $this->internalReplaceItem($args['key'], $args['value'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
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
        if (!$this->internalhasItem($normalizedKey, $normalizedOptions)) {
            return false;
        }

        return $this->internalSetItem($normalizedKey, $value, $normalizedOptions);
    }

    /**
     * Replace multiple existing items.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return array Array of not stored keys
     * @throws Exception\ExceptionInterface
     *
     * @triggers replaceItems.pre(PreEvent)
     * @triggers replaceItems.post(PostEvent)
     * @triggers replaceItems.exception(ExceptionEvent)
     */
    public function replaceItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return array_keys($keyValuePairs);
        }

        $this->normalizeKeyValuePairs($keyValuePairs);
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

            $result = $this->internalReplaceItems($args['keyValuePairs'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = array_keys($keyValuePairs);
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
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
        $result = array();
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            if (!$this->internalReplaceItem($normalizedKey, $value, $normalizedOptions)) {
                $result[] = $normalizedKey;
            }
        }
        return $result;
    }

    /**
     * Set an item only if token matches
     *
     * It uses the token received from getItem() to check if the item has
     * changed before overwriting it.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  mixed  $token
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    getItem()
     * @see    setItem()
     */
    public function checkAndSetItem($token, $key, $value, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
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

            $result = $this->internalCheckAndSetItem($args['token'], $args['key'], $args['value'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
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
     * @throws Exception\ExceptionInterface
     * @see    getItem()
     * @see    setItem()
     */
    protected function internalCheckAndSetItem(& $token, & $normalizedKey, & $value, array & $normalizedOptions)
    {
        $oldValue = $this->internalGetItem($normalizedKey, $normalizedOptions);
        if ($oldValue !== $token) {
            return false;
        }

        return $this->internalSetItem($normalizedKey, $value, $normalizedOptions);
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
     * @param  array  $options
     * @return boolean
     * @throws Exception\ExceptionInterface
     *
     * @triggers touchItem.pre(PreEvent)
     * @triggers touchItem.post(PostEvent)
     * @triggers touchItem.exception(ExceptionEvent)
     */
    public function touchItem($key, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
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

            $result = $this->internalTouchItem($args['key'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

    /**
     * Internal method to reset lifetime of an item
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
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
        $success = null;
        $value   = $this->internalGetItem($normalizedKey, $normalizedOptions, $success);
        if (!$success) {
            return false;
        }

        // rewrite item to update mtime/ttl
        if (!isset($normalizedOptions['tags'])) {
            $info = $this->internalGetMetadata($normalizedKey, $normalizedOptions);
            if (isset($info['tags'])) {
                $normalizedOptions['tags'] = & $info['tags'];
            }
        }

        return $this->internalReplaceItem($normalizedKey, $value, $normalizedOptions);
    }

    /**
     * Reset lifetime of multiple items.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $keys
     * @param  array $options
     * @return array Array of not updated keys
     * @throws Exception\ExceptionInterface
     *
     * @triggers touchItems.pre(PreEvent)
     * @triggers touchItems.post(PostEvent)
     * @triggers touchItems.exception(ExceptionEvent)
     */
    public function touchItems(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return $keys;
        }

        $this->normalizeKeys($keys);
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

            $result = $this->internalTouchItems($args['keys'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $keys, $e);
        }
    }

    /**
     * Internal method to reset lifetime of multiple items.
     *
     * Options:
     *  - ttl <float
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to us
     *
     * @param  array $normalizedKeys
     * @param  array $normalizedOptions
     * @return array Array of not updated keys
     * @throws Exception\ExceptionInterface
     */
    protected function internalTouchItems(array & $normalizedKeys, array & $normalizedOptions)
    {
        $result = array();
        foreach ($normalizedKeys as $normalizedKey) {
            if (!$this->internalTouchItem($normalizedKey, $normalizedOptions)) {
                $result[] = $normalizedKey;
            }
        }
        return $result;
    }

    /**
     * Remove an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string $key
     * @param  array  $options
     * @return boolean
     * @throws Exception\ExceptionInterface
     *
     * @triggers removeItem.pre(PreEvent)
     * @triggers removeItem.post(PostEvent)
     * @triggers removeItem.exception(ExceptionEvent)
     */
    public function removeItem($key, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
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

            $result = $this->internalRemoveItem($args['key'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
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
    abstract protected function internalRemoveItem(& $normalizedKey, array & $normalizedOptions);

    /**
     * Remove multiple items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $keys
     * @param  array $options
     * @return array Array of not removed keys
     * @throws Exception\ExceptionInterface
     *
     * @triggers removeItems.pre(PreEvent)
     * @triggers removeItems.post(PostEvent)
     * @triggers removeItems.exception(ExceptionEvent)
     */
    public function removeItems(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return $keys;
        }

        $this->normalizeOptions($options);
        $this->normalizeKeys($keys);
        $args = new ArrayObject(array(
            'keys'    => & $keys,
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->internalRemoveItems($args['keys'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            return $this->triggerException(__FUNCTION__, $args, $keys, $e);
        }
    }

    /**
     * Internal method to remove multiple items.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $keys
     * @param  array $options
     * @return array Array of not removed keys
     * @throws Exception\ExceptionInterface
     */
    protected function internalRemoveItems(array & $normalizedKeys, array & $normalizedOptions)
    {
        $result = array();
        foreach ($normalizedKeys as $normalizedKey) {
            if (!$this->internalRemoveItem($normalizedKey, $normalizedOptions)) {
                $result[] = $normalizedKey;
            }
        }
        return $result;
    }

    /**
     * Increment an item.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string $key
     * @param  int    $value
     * @param  array  $options
     * @return int|boolean The new value on success, false on failure
     * @throws Exception\ExceptionInterface
     *
     * @triggers incrementItem.pre(PreEvent)
     * @triggers incrementItem.post(PostEvent)
     * @triggers incrementItem.exception(ExceptionEvent)
     */
    public function incrementItem($key, $value, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
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

            $result = $this->internalIncrementItem($args['key'], $args['value'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
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
        $success  = null;
        $value    = (int) $value;
        $get      = (int) $this->internalGetItem($normalizedKey, $normalizedOptions, $success);
        $newValue = $get + $value;

        if ($success) {
            $this->internalReplaceItem($normalizedKey, $newValue, $normalizedOptions);
        } else {
            $this->internalAddItem($normalizedKey, $newValue, $normalizedOptions);
        }

        return $newValue;
    }

    /**
     * Increment multiple items.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return array Associative array of keys and new values
     * @throws Exception\ExceptionInterface
     *
     * @triggers incrementItems.pre(PreEvent)
     * @triggers incrementItems.post(PostEvent)
     * @triggers incrementItems.exception(ExceptionEvent)
     */
    public function incrementItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return array();
        }

        $this->normalizeOptions($options);
        $this->normalizeKeyValuePairs($keyValuePairs);
        $args = new ArrayObject(array(
            'keyValuePairs' => & $keyValuePairs,
            'options'       => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->internalIncrementItems($args['keyValuePairs'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = array();
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

    /**
     * Internal method to increment multiple items.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $normalizedKeyValuePairs
     * @param  array $normalizedOptions
     * @return array Associative array of keys and new values
     * @throws Exception\ExceptionInterface
     */
    protected function internalIncrementItems(array & $normalizedKeyValuePairs, array & $normalizedOptions)
    {
        $result = array();
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            $newValue = $this->internalIncrementItem($normalizedKey, $value, $normalizedOptions);
            if ($newValue !== false) {
                $result[$normalizedKey] = $newValue;
            }
        }
        return $result;
    }

    /**
     * Decrement an item.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string $key
     * @param  int    $value
     * @param  array  $options
     * @return int|boolean The new value on success, false on failure
     * @throws Exception\ExceptionInterface
     *
     * @triggers decrementItem.pre(PreEvent)
     * @triggers decrementItem.post(PostEvent)
     * @triggers decrementItem.exception(ExceptionEvent)
     */
    public function decrementItem($key, $value, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
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

            $result = $this->internalDecrementItem($args['key'], $args['value'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
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
        $success  = null;
        $value    = (int) $value;
        $get      = (int) $this->internalGetItem($normalizedKey, $normalizedOptions, $success);
        $newValue = $get - $value;

        if ($success) {
            $this->internalReplaceItem($normalizedKey, $newValue, $normalizedOptions);
        } else {
            $this->internalAddItem($normalizedKey, $newValue, $normalizedOptions);
        }

        return $newValue;
    }

    /**
     * Decrement multiple items.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return array Associative array of keys and new values
     * @throws Exception\ExceptionInterface
     *
     * @triggers incrementItems.pre(PreEvent)
     * @triggers incrementItems.post(PostEvent)
     * @triggers incrementItems.exception(ExceptionEvent)
     */
    public function decrementItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return array();
        }

        $this->normalizeOptions($options);
        $this->normalizeKeyValuePairs($keyValuePairs);
        $args = new ArrayObject(array(
            'keyValuePairs' => & $keyValuePairs,
            'options'       => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->internalDecrementItems($args['keyValuePairs'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = array();
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

    /**
     * Internal method to decrement multiple items.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $normalizedKeyValuePairs
     * @param  array $normalizedOptions
     * @return array Associative array of keys and new values
     * @throws Exception\ExceptionInterface
     */
    protected function internalDecrementItems(array & $normalizedKeyValuePairs, array & $normalizedOptions)
    {
        $result = array();
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            $newValue = $this->decrementItem($normalizedKey, $value, $normalizedOptions);
            if ($newValue !== false) {
                $result[$normalizedKey] = $newValue;
            }
        }
        return $result;
    }

    /* non-blocking */

    /**
     * Request multiple items.
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
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    fetch()
     * @see    fetchAll()
     *
     * @triggers getDelayed.pre(PreEvent)
     * @triggers getDelayed.post(PostEvent)
     * @triggers getDelayed.exception(ExceptionEvent)
     */
    public function getDelayed(array $keys, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return false;
        } elseif (!$keys) {
            // empty statement
            return true;
        }

        $this->normalizeKeys($keys);
        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'keys'     => & $keys,
            'options'  => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->internalGetDelayed($args['keys'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

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
     * @throws Exception\ExceptionInterface
     * @see    fetch()
     * @see    fetchAll()
     */
    protected function internalGetDelayed(array & $normalizedKeys, array & $normalizedOptions)
    {
        if ($this->stmtActive) {
            throw new Exception\RuntimeException('Statement already in use');
        }

        $this->stmtOptions = array_merge($this->getOptions()->toArray(), $normalizedOptions);
        $this->stmtKeys    = & $normalizedKeys;
        $this->stmtActive  = true;

        if (isset($normalizedOptions['callback'])) {
            $callback = $normalizedOptions['callback'];
            if (!is_callable($callback, false)) {
                throw new Exception\InvalidArgumentException('Invalid callback');
            }

            while ( ($item = $this->fetch()) !== false) {
                call_user_func($callback, $item);
            }
        }

        return true;
    }

    /* find */

    /**
     * Find items.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-live (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - Tags to search for used with matching modes of
     *      Adapter::MATCH_TAGS_*
     *
     * @param  int   $mode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    fetch()
     * @see    fetchAll()
     *
     * @triggers find.pre(PreEvent)
     * @triggers find.post(PostEvent)
     * @triggers find.exception(ExceptionEvent)
     */
    public function find($mode = self::MATCH_ACTIVE, array $options = array())
    {
        if (!$this->getOptions()->getReadable()) {
            return false;
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

            $result = $this->internalFind($args['mode'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

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
        throw new Exception\UnsupportedMethodCallException('find isn\'t supported by this adapter');
    }

    /**
     * Fetches the next item from result set
     *
     * @return array|boolean The next item or false
     * @throws Exception\ExceptionInterface
     * @see    fetchAll()
     *
     * @triggers fetch.pre(PreEvent)
     * @triggers fetch.post(PostEvent)
     * @triggers fetch.exception(ExceptionEvent)
     */
    public function fetch()
    {
        $args = new ArrayObject();

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->internalFetch();
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
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

        $options = $this->stmtOptions;
        $success = null;

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
                    $value = $this->internalGetItem($key, $options, $success);
                    if (!$success) {
                        $exist = false;
                        break;
                    }
                    $exist = true;
                    $item['value'] = $value;
                } else {
                    if ($info === null) {
                        $info = $this->internalGetMetadata($key, $options);
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
            if ($exist === false || ($exist === null && !$this->internalHasItem($key, $options))) {
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
     * Returns all items of result set.
     *
     * @return array The result set as array containing all items
     * @throws Exception\ExceptionInterface
     * @see    fetch()
     *
     * @triggers fetchAll.pre(PreEvent)
     * @triggers fetchAll.post(PostEvent)
     * @triggers fetchAll.exception(ExceptionEvent)
     */
    public function fetchAll()
    {
        $args = new ArrayObject();

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->internalFetchAll();
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = array();
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

    /**
     * Internal method to return all items of result set.
     *
     * @return array The result set as array containing all items
     * @throws Exception\ExceptionInterface
     * @see    fetch()
     */
    protected function internalFetchAll()
    {
        $rs = array();
        while (($item = $this->internalFetch()) !== false) {
            $rs[] = $item;
        }
        return $rs;
    }

    /* cleaning */

    /**
     * Clear items off all namespaces.
     *
     * Options:
     *  - ttl <float> optional
     *    - The time-to-life (Default: ttl of object)
     *  - tags <array> optional
     *    - Tags to search for used with matching modes of Adapter::MATCH_TAGS_*
     *
     * @param  int   $mode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    clearByNamespace()
     *
     * @triggers clear.pre(PreEvent)
     * @triggers clear.post(PostEvent)
     * @triggers clear.exception(ExceptionEvent)
     */
    public function clear($mode = self::MATCH_EXPIRED, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
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

            $result = $this->internalClear($args['mode'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

    /**
     * Internal method to clear items off all namespaces.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - tags <array>
     *    - Tags to search for used with matching modes of Adapter::MATCH_TAGS_*
     *
     * @param  int   $normalizedMode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    clearByNamespace()
     */
    protected function internalClear(& $normalizedMode, array & $normalizedOptions)
    {
        throw new Exception\RuntimeException(
            "This adapter doesn't support to clear items off all namespaces"
        );
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
     *    - Tags to search for used with matching modes of Adapter::MATCH_TAGS_*
     *
     * @param  int   $mode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $options
     * @return boolean
     * @throws Exception\ExceptionInterface
     * @see    clear()
     *
     * @triggers clearByNamespace.pre(PreEvent)
     * @triggers clearByNamespace.post(PostEvent)
     * @triggers clearByNamespace.exception(ExceptionEvent)
     */
    public function clearByNamespace($mode = self::MATCH_EXPIRED, array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
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

            $result = $this->internalClearByNamespace($args['mode'], $args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
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
        throw new Exception\RuntimeException(
            "This adapter doesn't support to clear items by namespace"
        );
    }

    /**
     * Optimize adapter storage.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $options
     * @return boolean
     * @throws Exception\ExceptionInterface
     *
     * @triggers optimize.pre(PreEvent)
     * @triggers optimize.post(PostEvent)
     * @triggers optimize.exception(ExceptionEvent)
     */
    public function optimize(array $options = array())
    {
        if (!$this->getOptions()->getWritable()) {
            return false;
        }

        $args = new ArrayObject(array(
            'options' => & $options
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->internalOptimize($args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

    /**
     * Internal method to optimize adapter storage.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception\ExceptionInterface
     */
    protected function internalOptimize(array & $normalizedOptions)
    {
        return true;
    }

    /* status */

    /**
     * Get capabilities of this adapter
     *
     * @return Capabilities
     * @triggers getCapabilities.pre(PreEvent)
     * @triggers getCapabilities.post(PostEvent)
     * @triggers getCapabilities.exception(ExceptionEvent)
     */
    public function getCapabilities()
    {
        $args = new ArrayObject();

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->internalGetCapabilities();
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

    /**
     * Internal method to get capabilities of this adapter
     *
     * @return Capabilities
     */
    protected function internalGetCapabilities()
    {
        if ($this->capabilities === null) {
            $this->capabilityMarker = new stdClass();
            $this->capabilities     = new Capabilities($this, $this->capabilityMarker);
        }
        return $this->capabilities;
    }

    /**
     * Get storage capacity.
     *
     * @param  array $options
     * @return array|boolean Associative array of capacity, false on failure
     * @throws Exception\ExceptionInterface
     *
     * @triggers getCapacity.pre(PreEvent)
     * @triggers getCapacity.post(PostEvent)
     * @triggers getCapacity.exception(ExceptionEvent)
     */
    public function getCapacity(array $options = array())
    {
        $this->normalizeOptions($options);
        $args = new ArrayObject(array(
            'options' => & $options,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = $this->internalGetCapacity($args['options']);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }

    /**
     * Internal method to get storage capacity.
     *
     * @param  array $normalizedOptions
     * @return array|boolean Associative array of capacity, false on failure
     * @throws Exception\ExceptionInterface
     */
    abstract protected function internalGetCapacity(array & $normalizedOptions);

    /* internal */

    /**
     * Validates and normalizes the $options argument
     *
     * @param array $options
     * @return void
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
     * @return void
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
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    protected function normalizeNamespace(&$namespace)
    {
        $namespace = (string) $namespace;
        $pattern   = $this->getOptions()->getNamespacePattern();
        if ($pattern && !preg_match($pattern, $namespace)) {
            throw new Exception\InvalidArgumentException(
                "The namespace '{$namespace}' doesn't match against pattern '{$pattern}'"
            );
        }
    }

    /**
     * Validates and normalize tags array
     *
     * @param  array $tags
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
     * @throws Exception\InvalidArgumentException On an invalid key
     */
    protected function normalizeKey(&$key)
    {
        $key = (string) $key;

        if ($key === '') {
            throw new Exception\InvalidArgumentException(
                "An empty key isn't allowed"
            );
        } elseif (($p = $this->getOptions()->getKeyPattern()) && !preg_match($p, $key)) {
            throw new Exception\InvalidArgumentException(
                "The key '{$key}' doesn't match agains pattern '{$p}'"
            );
        }
    }

    /**
     * Validates and normalizes multiple keys
     *
     * @param  array $keys
     * @return void
     * @throws Exception\InvalidArgumentException On an invalid key
     */
    protected function normalizeKeys(array &$keys)
    {
        if (!$keys) {
            throw new Exception\InvalidArgumentException(
                "An empty list of keys isn't allowed"
            );
        }

        array_walk($keys, array($this, 'normalizeKey'));
        $keys = array_values(array_unique($keys));
    }

    /**
     * Validates and normalizes an array of key-value paírs
     *
     * @param  array $keyValuePairs
     * @return void
     * @throws Exception\InvalidArgumentException On an invalid key
     */
    protected function normalizeKeyValuePairs(array & $keyValuePairs)
    {
        $normalizedKeyValuePairs = array();
        foreach ($keyValuePairs as $key => $value) {
            $this->normalizeKey($key);
            $normalizedKeyValuePairs[$key] = $value;
        }
        $keyValuePairs = $normalizedKeyValuePairs;
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
