<?php

namespace Zf2Mvc\Controller\TestAsset;

use Zf2Mvc\Controller\ActionController;

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
