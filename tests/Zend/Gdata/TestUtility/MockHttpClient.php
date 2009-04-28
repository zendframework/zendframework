<?php

require_once 'Zend/Http/Client/Adapter/Test.php';

class Test_Zend_Gdata_MockHttpClient_Request
{
    public $methd;
    public $uri;
    public $http_ver;
    public $headers;
    public $body;
}

class Test_Zend_Gdata_MockHttpClient extends Zend_Http_Client_Adapter_Test
{
    protected $_requests;
    
    public function __construct()
    {
        parent::__construct();
        $this->_requests = array();
    }
        
    public function popRequest()
    {
        if (count($this->_requests))
            return array_pop($this->_requests);
        else
            return NULL;
    }
    
    public function write($method,
                          $uri,
                          $http_ver = '1.1',
                          $headers = array(),
                          $body = '')
    {
        $request = new Test_Zend_Gdata_MockHttpClient_Request();
        $request->method = $method;
        $request->uri = $uri;
        $request->http_ver = $http_ver;
        $request->headers = $headers;
        $request->body = $body;
        array_push($this->_requests, $request);
        return parent::write($method, $uri, $http_ver, $headers, $body);
    }
}