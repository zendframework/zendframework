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
    Zend\Stdlib\IteratorToArray;

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
     * @return Storage\Adapter
     * @throws Exception\InvalidArgumentException
     */
    public static function factory($cfg)
    {
        if ($cfg instanceof Traversable) {
            $cfg = IteratorToArray::convert($cfg);
        }

        if (!is_array($cfg)) {
            throw new Exception\InvalidArgumentException(
                'The factory needs an associative array '
                . 'or a Traversable object as an argument'
            );
        }

        // instantiate the adapter
        if (!isset($cfg['adapter'])) {
            throw new Exception\InvalidArgumentException(
                'Missing "adapter"'
            );
        } elseif (is_array($cfg['adapter'])) {
            if (!isset($cfg['adapter']['name'])) {
                throw new Exception\InvalidArgumentException(
                    'Missing "adapter.name"'
                );
            }

            $name    = $cfg['adapter']['name'];
            $options = isset($cfg['adapter']['options'])
                     ? $cfg['adapter']['options'] : array();
            $adapter = static::adapterFactory($name, $options);
        } else {
            $adapter = static::adapterFactory($cfg['adapter']);
        }

        // add plugins
        if (isset($cfg['plugins'])) {
            if (!is_array($cfg['plugins'])) {
                throw new Exception\InvalidArgumentException(
                    'Plugins needs to be an array'
                );
            }

            foreach ($cfg['plugins'] as $k => $v) {
                if (is_string($k)) {
                    $name = $k;
                    if (!is_array($v)) {
                        throw new Exception\InvalidArgumentException(
                            "'plugins.{$k}' needs to be an array"
                        );
                    }
                    $options = $v;
                } elseif (is_array($v)) {
                    if (!isset($v['name'])) {
                        throw new Exception\InvalidArgumentException("Invalid plugins[{$k}] or missing plugins[{$k}].name");
                    }
                    $name = (string) $v['name'];
                    if (isset($v['options'])) {
                        $options = $v['options'];
                    } else {
                        $options = array();
                    }
                } else {
                    $name    = $v;
                    $options = array();
                }

                $plugin = static::pluginFactory($name, $options);
                $adapter->addPlugin($plugin);
            }
        }

        // set adapter or plugin options
        if (isset($cfg['options'])) {
            if (!is_array($cfg['options'])
                && !$cfg['options'] instanceof Traversable
            ) {
                throw new Exception\InvalidArgumentException(
                    'Options needs to be an array or Traversable object'
                );
            }

            // Options at the top-level should be *merged* with existing options
            $options = $adapter->getOptions();
            foreach ($cfg['options'] as $key => $value) {
                $options->$key = $value;
            }
        }

        return $adapter;
    }

    /**
     * Instantiate a storage adapter
     *
     * @param  string|Storage\Adapter $adapterName
     * @param  null|array|Traversable|Storage\Adapter\AdapterOptions $options
     * @return Storage\Adapter
     * @throws Exception\RuntimeException
     */
    public static function adapterFactory($adapterName, $options = null)
    {
        if ($adapterName instanceof Storage\Adapter) {
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
        static::$adapterBroker = new Storage\AdapterBroker();
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
        static::$pluginBroker = new Storage\PluginBroker();
    }
}
