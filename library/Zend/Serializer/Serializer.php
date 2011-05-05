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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Serializer;

use Zend\Loader\Broker;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Serializer
{
    /**
     * Broker for loading adapters
     *
     * @var null|Zend\Loader\Broker
     */
    private static $_adapterBroker = null;

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
     * @throws Zend\Serializer\Exception
     */
    public static function factory($adapterName, $opts = array()) 
    {
        if ($adapterName instanceof Adapter) {
            return $adapterName; // $adapterName is already an adapter object
        }

        return self::getAdapterBroker()->load($adapterName, $opts);
    }

    /**
     * Get the adapter broker
     *
     * @return Zend\Loader\Broker
     */
    public static function getAdapterBroker() 
    {
        if (self::$_adapterBroker === null) {
            self::$_adapterBroker = self::_getDefaultAdapterBroker();
        }
        return self::$_adapterBroker;
    }

    /**
     * Change the adapter broker
     *
     * @param  Zend\Loader\Broker $broker
     * @return void
     */
    public static function setAdapterBroker(Broker $broker) 
    {
        self::$_adapterBroker = $broker;
    }
    
    /**
     * Resets the internal adapter broker
     *
     * @return Zend\Loader\Broker
     */
    public static function resetAdapterBroker()
    {
        self::$_adapterBroker = self::_getDefaultAdapterBroker();
        return self::$_adapterBroker;
    }
    
    /**
     * Returns a default adapter broker
     *
     * @return Zend\Loader\Broker
     */
    protected static function _getDefaultAdapterBroker()
    {
        $broker = new AdapterBroker();
        return $broker;
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
