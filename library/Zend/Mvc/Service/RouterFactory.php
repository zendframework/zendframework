<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\Console\Console;
use Zend\Mvc\Router\Console\SimpleRouteStack as ConsoleRouter;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RouterFactory implements FactoryInterface
{
    /**
     * Create and return the router
     *
     * Retrieves the "router" key of the Config service, and uses it
     * to instantiate the router. Uses the TreeRouteStack implementation by
     * default.
     *
     * @param  ServiceLocatorInterface        $serviceLocator
     * @param  string|null                     $cName
     * @param  string|null                     $rName
     * @return \Zend\Mvc\Router\RouteStackInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $cName = null, $rName = null)
    {
        $config             = $serviceLocator->get('Config');
        $routerConfig       = array();
        $routePluginManager = $serviceLocator->get('RoutePluginManager');

        if ($rName === 'ConsoleRouter'                       // force console router
            || ($cName === 'router' && Console::isConsole()) // auto detect console
        ) {
            // We are in a console, use console router.
            if (isset($config['console']) && isset($config['console']['router'])) {
                $routerConfig = $config['console']['router'];
            }

            if (!isset($routerConfig['route_plugins'])) {
                $routerConfig['route_plugins'] = $routePluginManager;
            }

            return ConsoleRouter::factory($routerConfig);
        }

        // This is an HTTP request, so use HTTP router
        if (isset($config['router'])) {
            $routerConfig = $config['router'];
        }

        if (!isset($routerConfig['route_plugins'])) {
            $routerConfig['route_plugins'] = $routePluginManager;
        }

        return HttpRouter::factory($routerConfig);
    }
}
