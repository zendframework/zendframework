<?php

namespace Zend\Di;

interface InstanceCollection
{
    public function hasSharedInstance($classOrAlias);
    public function getSharedInstance($classOrAlias);
    public function addSharedInstance($instance, $class);
    public function hasSharedInstanceWithParameters($classOrAlias, array $params);
    public function getSharedInstanceWithParameters($classOrAlias, array $params);
    public function addSharedInstanceWithParameters($instance, $class, array $params);
    public function getClassFromAlias($alias);
    public function addAlias($class, $alias);
    public function hasProperties($classOrAlias);
    public function getProperties($classOrAlias);
    public function hasProperty($classOrAlias, $name);
    public function getProperty($classOrAlias, $name);
    public function setProperty($classOrAlias, $name, $value);
    public function unsetProperty($classOrAlias, $name);    
}
