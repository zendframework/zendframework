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
        $this->definition      = $di->getDefinition();
        $this->instanceManager = $di->getInstanceManager();
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
        array_push($this->instanceContext, array('GET', $name));
        
        $im = $this->getInstanceManager();

        if ($params) {
            if (($fastHash = $im->hasSharedInstanceWithParameters($name, $params, true))) {
                array_pop($this->instanceContext);
                return $im->getSharedInstanceWithParameters(null, array(), $fastHash);
            }
        } else {
            if ($im->hasSharedInstance($name, $params)) {
                array_pop($this->instanceContext);
                return $im->getSharedInstance($name, $params);
            }
        }
        $instance = $this->newInstance($name, $params);
        array_pop($this->instanceContext);
        return $instance;
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
        if ($this->di->definition->hasInjectionMethod($class, '__construct')) {
            $callParameters = $this->resolveMethodParameters(
                $class, '__construct', $params, true, $alias
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
        if ($this->di->definition->hasInjectionMethod($class, $method)) {
            $callParameters = $this->resolveMethodParameters($class, $method, $params, true, $alias);
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
    public function handleInjectionMethodForObject($class, $method, $params, $alias)
    {
        $callParameters = $this->resolveMethodParameters($class, $method, $params, false, $alias);
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
        // localize dependencies (this also will serve as poka-yoke)
        $definition      = $this->getDefinition();
        $instanceManager = $this->getInstanceManager();

        if ($instanceManager->hasAlias($name)) {
            $class = $instanceManager->getClassFromAlias($name);
            $alias = $name;
        } else {
            $class = $name;
            $alias = null;
        }

        array_push($this->instanceContext, array('NEW', $class, $alias));
        
        if (!$definition->hasClass($class)) {
            $aliasMsg = ($alias) ? '(specified by alias ' . $alias . ') ' : '';
            throw new Exception\ClassNotFoundException(
                'Class ' . $aliasMsg . $class . ' could not be located in provided definition.'
            );
        }

        $instantiator     = $definition->getInstantiator($class);
        $injectionMethods = $definition->getInjectionMethods($class);

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
                $this->handleInjectionMethodForObject($object, $injectionMethod, $params, $alias);
            }
        }

        // Methods for which we have configuration
        $iConfig = ($instanceManager->hasAlias($alias) && $instanceManager->hasConfiguration($alias))
            ? $instanceManager->getConfiguration($alias)
            : $instanceManager->getConfiguration(get_class($object));

        if ($iConfig['methods']) {
            foreach ($iConfig['methods'] as $iConfigMethod => $iConfigMethodParams) {
                // skip methods processed by handleInjectionMethodForObject
                if (in_array($iConfigMethod, $injectionMethods) && $iConfigMethod !== '__construct') continue; 
                call_user_func_array(array($object, $iConfigMethod), array_values($iConfigMethodParams));
            }
        }
        
        if ($isShared) {
            if ($params) {
                $this->getInstanceManager()->addSharedInstanceWithParameters($object, $name, $params);
            } else {
                $this->getInstanceManager()->addSharedInstance($object, $name);
            }
        }
        
        array_pop($this->instanceContext);
        return $object;
    }
}
