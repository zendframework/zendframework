<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\Controller\Plugin;

use Zend\EventManager\EventInterface;
use Zend\Mvc\Exception;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteStackInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 */
class Url extends AbstractPlugin
{
    /**
     * Generates a URL based on a route
     *
     * @param  string $route RouteInterface name
     * @param  array $params Parameters to use in url generation, if any
     * @param  array $options RouteInterface-specific options to use in url generation, if any
     * @return string
     * @throws Exception\DomainException if composed controller does not implement InjectApplicationEventInterface, or
     *         router cannot be found in controller event
     */
    public function fromRoute($route, array $params = array(), array $options = array())
    {
        $controller = $this->getController();
        if (!$controller instanceof InjectApplicationEventInterface) {
            throw new Exception\DomainException('Url plugin requires a controller that implements InjectApplicationEventInterface');
        }

        $event  = $controller->getEvent();
        $router = null;
        if ($event instanceof MvcEvent) {
            $router = $event->getRouter();
        } elseif ($event instanceof EventInterface) {
            $router = $event->getParam('router', false);
        }
        if (!$router instanceof RouteStackInterface) {
            throw new Exception\DomainException('Url plugin requires that controller event compose a router; none found');
        }

        $options['name'] = $route;
        return $router->assemble($params, $options);
    }
}
