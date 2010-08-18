<?php

namespace ZendTest\Rest\TestAsset;

use Zend\Controller\Request\AbstractRequest,
    Zend\Controller\Response\AbstractResponse,
    Zend\Rest\Controller as RESTController;

class TestController extends RESTController
{
    public $testValue = '';
    public function __construct(AbstractRequest$request,
                                AbstractResponse $response,
                                array $invokeArgs = array())
    {
        $this->testValue = '';
    }
    public function indexAction()
    {
        $this->testValue = 'indexAction';
    }
    public function getAction()
    {
        $this->testValue = 'getAction';
    }
    public function postAction()
    {
        $this->testValue = 'postAction';
    }
    public function putAction()
    {
        $this->testValue = 'putAction';
    }
    public function deleteAction()
    {
        $this->testValue = 'deleteAction';
    }

}
