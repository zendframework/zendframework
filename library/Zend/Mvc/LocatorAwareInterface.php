<?php

namespace Zend\Mvc;

use Zend\Di\LocatorInterface;

interface LocatorAwareInterface
{
    /**
     * Set locator
     *
     * @param LocatorInterface $locator
     * @return LocatorAwareInterface
     */
    public function setLocator(LocatorInterface $locator);

    /**
     * Get locator
     *
     * @return LocatorInterface
     */
    public function getLocator();
}
