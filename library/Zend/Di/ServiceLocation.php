<?php
namespace Zend\Di;

interface ServiceLocation
{
    public function set($name, $service);
    public function get($name, array $params = null);
}
