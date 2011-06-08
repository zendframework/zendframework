<?php

namespace Zend\Di;

interface InstanceCollection
{
    public function hasSharedInstance($classOrAlias, array $params = array());
    public function getSharedInstance($classOrAlias, array $params = array());
    public function addSharedInstance($object, $class, array $params = array());
    public function getClassFromAlias($alias);
    public function addAlias($class, $alias);
    public function hasProperties($classOrAlias);
    public function getProperties($classOrAlias);
    public function hasProperty($classOrAlias, $name);
    public function getProperty($classOrAlias, $name);
    public function setProperty($classOrAlias, $name, $value);
    public function unsetProperty($classOrAlias, $name);    
}
