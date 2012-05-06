<?php

namespace Zend\Mvc;

use Zend\Di\LocatorInterface;

interface LocatorAware
{
    public function setLocator(LocatorInterface $locator);
    public function getLocator();
}
