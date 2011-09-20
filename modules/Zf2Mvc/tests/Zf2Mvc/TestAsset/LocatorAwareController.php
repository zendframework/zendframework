<?php

namespace Zf2Mvc\TestAsset;

use Zend\Di\Locator as DiLocator,
    Zf2Mvc\Controller\ActionController,
    Zf2Mvc\LocatorAware;

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
