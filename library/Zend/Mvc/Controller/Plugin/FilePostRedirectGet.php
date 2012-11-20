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

use Zend\Form\Form;
use Zend\Mvc\Exception\RuntimeException;
use Zend\Session\Container;

/**
 * Plugin to help facilitate Post/Redirect/Get (http://en.wikipedia.org/wiki/Post/Redirect/Get)
 *
 * @category Zend
 * @package Zend_Mvc
 * @subpackage Controller
 */
class FilePostRedirectGet extends AbstractPlugin
{
    /**
     * @var Container
     */
    protected $sessionContainer;

    public function __invoke($form, $redirect = null, $redirectToUrl = false)
    {
        $controller = $this->getController();
        $request    = $controller->getRequest();
        $container  = $this->getSessionContainer();

        if ($request->isPost()) {
            $container->setExpirationHops(1, array('post', 'errors'));

            $post = array_merge(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $container->post = $post;

            $form->setData($post);
            if (!$form->isValid()) {
                $container->errors = $form->getMessages();
            }

            return $this->redirect($redirect, $redirectToUrl);
        } else {
            if (null !== $container->post) {
                $post   = $container->post;
                $errors = $container->errors;
                unset($container->post);
                unset($container->errors);

                $form->setData($post);
                if (null !== $errors) {
                    $form->setMessages($errors);
                }

                return $post;
            }

            return false;
        }
    }

    public function getSessionContainer()
    {
        if (!isset($this->sessionContainer)) {
            $this->sessionContainer = new Container('file_prg_post1');
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
