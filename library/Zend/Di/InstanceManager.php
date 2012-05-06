<?php

namespace Zend\Di;

class InstanceManager /* implements InstanceManagerInterface */
{
    /**
     * Array of shared instances
     * @var array
     */
    protected $sharedInstances = array();

    /**
     * Array of shared instances with params
     * @var array
     */
    protected $sharedInstancesWithParams = array('hashShort' => array(), 'hashLong' => array());
    
    /**
     * Array of class aliases
     * @var array key: alias, value: class
     */
    protected $aliases = array();
    
    /**
     * The template to use for housing configuration information
     * @var array 
     */
    protected $configurationTemplate = array(
        /** 
         * alias|class => alias|class
         * interface|abstract => alias|class|object
         * name => value
         */
        'parameters' => array(),
        /**
         * injection type => array of ordered method params
         */
        'injections' => array(),
        /**
         * alias|class => bool
         */
        'shared' => true
        );

    /**
     * An array of instance configuration data
     * @var array
     */
    protected $configurations = array();
    
    /**
     * An array of globally preferred implementations for interfaces/abstracts
     * @var array
     */
    protected $typePreferences = array();
    
    /**
     * Does this instance manager have this shared instance
     * @return bool
     */
    public function hasSharedInstance($classOrAlias)
    {
        return isset($this->sharedInstances[$classOrAlias]);
    }
    
    /**
     * getSharedInstance()
     */
    public function getSharedInstance($classOrAlias)
    {
        return $this->sharedInstances[$classOrAlias];
    }
    
    /**
     * addSharedInstance()
     */
    public function addSharedInstance($instance, $classOrAlias)
    {
        if (!is_object($instance)) {
            throw new Exception\InvalidArgumentException('This method requires an object to be shared');
        }

        $this->sharedInstances[$classOrAlias] = $instance;
    }

    /**
     * hasSharedInstanceWithParameters()
     *
     * @param string $classOrAlias
     * @param array $params
     * @param bool $returnFashHashLookupKey
     * @return bool|string
     */
    public function hasSharedInstanceWithParameters($classOrAlias, array $params, $returnFashHashLookupKey = false)
    {
        ksort($params);
        $hashKey = $this->createHashForKeys($classOrAlias, array_keys($params));
        if (isset($this->sharedInstancesWithParams['hashShort'][$hashKey])) {
            $hashValue = $this->createHashForValues($classOrAlias, $params);
            if (isset($this->sharedInstancesWithParams['hashLong'][$hashKey . '/' . $hashValue])) {
                return ($returnFashHashLookupKey) ? $hashKey . '/' . $hashValue : true;
            }
        }
        return false;
    }

    /**
     * addSharedInstanceWithParameters()
     *
     * @param object $instance
     * @param string $classOrAlias
     * @param array $params
     * @return void
     */
    public function addSharedInstanceWithParameters($instance, $classOrAlias, array $params)
    {
        ksort($params);
        $hashKey = $this->createHashForKeys($classOrAlias, array_keys($params));
        $hashValue = $this->createHashForValues($classOrAlias, $params);
        
        if (!isset($this->sharedInstancesWithParams[$hashKey]) 
            || !is_array($this->sharedInstancesWithParams[$hashKey])) {
            $this->sharedInstancesWithParams[$hashKey] = array();
        }

        $this->sharedInstancesWithParams['hashShort'][$hashKey] = true;
        $this->sharedInstancesWithParams['hashLong'][$hashKey . '/' . $hashValue] = $instance;
    }
    
    public function getSharedInstanceWithParameters($classOrAlias, array $params, $fastHashFromHasLookup = null)
    {
        if ($fastHashFromHasLookup) {
            return $this->sharedInstancesWithParams['hashLong'][$fastHashFromHasLookup];
        }
        
        ksort($params);
        $hashKey = $this->createHashForKeys($classOrAlias, array_keys($params));
        if (isset($this->sharedInstancesWithParams['hashShort'][$hashKey])) {
            $hashValue = $this->createHashForValues($classOrAlias, $params);
            if (isset($this->sharedInstancesWithParams['hashLong'][$hashKey . '/' . $hashValue])) {
                return $this->sharedInstancesWithParams['hashLong'][$hashKey . '/' . $hashValue];
            }
        }
        return false;
    }
    
    
    public function hasAlias($alias)
    {
        return (isset($this->aliases[$alias]));
    }
    
