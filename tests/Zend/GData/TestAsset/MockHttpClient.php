<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\TestAsset;

/**
 * @category   Zend
 * @package    Zend_GData
 * @subpackage UnitTests
 */
class Request
{
    public $method;
    public $uri;
    public $http_ver;
    public $headers;
    public $body;
}

/**
 * @category   Zend
 * @package    Zend_GData
 * @subpackage UnitTests
 */
class MockHttpClient extends \Zend\Http\Client\Adapter\Test
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
        $request = new Request();
        $request->method = $method;
        $request->uri = $uri;
        $request->http_ver = $http_ver;
        $request->headers = $headers;
        $request->body = $body;
        array_push($this->_requests, $request);
        return parent::write($method, $uri, $http_ver, $headers, $body);
    }
}
