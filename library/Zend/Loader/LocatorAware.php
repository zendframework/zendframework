<?php

namespace Zend\Loader;

use Zend\Di\Locator;

interface LocatorAware
{
    public function setLocator(Locator $locator);
    public function getLocator();
}
