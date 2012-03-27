<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\TestAsset;

/**
 * @category   Zend
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Request
{
    public $methd;
    public $uri;
    public $http_ver;
    public $headers;
    public $body;
}

/**
 * @category   Zend
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
