<?php

namespace Zend\Di\ServiceLocator;

use Zend\Di\Di,
    Zend\Di\Exception;

class DependencyInjectorProxy extends Di
{
    /**
     * @var DependencyInjector
     */
    protected $di;

    /**
     * @param DependencyInjector $di
     * @return void
     */
    public function __construct(Di $di)
    {
        $this->di              = $di;
        $this->definitions     = $di->definitions();
        $this->instanceManager = $di->instanceManager();
    }

    /**
     * Methods with functionality overrides
     */

    /**
     * Override, as we want it to use the functionality defined in the proxy
     *
     * @param  string $name
     * @param  array $params
     * @return GeneratorInstance
     */
    public function get($name, array $params = array())
    {
        $im = $this->instanceManager();

        if ($params) {
            $fastHash = $im->hasSharedInstanceWithParameters($name, $params, true);
            if ($fastHash) {
                return $im->getSharedInstanceWithParameters(null, array(), $fastHash);
            }
        } else {
            if ($im->hasSharedInstance($name, $params)) {
                return $im->getSharedInstance($name, $params);
            }
        }
        return $this->newInstance($name, $params);
    }

    /**
     * Override createInstanceViaConstructor method from injector
     *
     * Returns code generation artifacts.
     *
     * @param  string $class
     * @param  null|array $params
     * @param  null|string $alias
     * @return GeneratorInstance
     */
    public function createInstanceViaConstructor($class, $params, $alias = null)
    {
        $callParameters = array();
        if ($this->di->definitions->hasMethod($class, '__construct')
            && (count($this->di->definitions->getMethodParameters($class, '__construct')) > 0)
        ) {
            $callParameters = $this->resolveMethodParameters(
                $class, '__construct', $params, true, $alias, true
            );
        }
        return new GeneratorInstance($class, '__construct', $callParameters);
    }

    /**
     * Override instance creation via callback
     *
     * @param  callback $callback
     * @param  null|array $params
     * @return GeneratorInstance
     */
    public function createInstanceViaCallback($callback, $params, $alias = null)
    {
        if (!is_callable($callback)) {
            throw new Exception\InvalidCallbackException('An invalid constructor callback was provided');
        }

        if (!is_array($callback) || is_object($callback[0])) {
            throw new Exception\InvalidCallbackException('For purposes of service locator generation, constructor callbacks must refer to static methods only');
        }

        $class  = $callback[0];
        $method = $callback[1];

        $callParameters = array();
        if ($this->di->definitions->hasMethod($class, $method)) {
            $callParameters = $this->resolveMethodParameters($class, $method, $params, true, $alias, true);
        }

        return new GeneratorInstance(null, $callback, $callParameters);
    }

    /**
     * Retrieve metadata for injectible methods
     *
     * @param  string $class
     * @param  string $method
     * @param  array $params
     * @param  string $alias
     * @return array
     */
    public function handleInjectionMethodForObject($class, $method, $params, $alias, $isRequired)
    {
        $callParameters = $this->resolveMethodParameters($class, $method, $params, false, $alias, $isRequired);
        return array(
            'method' => $method,
            'params' => $callParameters,
        );
    }

    /**
     * Override new instance creation
     *
     * @param  string $name
     * @param  array $params
     * @param  bool $isShared
     * @return GeneratorInstance
     */
    public function newInstance($name, array $params = array(), $isShared = true)
    {
        $definition      = $this->definitions();
        $instanceManager = $this->instanceManager();

        if ($instanceManager->hasAlias($name)) {
            $class = $instanceManager->getClassFromAlias($name);
            $alias = $name;
        } else {
            $class = $name;
            $alias = null;
        }

        if (!$definition->hasClass($class)) {
            $aliasMsg = ($alias) ? '(specified by alias ' . $alias . ') ' : '';
            throw new Exception\ClassNotFoundException(
                'Class ' . $aliasMsg . $class . ' could not be located in provided definitions.'
            );
        }

        $instantiator     = $definition->getInstantiator($class);
        $injectionMethods = $definition->getMethods($class);

        if ($instantiator === '__construct') {
            $object = $this->createInstanceViaConstructor($class, $params, $alias);
            if (in_array('__construct', $injectionMethods)) {
                unset($injectionMethods[array_search('__construct', $injectionMethods)]);
            }
        } elseif (is_callable($instantiator)) {
            $object = $this->createInstanceViaCallback($instantiator, $params, $alias);
            $object->setName($class);
        } else {
            throw new Exception\RuntimeException('Invalid instantiator');
        }

        if ($injectionMethods) {
            foreach ($injectionMethods as $injectionMethod) {
                $methodMetadata[] = $this->handleInjectionMethodForObject($class, $injectionMethod, $params, $alias);
            }
        }

        if ($isShared) {
            if ($params) {
                $instanceManager->addSharedInstanceWithParameters($object, $name, $params);
            } else {
                $instanceManager->addSharedInstance($object, $name);
            }
        }

        return $object;
    }
}
