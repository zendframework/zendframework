<?php

namespace Zend\Di;

class InstanceManager implements InstanceCollection
{
    /**
     * Preferred Instances for classes and aliases
     * @var unknown_type
     */
    protected $preferredInstances = array();
    
    /**
     * Properties array
     * @var array
     */
    protected $properties = array();
    
    /**
     * Array of shared instances
     * @var array
     */
    protected $sharedInstances = array();
    
    protected $sharedInstancesWithParams = array('hashShort' => array(), 'hashLong' => array());
    
    /**
     * Array of class aliases
     * @var array
     */
    protected $aliases = array();
    
    /**
     * Does this instance manager have this shared instance
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
    
    public function addSharedInstanceWithParameters($instance, $classOrAlias, array $params)
    {
        ksort($params);
        $hashKey = $this->createHashForKeys($classOrAlias, array_keys($params));
        $hashValue = $this->createHashForValues($classOrAlias, $params);
        
        if (!isset($this->sharedInstancesWithParams[$hashKey]) || !is_array($this->sharedInstancesWithParams[$hashKey])) {
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
        return array_key_exists($alias, $this->aliases);
    }
    
    public function getAliases()
    {
        return $this->aliases;
    }
    
    /**
     * getClassFromAlias()
     */
    public function getClassFromAlias($alias)
    {
        if (isset($this->aliases[$alias])) {
            return $this->aliases[$alias];
        }
        return false;
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::addAlias()
     */
    public function addAlias($alias, $class, array $properties = array(), array $preferredInstances = array())
    {
        if (!preg_match('#^[a-zA-Z0-9-_]+$#', $alias)) {
            throw new Exception\InvalidArgumentException('Aliases must be alphanumeric and can contain dashes and underscores only.');
        }
        $this->aliases[$alias] = $class;
        if ($properties) {
            $this->setProperties($alias, $properties);
        }
        if ($preferredInstances) {
            $this->setPreferredInstances($alias, $preferredInstances);
        }
    }
    
    public function hasPreferredInstances($classOrAlias)
    {
        $key = ($this->hasAlias($classOrAlias)) ? 'alias:' . $classOrAlias : $classOrAlias;
        return (isset($this->preferredInstances[$key]) && $this->preferredInstances[$key]);
    }
    
    public function setPreferredInstances($classOrAlias, array $preferredInstances)
    {
        $key = ($this->hasAlias($classOrAlias)) ? 'alias:' . $classOrAlias : $classOrAlias;
        foreach ($preferredInstances as $preferredInstance) {
            $this->addPreferredInstance($key, $preferredInstance);
        }
        return $this;
    }

    public function getPreferredInstances($classOrAlias)
    {
        $key = ($this->hasAlias($classOrAlias)) ? 'alias:' . $classOrAlias : $classOrAlias;
        if (isset($this->preferredInstances[$key])) {
            return $this->preferredInstances[$key];
        }
        return array();
    }
    
    public function unsetPreferredInstances($classOrAlias)
    {
        $key = ($this->hasAlias($classOrAlias)) ? 'alias:' . $classOrAlias : $classOrAlias;
        if (isset($this->preferredInstances[$key])) {
            unset($this->preferredInstances[$key]);
        }
        return false;
    }
    
    public function addPreferredInstance($classOrAlias, $preferredInstance)
    {
        $key = ($this->hasAlias($classOrAlias)) ? 'alias:' . $classOrAlias : $classOrAlias;
        if (!isset($this->preferredInstances[$key])) {
            $this->preferredInstances[$key] = array();
        }
        $this->preferredInstances[$key][] = $preferredInstance;
        return $this;
    }

    public function removePreferredInstance($classOrAlias, $preferredInstance)
    {
        $key = ($this->hasAlias($classOrAlias)) ? 'alias:' . $classOrAlias : $classOrAlias;
        if (!isset($this->preferredInstances[$key]) || !in_array($preferredInstance, $this->preferredInstances[$key])) {
            return false;
        }
        unset($this->preferredInstances[$key][array_search($key, $this->preferredInstances)]);
        return $this;
    }
    

    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::hasProperties()
     */
    public function hasProperties($classOrAlias)
    {
        $key = ($this->hasAlias($classOrAlias)) ? 'alias:' . $classOrAlias : $classOrAlias;
        return isset($this->properties[$key]);
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::getProperties()
     */
    public function getProperties($classOrAlias)
    {
        $key = ($this->hasAlias($classOrAlias)) ? 'alias:' . $classOrAlias : $classOrAlias;
        if (isset($this->properties[$key])) {
            return $this->properties[$key];
        }
        return array();
    }
    
    public function setProperties($classOrAlias, array $properties, $merge = false)
    {
        $key = ($this->hasAlias($classOrAlias)) ? 'alias:' . $classOrAlias : $classOrAlias;
        if (isset($this->properties[$key]) && $merge == false) {
            $this->properties[$key] = array();
        }
        foreach ($properties as $propertyName => $propertyValue) {
            $this->setProperty($key, $propertyName, $propertyValue);
        }
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::getProperty()
     */
    public function hasProperty($classOrAlias, $name)
    {
        $key = ($this->hasAlias($classOrAlias)) ? 'alias:' . $classOrAlias : $classOrAlias;
        if (isset($this->properties[$key]) && isset($this->properties[$key][$name])) {
            return true;
        }
        return false;
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::getProperty()
     */
    public function getProperty($classOrAlias, $name)
    {
        $key = ($this->hasAlias($classOrAlias)) ? 'alias:' . $classOrAlias : $classOrAlias;
        if (isset($this->properties[$key][$name])) {
            return $this->properties[$key][$name];
        }
        return null;
    }
    
    /**
     * setProperty()
     */
    public function setProperty($classOrAlias, $name, $value)
    {
        if (is_object($value)) { 
            throw new Exception\InvalidArgumentException('Property value must be a scalar or array');
        }
        $key = ($this->hasAlias($classOrAlias)) ? 'alias:' . $classOrAlias : $classOrAlias;
        if (!isset($this->properties[$key])) {
            $this->properties[$key] = array();
        }
        $this->properties[$key][$name] = $value;
        return $this;
    }
    
    /**
     * unsetProperty()
     */
    public function unsetProperty($classOrAlias, $name)
    {
        $key = ($this->hasAlias($classOrAlias)) ? 'alias:' . $classOrAlias : $classOrAlias;
        if (isset($this->properties[$key])) {
            unset($this->properties[$key][$name]);
            return true;
        }
        return false;
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