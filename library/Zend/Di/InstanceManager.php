<?php

namespace Zend\Di;

class InstanceManager implements InstanceCollection
{
    /**
     * @todo Implement parameter hashing to determine which object is truely new
     * @var array
     */
    protected $parameterHashes = array();
    
    /**
     * Enter description here ...
     * @var array
     */
    protected $properties = array();
    
    /**
     * Array of shared instances
     * @var array
     */
    protected $sharedInstances = array();
    
    /**
     * Array of class aliases
     * @var array
     */
    protected $aliases = array();
    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::hasSharedInstance()
     */
    public function hasSharedInstance($class, array $params = array())
    {
        return isset($this->sharedInstances[$class]);
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::getSharedInstance()
     */
    public function getSharedInstance($class, array $params = array())
    {
        return $this->sharedInstances[$class];
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::addSharedInstance()
     */
    public function addSharedInstance($object, $class, array $params = array())
    {
        $this->sharedInstances[$class] = $object;
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::getClassFromAlias()
     */
    public function getClassFromAlias($alias)
    {
        if (isset($this->aliases[$alias])) {
            return $this->aliases[$alias];
        }
        return $alias; // must be a class?
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::addAlias()
     */
    public function addAlias($class, $alias, $params = array())
    {
        // @todo impelement params for aliases
        $this->aliases[$alias] = $class;
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::hasProperties()
     */
    public function hasProperties($classOrAlias)
    {
        $class = $this->getClassFromAlias($classOrAlias);
        return isset($this->properties[$class]);
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::getProperties()
     */
    public function getProperties($classOrAlias)
    {
        // @todo better alias property management
        if (isset($this->properties[$classOrAlias])) {
            return $this->properties[$classOrAlias];
        }
        return array();
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::getProperty()
     */
    public function getProperty($classOrAlias, $name)
    {
        // @todo better alias property management
        if (isset($this->properties[$classOrAlias])) {
            return $this->properties[$classOrAlias][$name];
        }
        return null;
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::setProperty()
     */
    public function setProperty($classOrAlias, $name, $value)
    {
        if (!isset($this->properties[$classOrAlias])) {
            $this->properties[$classOrAlias] = array();
        }
        $this->properties[$classOrAlias][$name] = $value;
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend\Di.InstanceCollection::unsetProperty()
     */
    public function unsetProperty($classOrAlias, $name)
    {
        if (isset($this->properties[$classOrAlias])) {
            unset($this->properties[$classOrAlias][$name]);
            return true;
        }
        return false;
    }
    
    
}