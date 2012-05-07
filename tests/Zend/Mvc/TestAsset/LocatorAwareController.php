<?php

namespace ZendTest\Mvc\TestAsset;

use Zend\Di\LocatorInterface as DiLocator,
    Zend\Mvc\Controller\ActionController,
    Zend\Mvc\LocatorAwareInterface;

class LocatorAwareController extends ActionController implements LocatorAwareInterface
{
    protected $locator;

    public function setLocator(DiLocator $locator)
    {
        $this->locator = $locator;
        return $this;
    }

    public function getLocator()
    {
        return $this->locator;
    }
}
