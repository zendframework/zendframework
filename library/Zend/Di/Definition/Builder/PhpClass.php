<?php

namespace Zend\Di\Definition\Builder;

class PhpClass
{
    protected $defaultMethodBuilder = 'Zend\Di\Definition\Builder\InjectionMethod';
    protected $name                 = null;
    protected $instantiator         = '__construct';
    protected $injectionMethods     = array();
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setInstantiator($instantiator)
    {
        $this->instantiator = $instantiator;
        return $this;
    }
    
    public function getInstantiator()
    {
        return $this->instantiator;
    }
    
    public function addSuperType($superType)
    {
        $this->superTypes[] = $superType;
        return $this;
    }
    
    public function getSuperTypes()
    {
        return $this->superTypes;
    }
    
    public function addInjectionMethod(InjectionMethod $injectionMethod)
    {
        $this->injectionMethods[] = $injectionMethod;
        return $this;
    }

    /**
     * Create and register an injection method
     *
     * Optionally takes the method name.
     *
     * This method may be used in lieu of addInjectionMethod() in 
     * order to provide a more fluent interface for building classes with
     * injection methods.
     * 
     * @param  null|string $name 
     * @return InjectionMethod
     */
    public function createInjectionMethod($name = null)
    {
        $builder = $this->defaultMethodBuilder;
        $method  = new $builder();
        if (null !== $name) {
            $method->setName($name);
        }
        $this->addInjectionMethod($method);
        return $method;
    }

    /**
     * Override which class will be used by {@link createInjectionMethod()}
     * 
     * @param  string $class 
     * @return PhpClass
     */
    public function setMethodBuilder($class)
    {
        $this->defaultMethodBuilder = $class;
        return $this;
    }

    /**
     * Determine what class will be used by {@link createInjectionMethod()}
     *
     * Mainly to provide the ability to temporarily override the class used.
     * 
     * @return string
     */
    public function getMethodBuilder()
    {
        return $this->defaultMethodBuilder;
    }
    
    public function getInjectionMethods()
    {
        return $this->injectionMethods;
    }

}
