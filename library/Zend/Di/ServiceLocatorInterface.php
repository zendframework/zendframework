<?php
namespace Zend\Di;

interface ServiceLocatorInterface extends LocatorInterface
{
    public function set($name, $service);
}