    public function getAliases()
    {
        return $this->aliases;
    }
    
    /**
     * getClassFromAlias()
     *
     * @param string
     * @return bool
     */
    public function getClassFromAlias($alias)
    {
        if (!isset($this->aliases[$alias])) {
            return false;
        }
        $r = 0;
        while (isset($this->aliases[$alias])) {
            $alias = $this->aliases[$alias];
            $r++;
            if ($r > 100) {
                throw new Exception\RuntimeException(
                    sprintf('Possible infinite recursion in DI alias! Max recursion of 100 levels reached at alias "%s".', $alias)
                );
            }
        }
        return $alias;
    }
    
    protected function getBaseAlias($alias)
    {
        if (!$this->hasAlias($alias)) {
            return false;
        }
        $lastAlias = false;
        $r = 0;
        while (isset($this->aliases[$alias])) {
            $lastAlias = $alias;
            $alias = $this->aliases[$alias];
            $r++;
            if ($r > 100) {
                throw new Exception\RuntimeException(
                    sprintf('Possible infinite recursion in DI alias! Max recursion of 100 levels reached at alias "%s".', $alias)
                ); 
            }
        }
        return $lastAlias;
    }
    
    /**
     * addAlias()
     *
     * @throws Exception\InvalidArgumentException
     * @param $alias
     * @param $class
     * @param array $parameters
     * @return void
     */
    public function addAlias($alias, $class, array $parameters = array())
    {
        if (!preg_match('#^[a-zA-Z0-9-_]+$#', $alias)) {
            throw new Exception\InvalidArgumentException(
                'Aliases must be alphanumeric and can contain dashes and underscores only.'
            );
        }
        $this->aliases[$alias] = $class;
        if ($parameters) {
            $this->setParameters($alias, $parameters);
        }
    }
    
    public function hasConfiguration($aliasOrClass)
    {
        $key = ($this->hasAlias($aliasOrClass)) ? 'alias:' . $this->getBaseAlias($aliasOrClass) : $aliasOrClass;
        if (!isset($this->configurations[$key])) {
            return false;
        }
        if ($this->configurations[$key] === $this->configurationTemplate) {
            return false;
        }
        return true;
    }
    
    public function setConfiguration($aliasOrClass, array $configuration, $append = false)
    {
        $key = ($this->hasAlias($aliasOrClass)) ? 'alias:' . $this->getBaseAlias($aliasOrClass) : $aliasOrClass;
        if (!isset($this->configurations[$key]) || !$append) {
            $this->configurations[$key] = $this->configurationTemplate;
        }
        // Ignore anything but 'parameters' and 'injections'
        $configuration = array(
            'parameters' => isset($configuration['parameters']) ? $configuration['parameters'] : array(),
            'injections' => isset($configuration['injections']) ? $configuration['injections'] : array(),
            'shared'     => isset($configuration['shared'])     ? $configuration['shared']     : true
        );
        $this->configurations[$key] = array_replace_recursive($this->configurations[$key], $configuration);
    }

    public function getClasses()
    {
        $classes = array();
        foreach ($this->configurations as $name => $data) {
            if (strpos($name, 'alias') === 0) continue;
            $classes[] = $name;
        }
        return $classes;
    }

    public function getConfiguration($aliasOrClass)
    {
        $key = ($this->hasAlias($aliasOrClass)) ? 'alias:' . $this->getBaseAlias($aliasOrClass) : $aliasOrClass;
        if (isset($this->configurations[$key])) {
            return $this->configurations[$key];            
        } else {
            return $this->configurationTemplate;
        }
    }
    
