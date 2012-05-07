<?php

namespace Zend\Mvc;

use Zend\Di\LocatorInterface;

interface LocatorAwareInterface
{
    public function setLocator(LocatorInterface $locator);
    public function getLocator();
}
