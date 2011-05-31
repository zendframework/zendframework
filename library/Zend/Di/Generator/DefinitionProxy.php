<?php

namespace Zend\Di\Generator;

class DefinitionProxy extends \Zend\Di\Definition
{
    /**
	 * @var Zend\Di\Definition
     */
    protected $definition = null;
    
    public function __construct(\Zend\Di\Definition $definition)
    {
        $this->definition = $definition;
    }
    
    public function setClass($className)
    {
        $this->definition->setClass($className);
        return $this;
    }
    
    public function getClass()
    {
        return $this->definition->getClass();
    }

    public function setConstructorCallback($callback)
    {
        $this->definition->setConstructorCallback($callback);
        return $this;
    }
    public function getConstructorCallback()
    {
        return $this->definition->getConstructorCallback();
    }
    
    public function hasConstructorCallback()
    {
        return $this->definition->hasConstructorCallback();
    }

    public function setParam($name, $value)
    {
        $this->definition->setParam($name, $value);
        return $this;
    }
    
    public function setParams(array $params)
    {
        $this->definition->setParams($params);
        return $this;
    }
    
    /**
     * @param array $map Map of name => position pairs for constructor arguments
     */
    public function setParamMap(array $map)
    {
        $this->definition->setParamMap($map);
        return $this;
    }
    
    public function getParams()
    {
        return $this->definition->getParams();
    }
    
    public function setShared($flag = true)
    {
        $this->definition->setShared($flag);
        return $this;
    }
    
    public function isShared()
    {
        return $this->definition->isShared();
    }
    
    
    public function addTag($tag)
    {
        throw new \Exception('No Tags');
    }
    
    public function addTags(array $tags)
    {
        throw new \Exception('No Tags');
    }
    
    public function getTags()
    {
        throw new \Exception('No Tags');
    }
    
    public function hasTag($tag)
    {
        throw new \Exception('No Tags');
    }
    
    public function addMethodCall($name, array $args)
    {
        return $this->definition->addMethodCall($name, $args);
    }
    
    /**
     * @return InjectibleMethods
     */
    public function getMethodCalls() {}
    
    public function toArray()
    {
        $params = array();
        foreach ($this->definition->constructorParams as $cParamName => $cParamValue) {
            if ($cParamValue instanceof \Zend\Di\Reference) {
                $cParamValue = array('__reference' => $cParamValue->getServiceName());
            }
            $params[$cParamName] = $cParamValue;
        }
        
        $methods = array();
        foreach ($this->definition->injectibleMethods as $method) {
            $args = array();
            foreach ($method->getArgs() as $argKey => $arg) {
                if ($arg instanceof \Zend\Di\Reference) {
                    $args[$argKey]['__reference'] = $arg->getServiceName();
                } else {
                    $args[$argKey][] = $arg;
                }
            }
            $methods[] = array('name' => $method->getName(), 'args' => $args);
        }
        
        return array(
        	'class' => $this->definition->className,
            'methods' => $methods,
        	'param_map' => ($this->definition->constructorParamMap) ?: array(),
            'params' => $params
            );
    }
    
}

