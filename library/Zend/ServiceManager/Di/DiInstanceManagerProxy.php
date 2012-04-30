<?php

namespace Zend\ServiceManager\Di;

use Zend\Di\InstanceManager as DiInstanceManager,
    Zend\ServiceManager\ServiceManager;

class DiInstanceManagerProxy extends DiInstanceManager
{
    protected $diInstanceManager = null;
    protected $serviceManager = null;

    public function __construct(DiInstanceManager $diInstanceManager, ServiceManager $serviceManager)
    {
        $this->diInstanceManager = $diInstanceManager;
        $this->serviceManager = $serviceManager;
    }

    public function hasSharedInstance($classOrAlias)
    {
        return ($this->serviceManager->has($classOrAlias) || $this->diInstanceManager->hasSharedInstance($classOrAlias));
    }

    public function getSharedInstance($classOrAlias)
    {
        if ($this->serviceManager->has($classOrAlias)) {
            return $this->serviceManager->get($classOrAlias);
        } else {
            return $this->diInstanceManager->getSharedInstance($classOrAlias);
        }
    }
}