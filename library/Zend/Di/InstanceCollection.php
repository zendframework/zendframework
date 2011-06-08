<?php

namespace Zend\Di;

interface InstanceCollection
{
    public function hasSharedInstance($class, array $params = array());
    public function getSharedInstance($class, array $params = array());
    public function addSharedInstance($object, $class, array $params = array());
    public function getClassFromAlias($alias);
    public function addAlias($class, $alias);
    public function hasProperties($classOrAlias);
    public function getProperties($classOrAlias);
    public function getProperty($class, $name);
    public function setProperty($class, $name, $value);
    public function unsetProperty($class, $name);    
}
