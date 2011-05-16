<?php
namespace Zend\Di;

interface DependencyDefinition
{
    public function __construct($className);

    public function getClass();

    public function setConstructorCallback($callback);
    public function getConstructorCallback();
    public function hasConstructorCallback();

    public function setParam($name, $value);
    public function setParams(array $params);
    /**
     * @param array $map Map of name => position pairs for constructor arguments
     */
    public function setParamMap(array $map);
    public function getParams();
    
    public function setShared($flag = true);
    public function isShared();
    
    public function addMethodCall($name, array $args);
    /**
     * @return InjectibleMethods
     */
    public function getMethodCalls();
}
