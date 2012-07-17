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

use Zend\Mvc\Exception\RuntimeException;

/**
 * Plugin to help facilitate Post/Redirect/Get (http://en.wikipedia.org/wiki/Post/Redirect/Get)
 *
 * @category Zend
 * @package Zend_Mvc
 * @subpackage Controller
 */
class PostRedirectGet extends AbstractPlugin
{
    public function __invoke($redirect, $redirectToUrl = false)
    {
        $controller = $this->getController();
        $request = $controller->getRequest();

        if (method_exists($controller, 'getPluginManager')) {
            $flashMessenger = $controller->getPluginManager()->get('FlashMessenger');
        } else {
            $flashMessenger = new FlashMessenger();
        }

        $flashMessenger->setNamespace('prg-post');

        if ($request->isPost()) {
            $flashMessenger->addMessage($request->getPost()->toArray());

            if (method_exists($controller, 'getPluginManager')) {
                // get the redirect plugin from the plugin manager
                $redirector = $controller->getPluginManager()->get('Redirect');
            } else {

                /*
                 * if the user wants to redirect to a route, the redirector has to come
                 * from the plugin manager -- otherwise no router will be injected
                 */
                if ($redirectToUrl === false) {
                    throw new RuntimeException('Could not redirect to a route without a router');
                }

                $redirector = new Redirect();
            }

            if ($redirectToUrl === false) {
                return $redirector->toRoute($redirect);
            }

            return $redirector->toUrl($redirect);
        } else {
            $messages = $flashMessenger->getMessages();
            if (count($messages)) {
                return $messages[0];
            }

            return false;
        }
    }
}

