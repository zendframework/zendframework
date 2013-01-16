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
use Zend\Session\ManagerInterface as Manager;

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
     * @var Manager
     */
    protected $session;

    /**
     * Set the session manager
     *
     * @param  Manager $manager
     * @return FlashMessenger
     */
    public function setSessionManager(Manager $manager)
    {
        $this->session = $manager;
        return $this;
    }

    /**
     * Retrieve the session manager
     *
     * If none composed, lazy-loads a SessionManager instance
     *
     * @return Manager
     */
    public function getSessionManager()
    {
        if (!$this->session instanceof Manager) {
            $this->setSessionManager(Container::getDefaultManager());
        }
        return $this->session;
    }

    /**
     * Perform PRG logic
     *
     * If a null value is present for the $redirect, the current route is
     * retrieved and use to generate the URL for redirect.
     *
     * If the request method is POST, creates a session container set to expire
     * after 1 hop containing the values of the POST. It then redirects to the
     * specified URL using a status 303.
     *
     * If the request method is GET, checks to see if we have values in the
     * session container, and, if so, returns them; otherwise, it returns a
     * boolean false.
     *
     * @param  null|string $redirect
     * @param  bool $redirectToUrl
     * @return \Zend\Http\Response|array|Traversable|false
     */
    public function __invoke($redirect = null, $redirectToUrl = false)
    {
        $controller = $this->getController();
        $request    = $controller->getRequest();
        $params     = array();

        if (null === $redirect) {
            $routeMatch = $controller->getEvent()->getRouteMatch();

            $redirect = $routeMatch->getMatchedRouteName();
            $params   = $routeMatch->getParams();
        }

        $container = new Container('prg_post1', $this->getSessionManager());

        if ($request->isPost()) {
            $container->setExpirationHops(1, 'post');
            $container->post = $request->getPost()->toArray();

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

        if ($container->post !== null) {
            $post = $container->post;
            unset($container->post);
            return $post;
        }

        return false;
    }
}
