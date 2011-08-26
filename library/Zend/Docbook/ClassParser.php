<?php

namespace Zend\Docbook;

use ReflectionMethod,
    Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter,
    Zend\Reflection\ReflectionClass;

class ClassParser
{
    /**
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * @var string Normalized Docbook ID
     */
    protected $id;

    /**
     * @var array Array of ClassMethod objects representing public methods
     */
    protected $methods;

    /**
     * Constructor
     * 
     * @param  ReflectionClass $class 
     * @return void
     */
    public function __construct(ReflectionClass $class)
    {
        $this->reflection = $class;
    }

    /**
     * Retrieve docbook ID for this class
     * 
     * @return string
     */
    public function getId()
    {
        if (null !== $this->id) {
            return $this->id;
        }

        $class     = $this->reflection->getName();
        $id        = '';
        $filter    = new CamelCaseToDashFilter();

        foreach (explode('\\', $class) as $segment) {
            $id .= $filter->filter($segment) . '.';
        }

        $id = strtolower(rtrim($id, '.'));

        $this->id = $id;
        return $this->id;
    }

    /**
     * Get class name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->reflection->getName();
    }

    /**
     * Retrieve parsed methods for this class
     * 
     * @return array Array of ClassMethod objects
     */
    public function getMethods()
    {
        if (null !== $this->methods) {
            return $this->methods;
        }

        $rMethods = $this->reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $methods  = array();
        foreach ($rMethods as $method) {
            $methods[] = new ClassMethod($method);
        }
        
        $this->methods = $methods;
        return $this->methods;
    }
}
