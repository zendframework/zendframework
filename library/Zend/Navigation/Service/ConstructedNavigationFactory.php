<?php

namespace Zend\Navigation\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class ConstructedNavigationFactory extends AbstractNavigationFactory
{
    public function __construct($config)
    {
        $this->pages = $this->getPagesFromConfig($config);
    }

    public function getPages(ServiceLocatorInterface $serviceLocator)
    {
        return $this->pages;
    }

    public function getName()
    {
        return 'constructed';
    }
}