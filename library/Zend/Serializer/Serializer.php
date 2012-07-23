<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Serializer
 */

namespace Zend\Serializer;

use Zend\Serializer\Adapter\AdapterInterface as Adapter;

/**
 * @category   Zend
 * @package    Zend_Serializer
 */
class Serializer
{
    /**
     * Plugin manager for loading adapters
     *
     * @var null|AdapterPluginManager
     */
    private static $adapters = null;

    /**
     * The default adapter.
     *
     * @var string|Adapter
     */
    protected static $defaultAdapter = 'PhpSerialize';

    /**
     * Create a serializer adapter instance.
     *
     * @param  string|Adapter $adapterName Name of the adapter class
     * @param  array |\Traversable|null $adapterOptions Serializer options
     * @return Adapter
     */
    public static function factory($adapterName, $adapterOptions = null)
    {
        if ($adapterName instanceof Adapter) {
            return $adapterName; // $adapterName is already an adapter object
        }

        return self::getAdapterPluginManager()->get($adapterName, $adapterOptions);
    }

    /**
     * Change the adapter plugin manager
     *
     * @param  AdapterPluginManager $adapters
     * @return void
     */
    public static function setAdapterPluginManager(AdapterPluginManager $adapters)
    {
        self::$adapters = $adapters;
    }

    /**
     * Get the adapter plugin manager
     *
     * @return AdapterPluginManager
     */
    public static function getAdapterPluginManager()
    {
        if (self::$adapters === null) {
            self::$adapters = new AdapterPluginManager();
        }
        return self::$adapters;
    }

    /**
     * Resets the internal adapter plugin manager
     *
     * @return AdapterPluginManager
     */
    public static function resetAdapterPluginManager()
    {
        self::$adapters = new AdapterPluginManager();
        return self::$adapters;
    }

    /**
     * Change the default adapter.
     *
     * @param string|Adapter $adapter
     * @param array|\Traversable|null $options
     */
    public static function setDefaultAdapter($adapter, $adapterOptions = null)
    {
        self::$defaultAdapter = self::factory($adapter, $adapterOptions);
    }

    /**
     * Get the default adapter.
     *
     * @return Adapter
     */
    public static function getDefaultAdapter()
    {
        if (!self::$defaultAdapter instanceof Adapter) {
            self::setDefaultAdapter(self::$defaultAdapter);
        }
        return self::$defaultAdapter;
    }

    /**
     * Generates a storable representation of a value using the default adapter.
     * Optionally different adapter could be provided as second argument
     *
     * @param  mixed $value
     * @param  string|Adapter $adapter
     * @param  array|\Traversable|null $adapterOptions Adapter constructor options
     *                                                 only used to create adapter instance
     * @return string
     */
    public static function serialize($value, $adapter = null, $adapterOptions = null)
    {
        if ($adapter !== null) {
            $adapter = self::factory($adapter, $adapterOptions);
        } else {
            $adapter = self::getDefaultAdapter();
        }

        return $adapter->serialize($value);
    }

    /**
     * Creates a PHP value from a stored representation using the default adapter.
     * Optionally different adapter could be provided as second argument
     *
     * @param  string $serialized
     * @param  string|Adapter $adapter
     * @param  array|\Traversable|null $adapterOptions Adapter constructor options
     *                                                 only used to create adapter instance
     * @return mixed
     */
    public static function unserialize($serialized, $adapter = null, $adapterOptions = null)
    {
        if ($adapter !== null) {
            $adapter = self::factory($adapter, $adapterOptions);
        } else {
            $adapter = self::getDefaultAdapter();
        }

        return $adapter->unserialize($serialized);
    }
}