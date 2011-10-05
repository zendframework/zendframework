<?php

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\Mvc\Controller\ActionController;

class ForwardController extends ActionController
{
    public function testAction()
    {
        return array('content' => __METHOD__);
    }

    public function testMatchesAction()
    {
        $e = $this->getEvent();
        return $e->getRouteMatch()->getParams();
    }
}
