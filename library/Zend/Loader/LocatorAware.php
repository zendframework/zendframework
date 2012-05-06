<?php

namespace Zend\Loader;

use Zend\Di\LocatorInterface;

interface LocatorAware
{
    public function setLocator(LocatorInterface $locator);
    public function getLocator();
}
