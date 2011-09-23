<?php

namespace Zend\Mvc\Controller\TestAsset;

use Zend\Mvc\Controller\ActionController;

class BadController extends ActionController
{
    public function testAction()
    {
        echo "In " . __METHOD__ . "\n";
        throw new \Exception('Raised an exception');
    }
}
