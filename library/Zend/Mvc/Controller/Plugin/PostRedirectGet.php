<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\Controller\Plugin;

use Zend\Mvc\Exception\RuntimeException;
use Zend\Session\Container;

/**
 * Plugin to help facilitate Post/Redirect/Get (http://en.wikipedia.org/wiki/Post/Redirect/Get)
 *
 * @category Zend
 * @package Zend_Mvc
 * @subpackage Controller
 */
class PostRedirectGet extends AbstractPlugin
{
    /**
     * @var Container
     */
    protected $sessionContainer;

    public function __invoke($redirect = null, $redirectToUrl = false)
    {
        $controller = $this->getController();
        $request    = $controller->getRequest();
        $container  = $this->getSessionContainer();

        if ($request->isPost()) {
            $container->setExpirationHops(1, 'post');
            $container->post = $request->getPost()->toArray();
            return $this->redirect($redirect, $redirectToUrl);
        } else {
            if ($container->post !== null) {
                $post = $container->post;
                unset($container->post);
                return $post;
            }

            return false;
        }
    }

    public function getSessionContainer()
    {
        if (!isset($this->sessionContainer)) {
            $this->sessionContainer = new Container('prg_post1');
        }
        return $this->sessionContainer;
    }

    protected function redirect($redirect, $redirectToUrl)
    {
        $controller = $this->getController();
        $params     = array();

        if (null === $redirect) {
            $routeMatch = $controller->getEvent()->getRouteMatch();

            $redirect = $routeMatch->getMatchedRouteName();
            $params   = $routeMatch->getParams();
        }

        if (method_exists($controller, 'getPluginManager')) {
            // get the redirect plugin from the plugin manager
            $redirector = $controller->getPluginManager()->get('Redirect');
        } else {
            /*
             * If the user wants to redirect to a route, the redirector has to come
             * from the plugin manager -- otherwise no router will be injected
             */
            if ($redirectToUrl === false) {
                throw new RuntimeException('Could not redirect to a route without a router');
            }

            $redirector = new Redirect();
        }

        if ($redirectToUrl === false) {
            $response = $redirector->toRoute($redirect, $params);
            $response->setStatusCode(303);
            return $response;
        }

        $response = $redirector->toUrl($redirect);
        $response->setStatusCode(303);

        return $response;
    }
}
