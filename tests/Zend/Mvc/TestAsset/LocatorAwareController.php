<?php

namespace ZendTest\Mvc\TestAsset;

use Zend\Di\Locator as DiLocator,
    Zend\Mvc\Controller\ActionController,
    Zend\Mvc\LocatorAware;

class LocatorAwareController extends ActionController implements LocatorAware
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
