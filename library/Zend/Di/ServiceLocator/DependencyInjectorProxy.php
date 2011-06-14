<?php

namespace Zend\Di\ServiceLocator;

use Zend\Di\DependencyInjector,
    Zend\Di\Exception;

class DependencyInjectorProxy extends DependencyInjector
{
    /**
     * @var DependencyInjector
     */
    protected $di;

    /**
     * @param DependencyInjector $di 
     * @return void
     */
    public function __construct(DependencyInjector $di)
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
        $im = $this->getInstanceManager();

        if ($params) {
            if (($fastHash = $im->hasSharedInstanceWithParameters($name, $params, true))) {
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
        $definition      = $this->getDefinition();
        $instanceManager = $this->getInstanceManager();

        if ($instanceManager->hasAlias($name)) {
            $class = $instanceManager->getClassFromAlias($name);
            $alias = $name;
        } else {
            $class = $name;
            $alias = null;
        }

        if (!$definition->hasClass($class)) {
            $aliasMsg = ($alias) ? '(specified by alias ' . $alias . ') ' : '';
            throw new Exception\ClassNotFoundException('Class ' . $aliasMsg . $class . ' could not be located in provided definition.');
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
            $methodMetadata = array();
            foreach ($injectionMethods as $injectionMethod) {
                $methodMetadata[] = $this->handleInjectionMethodForObject($class, $injectionMethod, $params, $alias);
            }
            $object->setMethods($methodMetadata);
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

    /**
     * Change visibility to public
     * 
     * @param  string $class 
     * @param  string $method 
     * @param  array $userParams 
     * @param  bool $isInstantiator 
     * @param  string $alias 
     * @return array
     */
    public function resolveMethodParameters($class, $method, array $userParams, $isInstantiator, $alias)
    {
        $resolvedParams = array();
        
        $injectionMethodParameters = $this->definition->getInjectionMethodParameters($class, $method);
        
        $computedValueParams = array();
        $computedLookupParams = array();
        
        foreach ($injectionMethodParameters as $name => $type) {
            //$computedValueParams[$name] = null;
            
            // first consult user provided parameters
            if (isset($userParams[$name])) {
                if (is_string($userParams[$name])) {
                    if ($this->instanceManager->hasAlias($userParams[$name])) {
                        $computedLookupParams[$name] = array($userParams[$name], $this->instanceManager->getClassFromAlias($userParams[$name]));    
                    } elseif ($this->definition->hasClass($userParams[$name])) {
                        $computedLookupParams[$name] = array($userParams[$name], $userParams[$name]);
                    } else {
                        $computedValueParams[$name] = $userParams[$name];
                    }
                } else {
                    $computedValueParams[$name] = $userParams[$name];
                }
                continue;
            }
            
            // next consult alias specific properties
            if ($alias && $this->instanceManager->hasProperty($alias, $name)) {
                $computedValueParams[$name] = $this->instanceManager->getProperty($alias, $name);
                continue;
            }
            
            // next consult alias level preferred instances
            if ($alias && $this->instanceManager->hasPreferredInstances($alias)) {
                $pInstances = $this->instanceManager->getPreferredInstances($alias);
                foreach ($pInstances as $pInstance) {
                    $pInstanceClass = ($this->instanceManager->hasAlias($pInstance)) ?
                         $this->instanceManager->getClassFromAlias($pInstance) : $pInstance;
                    if ($pInstanceClass === $type || is_subclass_of($pInstanceClass, $type)) {
                        $computedLookupParams[$name] = array($pInstance, $pInstanceClass);
                        continue;
                    }
                }
            }
            
            // next consult class level preferred instances
            if ($type && $this->instanceManager->hasPreferredInstances($type)) {
                $pInstances = $this->instanceManager->getPreferredInstances($type);
                foreach ($pInstances as $pInstance) {
                    $pInstanceClass = ($this->instanceManager->hasAlias($pInstance)) ?
                         $this->instanceManager->getClassFromAlias($pInstance) : $pInstance;
                    if ($pInstanceClass === $type || is_subclass_of($pInstanceClass, $type)) {
                        $computedLookupParams[$name] = array($pInstance, $pInstanceClass);
                        continue;
                    }
                }
            }
            
            // finally consult alias specific properties
            if ($this->instanceManager->hasProperty($class, $name)) {
                $computedValueParams[$name] = $this->instanceManager->getProperty($class, $name);
                continue;
            }
            
            if ($type) {
                $computedLookupParams[$name] = array($type, $type);
            }
            
        }

        $index = 0;
        foreach ($injectionMethodParameters as $name => $value) {
            
            if (isset($computedValueParams[$name])) {
                $resolvedParams[$index] = $computedValueParams[$name];
            } elseif (isset($computedLookupParams[$name])) {
                if ($isInstantiator && in_array($computedLookupParams[$name][1], $this->currentDependencies)) {
                    throw new Exception\CircularDependencyException("Circular dependency detected: $class depends on $value and viceversa");
                }
                array_push($this->di->currentDependencies, $class);
                $resolvedParams[$index] = $this->get($computedLookupParams[$name][0], $userParams);
                array_pop($this->di->currentDependencies);
            } else {
                throw new Exception\MissingPropertyException('Missing parameter named ' . $name . ' for ' . $class . '::' . $method);
            }
            
            $index++;
        }

        return $resolvedParams;
    }

    /**
     * Methods being forwarded to the proxied object
     */

}
