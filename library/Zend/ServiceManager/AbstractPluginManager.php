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
 * @package    Zend_ServiceManager
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\ServiceManager;

use Zend\Code\Reflection\ClassReflection;

/**
 * ServiceManager implementation for managing plugins
 *
 * Automatically registers an initializer which should be used to verify that
 * a plugin instance is of a valid type. Additionally, allows plugins to accept
 * an array of options for the constructor, which can be used to configure
 * the plugin when retrieved. Finally, enables the allowOverride property by
 * default to allow registering factories, aliases, and invokables to take
 * the place of those provided by the implementing class.
 *
 * @category   Zend
 * @package    Zend_ServiceManager
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractPluginManager extends ServiceManager implements ServiceLocatorAwareInterface
{
    /**
     * Allow overriding by default
     *
     * @var bool
     */
    protected $allowOverride   = true;

    /**
     * @var mixed Options to use when creating an instance
     */
    protected $creationOptions = null;

    /**
     * The main service locator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Constructor
     *
     * Add a default initializer to ensure the plugin is valid after instance
     * creation.
     *
     * @param  null|ConfigurationInterface $configuration
     * @return void
     */
    public function __construct(ConfigurationInterface $configuration = null)
    {
        parent::__construct($configuration);
        $self = $this;
        $this->addInitializer(function ($instance) use ($self) {
            if ($instance instanceof ServiceManagerAwareInterface) {
                $instance->setServiceManager($self);
            }
        });
    }

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    abstract public function validatePlugin($plugin);

    /**
     * Retrieve a service from the manager by name
     *
     * Allows passing an array of options to use when creating the instance.
     * createFromInvokable() will use these and pass them to the instance
     * constructor if not null and a non-empty array.
     *
     * @param  string $name
     * @param  array $options
     * @param  bool $usePeeringServiceManagers
     * @return object
     */
    public function get($name, $options = array(), $usePeeringServiceManagers = true)
    {
        // Allow specifying a class name directly; registers as an invokable class
        if (!$this->has($name) && class_exists($name)) {
            $this->setInvokableClass($name, $name);
        }

        $this->creationOptions = $options;
        $instance = parent::get($name, $usePeeringServiceManagers);
        $this->creationOptions = null;
        $this->validatePlugin($instance);
        return $instance;
    }

    /**
     * Register a service with the locator.
     *
     * Validates that the service object via validatePlugin() prior to
     * attempting to register it.
     *
     * @param  string $name
     * @param  mixed $service
     * @param  bool $shared
     * @return AbstractPluginManager
     * @throws Exception\InvalidServiceNameException
     */
    public function setService($name, $service, $shared = true)
    {
        if ($service) {
            $this->validatePlugin($service);
        }
        parent::setService($name, $service, $shared);
        return $this;
    }

    /**
     * Set the main service locator so factories can have access to it to pull deps
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return AbstractPluginManager
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get the main plugin manager. Useful for fetching dependencies from within factories.
     *
     * @return mixed
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Attempt to create an instance via an invokable class
     *
     * Overrides parent implementation by passing $creationOptions to the
     * constructor, if non-null.
     *
     * @param  string $canonicalName
     * @param  string $requestedName
     * @return null|\stdClass
     * @throws Exception\ServiceNotCreatedException If resolved class does not exist
     */
    protected function createFromInvokable($canonicalName, $requestedName)
    {
        $invokable = $this->invokableClasses[$canonicalName];
        if (!class_exists($invokable)) {
            throw new Exception\ServiceNotCreatedException(sprintf(
                '%s: failed retrieving "%s%s" via invokable class "%s"; class does not exist',
                __METHOD__,
                $canonicalName,
                ($requestedName ? '(alias: ' . $requestedName . ')' : ''),
                $canonicalName
            ));
        }

        if (null === $this->creationOptions
            || (is_array($this->creationOptions) && empty($this->creationOptions))
        ) {
            $instance = new $invokable();
        } else {
            $instance = new $invokable($this->creationOptions);
        }

        return $instance;
    }

    /**
     * Determine if a class implements a given interface
     *
     * For PHP versions >= 5.3.7, uses is_subclass_of; otherwise, uses
     * reflection to determine the interfaces implemented.
     *
     * @param  string $class
     * @param  string $type
     * @return bool
     */
    protected function isSubclassOf($class, $type)
    {
        if (version_compare(PHP_VERSION, '5.3.7', 'gte')) {
            return is_subclass_of($class, $type);
        }

        $r = new ClassReflection($class);
        $interfaces = $r->getInterfaceNames();
        return (in_array($type, $interfaces));
    }
}
