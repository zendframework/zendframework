<?php

namespace Mock\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    public function unittestsAction()
    {
        $num_get = $this->getRequest()->getQuery()->get('num_get', 0);
        $num_post = $this->getRequest()->getPost()->get('num_post', 0);
        return array('num_get' => $num_get, 'num_post' => $num_post);
    }

    public function consoleAction()
    {

    }
}
