<?php

namespace Zend\ServiceManager\Di;

use Zend\Di\InstanceManager as DiInstanceManager,
    Zend\ServiceManager\ServiceLocatorInterface;

class DiInstanceManagerProxy extends DiInstanceManager
{
    /**
     * @var DiInstanceManager
     */
    protected $diInstanceManager = null;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * @param DiInstanceManager $diInstanceManager
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(DiInstanceManager $diInstanceManager, ServiceLocatorInterface $serviceLocator)
    {
        $this->diInstanceManager = $diInstanceManager;
        $this->serviceLocator = $serviceLocator;

        // localize state
        $this->aliases = &$diInstanceManager->aliases;
        $this->sharedInstances = &$diInstanceManager->sharedInstances;
        $this->sharedInstancesWithParams = &$diInstanceManager->sharedInstancesWithParams;
        $this->configurations = &$diInstanceManager->configurations;
        $this->typePreferences = &$diInstanceManager->typePreferences;
    }

    /**
     * @param $classOrAlias
     * @return bool
     */
    public function hasSharedInstance($classOrAlias)
    {
        return ($this->serviceLocator->has($classOrAlias) || $this->diInstanceManager->hasSharedInstance($classOrAlias));
    }

    /**
     * @param $classOrAlias
     * @return mixed
     */
    public function getSharedInstance($classOrAlias)
    {
        if ($this->serviceLocator->has($classOrAlias)) {
            return $this->serviceLocator->get($classOrAlias);
        } else {
            return $this->diInstanceManager->getSharedInstance($classOrAlias);
        }
    }
}
