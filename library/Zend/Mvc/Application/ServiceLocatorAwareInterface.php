<?php

namespace Zend\Mvc\Application;

interface ServiceLocatorAwareInterface
{
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator);
}
