<?php

namespace Zend\Mvc;

use Zend\Di\Locator;

interface LocatorAwareInterface
{
    public function setLocator(Locator $locator);
    public function getLocator();
}
