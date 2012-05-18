<?php

namespace ZendTest\Loader\TestAsset;

use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceLocator implements ServiceLocatorInterface
{
    protected $services = array();

    public function get($name, array $params = array())
    {
        if (!isset($this->services[$name])) {
            return null;
        }

        return $this->services[$name];
    }

    public function has($name)
    {
        return (isset($this->services[$name]));
    }

    public function set($name, $object)
    {
        $this->services[$name] = $object;
    }
}