    /**
     * setParameters() is a convenience method for:
     *    setConfiguration($type, array('parameters' => array(...)), true);
     *     
     * @param string $type Alias or Class
     * @param array $parameters Multi-dim array of parameters and their values
     */
    public function setParameters($aliasOrClass, array $parameters)
    {
        return $this->setConfiguration($aliasOrClass, array('parameters' => $parameters), true);
    }
    
    /**
     * setInjections() is a convenience method for:
     *    setConfiguration($type, array('injections' => array(...)), true);
     *     
     * @param string $type Alias or Class
     * @param array $methods Multi-dim array of methods and their parameters
     */
    public function setInjections($aliasOrClass, array $injections)
    {
        return $this->setConfiguration($aliasOrClass, array('injections' => $injections), true);
    }

    public function setShared($aliasOrClass, $isShared)
    {
        return $this->setConfiguration($aliasOrClass, array('shared' => $isShared), true);
    }

    public function hasTypePreferences($interfaceOrAbstract)
    {
        $key = ($this->hasAlias($interfaceOrAbstract)) ? 'alias:' . $interfaceOrAbstract : $interfaceOrAbstract;
        return (isset($this->typePreferences[$key]) && $this->typePreferences[$key]);
    }

    public function setTypePreference($interfaceOrAbstract, array $preferredImplementations)
    {
        $key = ($this->hasAlias($interfaceOrAbstract)) ? 'alias:' . $interfaceOrAbstract : $interfaceOrAbstract;
        foreach ($preferredImplementations as $preferredImplementation) {
            $this->addTypePreference($key, $preferredImplementation);
        }
        return $this;
    }

    public function getTypePreferences($interfaceOrAbstract)
    {
        $key = ($this->hasAlias($interfaceOrAbstract)) ? 'alias:' . $interfaceOrAbstract : $interfaceOrAbstract;
        if (isset($this->typePreferences[$key])) {
            return $this->typePreferences[$key];
        }
        return array();
    }
    
    public function unsetTypePreferences($interfaceOrAbstract)
    {
        $key = ($this->hasAlias($interfaceOrAbstract)) ? 'alias:' . $interfaceOrAbstract : $interfaceOrAbstract;
        unset($this->typePreferences[$key]);
    }

    public function addTypePreference($interfaceOrAbstract, $preferredImplementation)
    {
        $key = ($this->hasAlias($interfaceOrAbstract)) ? 'alias:' . $interfaceOrAbstract : $interfaceOrAbstract;
        if (!isset($this->typePreferences[$key])) {
            $this->typePreferences[$key] = array();
        }
        $this->typePreferences[$key][] = $preferredImplementation;
        return $this;
    }

    public function removeTypePreference($interfaceOrAbstract, $preferredType)
    {
        $key = ($this->hasAlias($interfaceOrAbstract)) ? 'alias:' . $interfaceOrAbstract : $interfaceOrAbstract;
        if (!isset($this->typePreferences[$key]) || !in_array($preferredType, $this->typePreferences[$key])) {
            return false;
        }
        unset($this->typePreferences[$key][array_search($key, $this->typePreferences)]);
        return $this;
    }

    
    protected function createHashForKeys($classOrAlias, $paramKeys)
    {
        return $classOrAlias . ':' . implode('|', $paramKeys);
    }
    
    protected function createHashForValues($classOrAlias, $paramValues)
    {
        $hashValue = '';
        foreach ($paramValues as $param) {
            switch (gettype($param)) {
                case 'object':
                    $hashValue .= spl_object_hash($param) . '|';
                    break;
                case 'integer':
                case 'string':
                case 'boolean':
                case 'NULL':
                case 'double':
                    $hashValue .= $param . '|';
                    break;
                case 'array':
                    $hashValue .= 'Array|';
                    break;
                case 'resource':
                    $hashValue .= 'resource|';
                    break;
            }
        }
        return $hashValue;
    }
}
