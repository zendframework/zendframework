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

use Zend\Mvc\Router\Exception\RuntimeException;

/**
 * Plugin to help facilitate Post/Redirect/Get (http://en.wikipedia.org/wiki/Post/Redirect/Get)
 *
 * @category Zend
 * @package Zend_Mvc
 * @subpackage Controller
 */
class PostRedirectGet extends AbstractPlugin
{
    public function __invoke($redirect)
    {
        $controller = $this->getController();
        $request = $controller->getRequest();
        $flashMessenger = $controller->flashMessenger()->setNamespace('prg-post');

        if ($request->isPost()) {
            $flashMessenger->addMessage($request->getPost()->toArray());
            try {
                return $controller->redirect()->toRoute($redirect);
            } catch (RuntimeException $e) {
                return $controller->redirect()->toUrl($redirect);
            }
        } else {
            $messages = $flashMessenger->getMessages();
            if (count($messages)) {
                return $messages[0];
            } else {
                return false;
            }
        }
    }
}

