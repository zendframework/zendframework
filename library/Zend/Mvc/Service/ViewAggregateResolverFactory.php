<?php

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\View\Resolver\AggregateResolver as ViewAggregateResolver;

class ViewAggregateResolverFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $map   = $serviceLocator->get('ViewTemplateMapResolver');
        $stack = $serviceLocator->get('ViewTemplatePathStack');
        $aggregate = new ViewAggregateResolver();
        $aggregate->attach($map);
        $aggregate->attach($stack);
        return $aggregate;
    }
}
