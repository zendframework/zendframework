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
 * @package    Zend_Mvc_Router
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Router;

use Zend\Loader\Broker,
    Zend\Loader\PluginClassLoader,
    Zend\Loader\ShortNameLocator,
    Zend\Mvc\Router\Exception;

/**
 * Route broker.
 *
 * @category   Zend
 * @package    Zend_Mvc_Router
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RouteBroker implements Broker
{
    /**
     * Default class loader to utilize with this broker.
     * 
     * @var string
     */
    protected $defaultClassLoader = 'Zend\Loader\PluginClassLoader';

    /**
     * Plugin class loader used by this instance.
     * 
     * @var ShortNameLocator 
     */
    protected $classLoader;

    /**
     * Constructor.
     *
     * Allow configuration via options; see {@link setOptions()} for details.
     * 
     * @param  null|array|Traversable $options 
     * @return void
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Configure route broker.
     * 
     * @param  mixed $options 
     * @return PluginBroker
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof \Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected an array or Traversable; received "%s"',
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'class_loader':
                    if (is_string($value)) {
                        if (!class_exists($value)) {
                            throw new Exception\RuntimeException(sprintf(
                                'Unknown class "%s" provided as class loader option',
                                $value
                            ));
                        }
                        
                        $value = new $value;
                    }
                    
                    if ($value instanceof ShortNameLocator) {
                        $this->setClassLoader($value);
                        break;
                    } 

                    if (!is_array($value) && !$value instanceof \Traversable) {
                        throw new Exception\RuntimeException(sprintf(
                            'Option passed for class loader (%s) is of an unknown type',
                            (is_object($value) ? get_class($value) : gettype($value))
                        ));
                    }

                    $class   = false;
                    $options = null;
                    
                    foreach ($value as $k => $v) {
                        switch (strtolower($k)) {
                            case 'class':
                                $class = $v;
                                break;
                            case 'options':
                                $options = $v;
                                break;
                            default:
                                break;
                        }
                    }
                    
                    if ($class) {
                        $loader = new $class($options);
                        $this->setClassLoader($loader);
                    }
                    break;
                    
                default:
                    // ignore unknown options
                    break;
            }
        }

        return $this;
    }

    /**
     * load(): defined by Broker interface.
     * 
     * @see    Broker::load()
     * @param  string $route
     * @param  array  $options
     * @return Route
     */
    public function load($route, array $options = array())
    {
        $routeName = strtolower($route);

        if (class_exists($route)) {
            // Allow loading fully-qualified class names via the broker
            $class = $route;
        } else {
            // Unqualified class names are then passed to the class loader
            $class = $this->getClassLoader()->load($route);

            if (empty($class)) {
                throw new Exception\RuntimeException('Unable to locate class associated with "' . $routeName . '"');
            }
        }

        return $class::factory($options);
    }

    /**
     * getPlugins(): defined by Broker interface.
     * 
     * Not required in the RouteBroker.
     * 
     * @see    Broker::getPlugins()
     * @return array
     */
    public function getPlugins()
    {
    }

    /**
     * isLoaded(): defined by Broker interface.
     * 
     * Not required in the RouteBroker.
     * 
     * @see    Broker::isLoaded()
     * @param  string $name 
     * @return bool
     */
    public function isLoaded($name)
    {
    }

    /**
     * register(): defined by Broker interface.
     * 
     * Not required in the RouteBroker.
     * 
     * @see    Broker::register()
     * @param  string $name 
     * @param  mixed $plugin 
     * @return PluginBroker
     */
    public function register($name, $plugin)
    {
    }

    /**
     * unregister(): defined by Broker interface.
     * 
     * Not required in the RouteBroker.
     * 
     * @see    Broker::unregister()
     * @param  string $name 
     * @return bool
     */
    public function unregister($name)
    {
    }

    /**
     * setClassLoader(): defined by Broker interface.
     * 
     * @see    Broker::setClassLoader()
     * @param  ShortNameLocator $loader 
     * @return PluginBroker
     */
    public function setClassLoader(ShortNameLocator $loader)
    {
        if (!$loader instanceof PluginClassLoader) {
            throw new Exception\InvalidArgumentException('Expected instance of PluginClassLoader');
        }
        
        $this->classLoader = $loader;
        
        return $this;
    }

    /**
     * getClassLoader(): defined by Broker interface.
     * 
     * @see    Broker::getClassLoader()
     * @return ShortNameLocator
     */
    public function getClassLoader()
    {
        if (null === $this->classLoader) {
            $loaderClass = $this->defaultClassLoader;
            $this->setClassLoader(new $loaderClass());
        }
        
        return $this->classLoader;
    }
}
