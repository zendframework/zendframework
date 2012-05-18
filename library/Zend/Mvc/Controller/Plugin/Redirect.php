<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Controller\Plugin;

use Zend\Http\Response,
    Zend\Mvc\InjectApplicationEventInterface,
    Zend\Mvc\Exception,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteStackInterface;

/**
 * @todo       allow specifying status code as a default, or as an option to methods
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Redirect extends AbstractPlugin
{
    protected $event;
    protected $response;
    protected $router;

    /**
     * Generates a URL based on a route
     *
     * @param  string $route RouteInterface name
     * @param  array $params Parameters to use in url generation, if any
     * @param  array $options RouteInterface-specific options to use in url generation, if any
     * @return Response
     * @throws Exception\DomainException if composed controller does not implement InjectApplicationEventInterface, or
     *         router cannot be found in controller event
     */
    public function toRoute($route, array $params = array(), array $options = array())
    {
        $response = $this->getResponse();
        $router   = $this->getRouter();

        $options['name'] = $route;
        $url = $router->assemble($params, $options);
        $response->headers()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);
        return $response;
    }

    /**
     * Redirect to the given URL
     *
     * @param  string $url
     * @return Response
     */
    public function toUrl($url)
    {
        $response = $this->getResponse();
        $response->headers()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);
        return $response;
    }

    /**
     * Get the router
     *
     * @return RouteStackInterface
     * @throws Exception\DomainException if unable to find router
     */
    protected function getRouter()
    {
        if ($this->router) {
            return $this->router;
        }

        $event  = $this->getEvent();
        $router = $event->getRouter();
        if (!$router instanceof RouteStackInterface) {
            throw new Exception\DomainException('Redirect plugin requires event compose a router');
        }
        $this->router = $router;
        return $this->router;
    }

    /**
     * Get the response
     *
     * @return Response
     * @throws Exception\DomainException if unable to find response
     */
    protected function getResponse()
    {
        if ($this->response) {
            return $this->response;
        }

        $event    = $this->getEvent();
        $response = $event->getResponse();
        if (!$response instanceof Response) {
            throw new Exception\DomainException('Redirect plugin requires event compose a response');
        }
        $this->response = $response;
        return $this->response;
    }

    /**
     * Get the event
     *
     * @return MvcEvent
     * @throws Exception\DomainException if unable to find event
     */
    protected function getEvent()
    {
        if ($this->event) {
            return $this->event;
        }

        $controller = $this->getController();
        if (!$controller instanceof InjectApplicationEventInterface) {
            throw new Exception\DomainException('Redirect plugin requires a controller that implements InjectApplicationEventInterface');
        }

        $event = $controller->getEvent();
        if (!$event instanceof MvcEvent) {
            $params = $event->getParams();
            $event  = new MvcEvent();
            $event->setParams($params);
        }
        $this->event = $event;

        return $this->event;
    }
}
