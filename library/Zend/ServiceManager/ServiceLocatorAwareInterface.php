<?php

namespace Zend\ServiceManager;

interface ServiceLocatorAwareInterface
{
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator);
}
