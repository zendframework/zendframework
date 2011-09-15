<?php
namespace Zend\Di;

interface ServiceLocation extends Locator
{
    public function set($name, $service);
}
