<?php

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\Mvc\Controller\ActionController;

class BadController extends ActionController
{
    public function testAction()
    {
        throw new \Exception('Raised an exception');
    }
}
