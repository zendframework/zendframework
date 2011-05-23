<?php

namespace Zend\Mvc\Application;

interface ServiceLocatorInterface
{
    public function get($name);
}