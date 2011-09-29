<?php

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\Mvc\Controller\ActionController;

class SampleController extends ActionController
{
    public function testAction()
    {
        return array('content' => 'test');
    }

    public function testSomeStrangelySeparatedWordsAction()
    {
        return array('content' => 'Test Some Strangely Separated Words');
    }
}
