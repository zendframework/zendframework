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
 * @package    Zend_Service_Nirvanix
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Nirvanix;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Client as HttpClient,
    Zend\Http\Client\Adapter\Test as TestAdapter,
    Zend\Service\Nirvanix\Nirvanix;

/**
 * @see        Zend\Service\Nirvanix\Nirvanix
 * @category   Zend
 * @package    Zend_Service_Nirvanix
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Nirvanix
 */
abstract class FunctionalTestCase extends TestCase
{
    public function setUp()
    {
        $this->httpAdapter = new TestAdapter();
        $this->httpClient  = new HttpClient(
            'http://foo',
            array('adapter' => $this->httpAdapter)
        );

        $this->auth    = array('username' => 'foo', 'password' => 'bar', 'appKey' => 'baz');
        $this->options = array('httpClient' => $this->httpClient);

        // set first nirvanix response to successful login
        $this->httpAdapter->setResponse(
            $this->makeNirvanixResponse(array(
                'ResponseCode' => '0',
                'SessionToken' => 'foo',
            ))
        );

        $this->nirvanix = new Nirvanix($this->auth, $this->options);
    }

    public function makeNirvanixResponse($hash)
    {
        $xml = "<?xml version='1.0'?><Response>";
        foreach ($hash as $k => $v) { 
            $xml .= "<$k>$v</$k>"; 
        }
        $xml .= "</Response>";

        $resp = $this->makeHttpResponseFrom($xml);
        return $resp;
    }

    public function makeHttpResponseFrom($data, $status = 200, $message = 'OK')
    {
        $headers = array(
            "HTTP/1.1 $status $message",
            "Status: $status",
            'Content_Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($data),
        );
        return implode("\r\n", $headers) . "\r\n\r\n$data\r\n\r\n";
    }
}
