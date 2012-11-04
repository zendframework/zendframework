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

        $returnObj = new \stdClass(); // TODO: Create a value object class / interface?
        $returnObj->post     = null;
        $returnObj->isValid  = null;
        $returnObj->response = null;

        if ($request->isPost()) {
            $post = array_merge(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $returnObj->post = $container->post = $post;
            $form->setData($post);

            $returnObj->isValid = $isValid = $form->isValid();
            if (!$isValid) {
                $container->errors = $form->getMessages();
            }

            $returnObj->response = $this->redirect($redirect, $redirectToUrl);
            return $returnObj;
        } else {
            if (null !== $container->post) {
                $post   = $container->post;
                $errors = $container->errors;
                unset($container->post);
                unset($container->errors);

                $form->setData($post);

                $returnObj->isValid = $isValid = (null === $errors);
                if (!$isValid) {
                    $form->setMessages($errors);
                }

                $returnObj->post = $post;
                return $returnObj;
            }

            return false;
        }
    }

    protected function getSessionContainer()
    {
        if (!isset($this->sessionContainer)) {
            $this->sessionContainer = new Container('file_prg_post1');
            $this->sessionContainer->setExpirationHops(1, array('post', 'errors'));
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
