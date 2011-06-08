<?php
namespace Zend\Di\ServiceLocator;

interface ServiceLocation
{
    public function set($name, $service);
    public function get($name, array $params = null);
}
