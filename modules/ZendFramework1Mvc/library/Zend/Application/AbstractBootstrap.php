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
 * @package    Zend_Application
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Application;

use Zend\Loader\LazyLoadingBroker;

/**
 * Abstract base class for bootstrap classes
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractBootstrap
    implements Bootstrapper,
               ResourceBootstrapper
{
    /**
     * @var Zend\Application\Application|\Zend\Application\Bootstrapper
     */
    protected $_application;

    /**
     * @var ResourceBroker
     */
    protected $broker;

    /**
     * @var array Internal resource methods (resource/method pairs)
     */
    protected $_classResources;

    /**
     * @var object Resource container
     */
    protected $_container;

    /**
     * @var string
     */
    protected $_environment;

    /**
     * Flattened (lowercase) option keys used for lookups
     *
     * @var array
     */
    protected $_optionKeys = array();

    /**
     * @var array
     */
    protected $_options = array();

    /**
     * @var \Zend\Loader\LazyLoadingBroker
     */
    protected $_pluginBroker;

    /**
     * @var array Initializers that have been run
     */
    protected $_run = array();

    /**
     * @var array Initializers that have been started but not yet completed (circular dependency detection)
     */
    protected $_started = array();

    /**
     * Constructor
     *
     * Sets application object, initializes options, and prepares list of
     * initializer methods.
     *
     * @param  Zend\Application\Application|\Zend\Application\Bootstrapper $application
     * @return void
     * @throws \Zend\Application\Exception\InvalidArgumentException When invalid application is provided
     */
    public function __construct($application)
    {
        $this->setApplication($application);
        $options = $application->getOptions();
        $this->setOptions($options);
    }

    /**
     * Set class state
     *
     * @param  array $options
     * @return \Zend\Application\AbstractBootstrap
     * @throws \Zend\Application\Exception\InvalidArgumentException
     */
    public function setOptions(array $options)
    {
        $options           = array_change_key_case($options, CASE_LOWER);
        $this->_options    = $this->mergeOptions($this->_options, $options);
        $this->_optionKeys = array_merge($this->_optionKeys, array_keys($options));

        $methods = get_class_methods($this);
        foreach ($methods as $key => $method) {
            $methods[$key] = strtolower($method);
        }

        if (array_key_exists('broker', $options)) {
            $brokerOption = $options['broker'];
            unset($options['broker']);

            if (is_array($brokerOption)) {
                if (!isset($brokerOption['class'])) {
                    throw new Exception\InvalidArgumentException(
                        'Broker option must contain a "class" key; none provided'
                    );
                }
                $brokerClass   = $brokerOption['class'];
                $brokerOptions = $brokerOption['options'] ?: array();
                $brokerOption  = new $brokerClass($brokerOptions);
                $this->setBroker($brokerOption);
                unset($brokerClass, $brokerOptions);
            } else {
                $this->setBroker($brokerOption);
            }
            unset($brokerOption);
        }

        foreach ($options as $key => $value) {
            $method = 'set' . strtolower($key);

            if (in_array($method, $methods)) {
                $this->$method($value);
            } elseif ('resources' == $key) {
                $broker = $this->getBroker();
                foreach ($value as $resource => $resourceOptions) {
                    $broker->registerSpec($resource, $resourceOptions);
                }
            }
        }
        return $this;
    }

    /**
     * Get current options from bootstrap
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Is an option present?
     *
     * @param  string $key
     * @return bool
     */
    public function hasOption($key)
    {
        return in_array(strtolower($key), $this->_optionKeys);
    }

    /**
     * Retrieve a single option
     *
     * @param  string $key
     * @return mixed
     */
    public function getOption($key)
    {
        if ($this->hasOption($key)) {
            $options = $this->getOptions();
            $options = array_change_key_case($options, CASE_LOWER);
            return $options[strtolower($key)];
        }
        return null;
    }

    /**
     * Merge options recursively
     *
     * @param  array $array1
     * @param  mixed $array2
     * @return array
     */
    public function mergeOptions(array $array1, $array2 = null)
    {
        if (is_array($array2)) {
            foreach ($array2 as $key => $val) {
                if (is_array($array2[$key])) {
                    $array1[$key] = (array_key_exists($key, $array1) && is_array($array1[$key]))
                                  ? $this->mergeOptions($array1[$key], $array2[$key])
                                  : $array2[$key];
                } else {
                    $array1[$key] = $val;
                }
            }
        }
        return $array1;
    }

    /**
     * Get class resources (as resource/method pairs)
     *
     * @return array
     */
    public function getClassResources()
    {
        if (null === $this->_classResources) {
            $methodNames = get_class_methods($this);

            $this->_classResources = array();
            foreach ($methodNames as $method) {
                if (5 < strlen($method) && '_init' === substr($method, 0, 5)) {
                    $this->_classResources[strtolower(substr($method, 5))] = $method;
                }
            }
        }

        return $this->_classResources;
    }

    /**
     * Get class resource names
     *
     * @return array
     */
    public function getClassResourceNames()
    {
        $resources = $this->getClassResources();
        return array_keys($resources);
    }

    /**
     * Set resource plugin broker instance
     *
     * @param  ResourceBroker $broker
     * @return AbstractBootstrap
     */
    public function setBroker($broker)
    {
        if (is_string($broker)) {
            if (!class_exists($broker)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Resource broker of class "%s" not found',
                    $broker
                ));
            }
            $broker = new $broker();
        }
        if (!$broker instanceof LazyLoadingBroker
            || !$broker instanceof BootstrapAware
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Broker must implement LazyLoadingBroker and BootstrapAware; received argument of type "%s"',
                (is_object($broker) ? get_class($broker) : gettype($broker))
            ));
        }
        $broker->setBootstrap($this);
        $this->broker = $broker;
        return $this;
    }

    /**
     * Get resource plugin broker instance
     *
     * @todo   Should this allow using a default class name for lazy loading purposes?
     * @return ResourceBroker
     */
    public function getBroker()
    {
        if (null === $this->broker) {
            $this->setBroker(new ResourceBroker());
        }
        return $this->broker;
    }

    /**
     * Set application/parent bootstrap
     *
     * @param  Zend\Application\Application|\Zend\Application\Bootstrapper $application
     * @return \Zend\Application\AbstractBootstrap
     */
    public function setApplication($application)
    {
        if (($application instanceof Application)
            || ($application instanceof Bootstrapper)
        ) {
            if ($application === $this) {
                throw new Exception\InvalidArgumentException('Cannot set application to same object; creates recursion');
            }
            $this->_application = $application;
        } else {
            throw new Exception\InvalidArgumentException('Invalid application provided to bootstrap constructor (received "' . get_class($application) . '" instance)');
        }
        return $this;
    }

    /**
     * Retrieve parent application instance
     *
     * @return Zend\Application\Application|\Zend\Application\Bootstrapper
     */
    public function getApplication()
    {
        return $this->_application;
    }

    /**
     * Retrieve application environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        if (null === $this->_environment) {
            $this->_environment = $this->getApplication()->getEnvironment();
        }
        return $this->_environment;
    }

    /**
     * Set resource container
     *
     * By default, if a resource callback has a non-null return value, this
     * value will be stored in a container using the resource name as the
     * key.
     *
     * Containers must be objects, and must allow setting public properties.
     *
     * @param  object $container
     * @return \Zend\Application\AbstractBootstrap
     */
    public function setContainer($container)
    {
        if (!is_object($container)) {
            throw new Exception\InvalidArgumentException('Resource containers must be objects');
        }
        $this->_container = $container;
        return $this;
    }

    /**
     * Retrieve resource container
     *
     * @return object
     */
    public function getContainer()
    {
        if (null === $this->_container) {
            $this->setContainer(new \Zend\Registry());
        }
        return $this->_container;
    }

    /**
     * Determine if a resource has been stored in the container
     *
     * During bootstrap resource initialization, you may return a value. If
     * you do, it will be stored in the {@link setContainer() container}.
     * You can use this method to determine if a value was stored.
     *
     * @param  string $name
     * @return bool
     */
    public function hasResource($name)
    {
        $resource  = strtolower($name);
        $container = $this->getContainer();
        return isset($container->{$resource});
    }

    /**
     * Retrieve a resource from the container
     *
     * During bootstrap resource initialization, you may return a value. If
     * you do, it will be stored in the {@link setContainer() container}.
     * You can use this method to retrieve that value.
     *
     * If no value was returned, this will return a null value.
     *
     * @param  string $name
     * @return null|mixed
     */
    public function getResource($name)
    {
        $resource  = strtolower($name);
        $container = $this->getContainer();
        if ($this->hasResource($resource)) {
            return $container->{$resource};
        }
        return null;
    }

    /**
     * Implement PHP's magic to retrieve a resource
     * in the bootstrap
     *
     * @param string $prop
     * @return null|mixed
     */
    public function __get($prop)
    {
        return $this->getResource($prop);
    }

    /**
     * Implement PHP's magic to ask for the
     * existence of a resource in the bootstrap
     *
     * @param string $prop
     * @return bool
     */
    public function __isset($prop)
    {
        return $this->hasResource($prop);
    }

    /**
     * Bootstrap individual, all, or multiple resources
     *
     * Marked as final to prevent issues when subclassing and naming the
     * child class 'Bootstrap' (in which case, overriding this method
     * would result in it being treated as a constructor).
     *
     * If you need to override this functionality, override the
     * {@link _bootstrap()} method.
     *
     * @param  null|string|array $resource
     * @return \Zend\Application\AbstractBootstrap
     * @throws \Zend\Application\Exception\InvalidArgumentException When invalid argument was passed
     */
    final public function bootstrap($resource = null)
    {
        $this->_bootstrap($resource);
        return $this;
    }

    /**
     * Overloading: intercept calls to bootstrap<resourcename>() methods
     *
     * @param  string $method
     * @param  array  $args
     * @return void
     * @throws \Zend\Application\Exception\BadMethodCallException On invalid method name
     */
    public function __call($method, $args)
    {
        if (9 < strlen($method) && 'bootstrap' === substr($method, 0, 9)) {
            $resource = substr($method, 9);
            return $this->bootstrap($resource);
        }

        throw new Exception\BadMethodCallException('Invalid method "' . $method . '"');
    }

    /**
     * Bootstrap implementation
     *
     * This method may be overridden to provide custom bootstrapping logic.
     * It is the sole method called by {@link bootstrap()}.
     *
     * @param  null|string|array $resource
     * @return void
     * @throws \Zend\Application\Exception\InvalidArgumentException When invalid argument was passed
     */
    protected function _bootstrap($resource = null)
    {
        if (null === $resource) {
            foreach ($this->getClassResourceNames() as $resource) {
                $this->_executeResource($resource);
            }
            if(!$this instanceof Module\Bootstrap)
                foreach ($this->getBroker()->getRegisteredPlugins() as $resource) {
                    $this->_executeResource($resource);
                }
        } elseif (is_string($resource)) {
            $this->_executeResource($resource);
        } elseif (is_array($resource)) {
            foreach ($resource as $r) {
                $this->_executeResource($r);
            }
        } else {
            throw new Exception\InvalidArgumentException('Invalid argument passed to ' . __METHOD__);
        }
    }

    /**
     * Execute a resource
     *
     * Checks to see if the resource has already been run. If not, it searches
     * first to see if a local method matches the resource, and executes that.
     * If not, it checks to see if a plugin resource matches, and executes that
     * if found.
     *
     * Finally, if not found, it throws an exception.
     *
     * @param  string $resource
     * @return void
     * @throws \Zend\Application\Exception\InvalidArgumentException When resource not found
     */
    protected function _executeResource($resource)
    {
        $resourceName = strtolower($resource);

        if (in_array($resourceName, $this->_run)) {
            return;
        }

        if (isset($this->_started[$resourceName]) && $this->_started[$resourceName]) {
            throw new Exception\RuntimeException('Circular resource dependency detected');
        }

        $classResources = $this->getClassResources();
        if (array_key_exists($resourceName, $classResources)) {
            $this->_started[$resourceName] = true;
            $method = $classResources[$resourceName];
            $return = $this->$method();
            unset($this->_started[$resourceName]);
            $this->_markRun($resourceName);

            if (null !== $return) {
                $this->getContainer()->{$resourceName} = $return;
            }

            return;
        }

        $broker = $this->getBroker();

        if ($broker->isRun($resourceName)) {
           return;
        }

        if ($broker->hasPlugin($resource)) {
            $this->_started[$resourceName] = true;
            $broker->markRun($resourceName);
            $plugin = $broker->load($resourceName);
            $return = $plugin->init();
            unset($this->_started[$resourceName]);

            if (null !== $return) {
                $this->getContainer()->{$resourceName} = $return;
            }

            return;
        }

        throw new Exception\InvalidArgumentException('Resource matching "' . $resource . '" not found');
    }

    /**
     * Mark a resource as having run
     *
     * @param  string $resource
     * @return void
     */
    protected function _markRun($resource)
    {
        if (!in_array($resource, $this->_run)) {
            $this->_run[] = $resource;
        }
    }
}
