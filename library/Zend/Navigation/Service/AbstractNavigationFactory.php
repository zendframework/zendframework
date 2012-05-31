<?php

namespace Zend\Navigation\Service;

use Zend\Navigation\Exception;
use Zend\Navigation\Navigation;
use Zend\Navigation\Page\Mvc as MvcPage;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\Url as UrlHelper;

abstract class AbstractNavigationFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $pages;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $pages = $this->getPages($serviceLocator);
        return new Navigation($pages);
    }

    abstract protected function getName();

    protected function getPages(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->pages) {
            $configuration = $serviceLocator->get('Configuration');

            if (!isset($configuration['navigation'])) {
                throw new Exception\InvalidArgumentException('Could not find navigation configuration key');
            }
            if (!isset($configuration['navigation'][$this->getName()])) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Failed to find a navigation container by the name "%s"',
                    $this->getName()
                ));
            }

            $application = $serviceLocator->get('Application');
            $urlHelper   = $serviceLocator->get('ViewHelperBroker')->load('url');
            $routeMatch  = $application->getMvcEvent()->getRouteMatch();
            $pages       = $configuration['navigation'][$this->getName()];

            $this->pages = $this->injectComponents($pages, $routeMatch, $urlHelper);
        }
        return $this->pages;
    }

    protected function injectComponents($pages, RouteMatch $routeMatch, UrlHelper $urlHelper)
    {
        foreach($pages as &$page) {
            $hasMvc = isset($page['action']) || isset($page['controller']) || isset($page['route']);
            if ($hasMvc) {
                if (!isset($page['routeMatch'])) {
                    $page['routeMatch'] = $routeMatch;
                }
                if (!isset($page['urlHelper'])) {
                    $page['urlHelper'] = $urlHelper;
                }
            }

            if (isset($page['pages'])) {
                $page['pages'] = $this->injectComponents($page['pages'], $routeMatch, $urlHelper);
            }
        }
        return $pages;
    }
}