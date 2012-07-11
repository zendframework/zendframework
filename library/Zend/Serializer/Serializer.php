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
    private static $_adapters = null;

    /**
     * The default adapter.
     *
     * @var string|Adapter
     */
    protected static $_defaultAdapter = 'PhpSerialize';

    /**
     * Create a serializer adapter instance.
     *
     * @param string|Adapter $adapterName Name of the adapter class
     * @param array |\Traversable $opts Serializer options
     * @return Adapter
     */
    public static function factory($adapterName, $opts = array()) 
    {
        if ($adapterName instanceof Adapter) {
            return $adapterName; // $adapterName is already an adapter object
        }

        return self::getAdapterPluginManager()->get($adapterName, $opts);
    }

    /**
     * Get the adapter plugin manager
     *
     * @return AdapterPluginManager
     */
    public static function getAdapterPluginManager() 
    {
        if (self::$_adapters === null) {
            self::$_adapters = self::_getDefaultAdapterPluginManager();
        }
        return self::$_adapters;
    }

    /**
     * Change the adapter plugin manager
     *
     * @param  AdapterPluginManager $adapters
     * @return void
     */
    public static function setAdapterPluginManager(AdapterPluginManager $adapters) 
    {
        self::$_adapters = $adapters;
    }
    
    /**
     * Resets the internal adapter plugin manager
     *
     * @return AdapterPluginManager
     */
    public static function resetAdapterPluginManager()
    {
        self::$_adapters = self::_getDefaultAdapterPluginManager();
        return self::$_adapters;
    }
    
    /**
     * Returns a default adapter plugin manager
     *
     * @return AdapterPluginManager
     */
    protected static function _getDefaultAdapterPluginManager()
    {
        $adapters = new AdapterPluginManager();
        return $adapters;
    }

    /**
     * Change the default adapter.
     *
     * @param string|Adapter $adapter
     * @param array|\Traversable $options
     */
    public static function setDefaultAdapter($adapter, $options = array()) 
    {
        self::$_defaultAdapter = self::factory($adapter, $options);
    }

    /**
     * Get the default adapter.
     *
     * @return Adapter
     */
    public static function getDefaultAdapter() 
    {
        if (!self::$_defaultAdapter instanceof Adapter) {
            self::setDefaultAdapter(self::$_defaultAdapter);
        }
        return self::$_defaultAdapter;
    }

    /**
     * Generates a storable representation of a value using the default adapter.
     *
     * @param mixed $value
     * @param array $options
     * @return string
     */
    public static function serialize($value, array $options = array()) 
    {
        if (isset($options['adapter'])) {
            $adapter = self::factory($options['adapter']);
            unset($options['adapter']);
        } else {
            $adapter = self::getDefaultAdapter();
        }

        return $adapter->serialize($value, $options);
    }

    /**
     * Creates a PHP value from a stored representation using the default adapter.
     *
     * @param string $serialized
     * @param array $options
     * @return mixed
     */
    public static function unserialize($serialized, array $options = array()) 
    {
        if (isset($options['adapter'])) {
            $adapter = self::factory($options['adapter']);
            unset($options['adapter']);
        } else {
            $adapter = self::getDefaultAdapter();
        }

        return $adapter->unserialize($serialized, $options);
    }
}
