<?php

namespace Zend\Mvc\Controller\Plugin;

use Zend\Mvc\Router\Exception\RuntimeException;

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

