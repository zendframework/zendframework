<?php

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\Mvc\Controller\AbstractActionController;

class ForwardController extends AbstractActionController
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
