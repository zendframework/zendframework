<?php

namespace ZendTest\REST\TestAsset;

class TestController extends \Zend\REST\Controller
{
    public $testValue = '';
    public function __construct(\Zend_Controller_Request_Abstract$request,
                                \Zend_Controller_Response_Abstract $response,
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
