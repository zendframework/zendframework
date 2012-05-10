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

namespace Zend\Cache;

use Traversable,
    Zend\Loader\Broker,
    Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StorageFactory
{
    /**
     * Broker for loading adapters
     *
     * @var null|Broker
     */
    protected static $adapterBroker = null;

    /**
     * Broker for loading plugins
     *
     * @var null|Broker
     */
    protected static $pluginBroker = null;

    /**
     * The storage factory
     * This can instantiate storage adapters and plugins.
     *
     * @param array|Traversable $cfg
     * @return Storage\Adapter\AdapterInterface
     * @throws Exception\InvalidArgumentException
     */
    public static function factory($cfg)
    {
        if ($cfg instanceof Traversable) {
            $cfg = ArrayUtils::iteratorToArray($cfg);
        }

        if (!is_array($cfg)) {
            throw new Exception\InvalidArgumentException(
                'The factory needs an associative array '
                . 'or a Traversable object as an argument'
            );
        }

        // instantiate the adapter
        if (!isset($cfg['adapter'])) {
            throw new Exception\InvalidArgumentException('Missing "adapter"');
        }
        $adapterName    = $cfg['adapter'];
        $adapterOptions = null;
        if (is_array($cfg['adapter'])) {
            if (!isset($cfg['adapter']['name'])) {
                throw new Exception\InvalidArgumentException('Missing "adapter.name"');
            }

            $adapterName    = $cfg['adapter']['name'];
            $adapterOptions = isset($cfg['adapter']['options']) ? $cfg['adapter']['options'] : null;
        }
        if ($adapterOptions && isset($cfg['options'])) {
            $adapterOptions = array_merge($adapterOptions, $cfg['options']);
        }

        $adapter = static::adapterFactory((string)$adapterName, $adapterOptions);

        // add plugins
        if (isset($cfg['plugins'])) {
            if (!is_array($cfg['plugins'])) {
                throw new Exception\InvalidArgumentException(
                    'Plugins needs to be an array'
                );
            }

            foreach ($cfg['plugins'] as $k => $v) {
                $pluginPrio = 1; // default priority

                if (is_string($k)) {
                    if (!is_array($v)) {
                        throw new Exception\InvalidArgumentException(
                            "'plugins.{$k}' needs to be an array"
                        );
                    }
                    $pluginName    = $k;
                    $pluginOptions = $v;
                } elseif (is_array($v)) {
                    if (!isset($v['name'])) {
                        throw new Exception\InvalidArgumentException("Invalid plugins[{$k}] or missing plugins[{$k}].name");
                    }
                    $pluginName = (string) $v['name'];

                    if (isset($v['options'])) {
                        $pluginOptions = $v['options'];
                    } else {
                        $pluginOptions = array();
                    }

                    if (isset($v['priority'])) {
                        $pluginPrio = $v['priority'];
                    }
                } else {
                    $pluginName    = $v;
                    $pluginOptions = array();
                }

                $plugin = static::pluginFactory($pluginName, $pluginOptions);
                $adapter->addPlugin($plugin, $pluginPrio);
            }
        }

        return $adapter;
    }

    /**
     * Instantiate a storage adapter
     *
     * @param  string|Storage\Adapter\AdapterInterface               $adapterName
     * @param  null|array|Traversable|Storage\Adapter\AdapterOptions $options
     * @return Storage\Adapter\AdapterInterface
     * @throws Exception\RuntimeException
     */
    public static function adapterFactory($adapterName, $options = null)
    {
        if ($adapterName instanceof Storage\Adapter\AdapterInterface) {
            // $adapterName is already an adapter object
            $adapter = $adapterName;
        } else {
            $adapter = static::getAdapterBroker()->load($adapterName);
        }

        if ($options !== null) {
            $adapter->setOptions($options);
        }

        return $adapter;
    }

    /**
     * Get the adapter broker
     *
     * @return Broker
     */
    public static function getAdapterBroker()
    {
        if (static::$adapterBroker === null) {
            static::$adapterBroker = new Storage\AdapterBroker();
            static::$adapterBroker->setRegisterPluginsOnLoad(false);
        }
        return static::$adapterBroker;
    }

    /**
     * Change the adapter broker
     *
     * @param  Broker $broker
     * @return void
     */
    public static function setAdapterBroker(Broker $broker)
    {
        static::$adapterBroker = $broker;
    }

    /**
     * Resets the internal adapter broker
     *
     * @return void
     */
    public static function resetAdapterBroker()
    {
        static::$adapterBroker = null;
    }

    /**
     * Instantiate a storage plugin
     *
     * @param string|Storage\Plugin $pluginName
     * @param array|Traversable|Storage\Plugin\PluginOptions $options
     * @return Storage\Plugin
     * @throws Exception\RuntimeException
     */
    public static function pluginFactory($pluginName, $options = array())
    {
        if ($pluginName instanceof Storage\Plugin) {
            // $pluginName is already an plugin object
            $plugin = $pluginName;
        } else {
            $plugin = static::getPluginBroker()->load($pluginName);
        }

        if (!$options instanceof Storage\Plugin\PluginOptions) {
            $options = new Storage\Plugin\PluginOptions($options);
        }

        $plugin->setOptions($options);

        return $plugin;
    }

    /**
     * Get the plugin broker
     *
     * @return Broker
     */
    public static function getPluginBroker()
    {
        if (static::$pluginBroker === null) {
            static::$pluginBroker = new Storage\PluginBroker();
            static::$pluginBroker->setRegisterPluginsOnLoad(false);
        }
        return static::$pluginBroker;
    }

    /**
     * Change the plugin broker
     *
     * @param  Broker $broker
     * @return void
     */
    public static function setPluginBroker(Broker $broker)
    {
        static::$pluginBroker = $broker;
    }

    /**
     * Resets the internal plugin broker
     *
     * @return void
     */
    public static function resetPluginBroker()
    {
        static::$pluginBroker = null;
    }
}
