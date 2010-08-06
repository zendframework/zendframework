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
 * @package    Zend_Serializer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Serializer;

use Zend\Loader\PluginLoader,
    Zend\Loader\ShortNameLocater;

/**
 * @uses       Zend\Loader\PluginLoader
 * @uses       Zend\Serializer\Exception
 * @category   Zend
 * @package    Zend_Serializer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Serializer
{
    /**
     * Plugin loader to load adapter.
     *
     * @var null|Zend\Loader\ShortNameLocater
     */
    private static $_adapterLoader = null;

    /**
     * The default adapter.
     *
     * @var string|Zend\Serializer\Adapter
     */
    protected static $_defaultAdapter = 'PhpSerialize';

    /**
     * Create a serializer adapter instance.
     *
     * @param string|Zend\Serializer\Adapter $adapterName Name of the adapter class
     * @param array |Zend\Config\Config $opts Serializer options
     * @return Zend\Serializer\Adapter
     */
    public static function factory($adapterName, $opts = array()) 
    {
        if ($adapterName instanceof Adapter) {
            return $adapterName; // $adapterName is already an adapter object
        }

        $adapterLoader = self::getAdapterLoader();
        try {
            $adapterClass = $adapterLoader->load($adapterName);
        } catch (\Exception $e) {
            throw new Exception('Can\'t load serializer adapter "'.$adapterName.'"', 0, $e);
        }

        // ZF-8842:
        // check that the loaded class implements Zend_Serializer_Adapter_AdapterInterface without execute code
        if (!in_array('Zend\\Serializer\\Adapter', class_implements($adapterClass))) {
            throw new Exception('The serializer adapter class "'.$adapterClass.'" must implement Zend\\Serializer\\Adapter');
        }

        return new $adapterClass($opts);
    }

    /**
     * Get the adapter plugin loader.
     *
     * @return Zend\Loader\ShortNameLocater
     */
    public static function getAdapterLoader() 
    {
        if (self::$_adapterLoader === null) {
            self::$_adapterLoader = self::_getDefaultAdapterLoader();
        }
        return self::$_adapterLoader;
    }

    /**
     * Change the adapter plugin load.
     *
     * @param  Zend\Loader\ShortNameLocater $pluginLoader
     * @return void
     */
    public static function setAdapterLoader(ShortNameLocater $pluginLoader) 
    {
        self::$_adapterLoader = $pluginLoader;
    }
    
    /**
     * Resets the internal adapter plugin loader
     *
     * @return Zend\Loader\ShortNameLocater
     */
    public static function resetAdapterLoader()
    {
        self::$_adapterLoader = self::_getDefaultAdapterLoader();
        return self::$_adapterLoader;
    }
    
    /**
     * Returns a default adapter plugin loader
     *
     * @return Zend\Loader\PluginLoader
     */
    protected static function _getDefaultAdapterLoader()
    {
        $loader = new PluginLoader();
        $loader->addPrefixPath('Zend\\Serializer\\Adapter\\', __DIR__ . '/Serializer/Adapter');
        return $loader;
    }

    /**
     * Change the default adapter.
     *
     * @param string|Zend\Serializer\Adapter $adapter
     * @param array|Zend\Config\Config $options
     */
    public static function setDefaultAdapter($adapter, $options = array()) 
    {
        self::$_defaultAdapter = self::factory($adapter, $options);
    }

    /**
     * Get the default adapter.
     *
     * @return Zend\Serializer\Adapter
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
     * @throws Zend\Serializer\Exception
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
     * @throws Zend\Serializer\Exception
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
