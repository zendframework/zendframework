<?php

namespace ZendTest\Mvc\TestAsset;

use Zend\Di\LocatorInterface as DiLocator;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\LocatorAwareInterface;

class LocatorAwareController extends AbstractActionController implements LocatorAwareInterface
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
