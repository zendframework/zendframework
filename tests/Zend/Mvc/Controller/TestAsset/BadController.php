<?php

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\Mvc\Controller\AbstractActionController;

class BadController extends AbstractActionController
{
    public function testAction()
    {
        throw new \Exception('Raised an exception');
    }
}
