<?php

namespace Zend\ServiceManager;

interface ServiceLocatorInterface
{
    public function get($name);
    public function has($name);
}
