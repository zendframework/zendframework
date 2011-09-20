<?php

namespace Zf2Mvc;

use Zend\Di\Locator;

interface LocatorAware
{
    public function setLocator(Locator $locator);
    public function getLocator();
}
