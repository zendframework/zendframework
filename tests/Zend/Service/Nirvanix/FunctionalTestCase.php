<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Nirvanix;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Test as TestAdapter;
use Zend\Service\Nirvanix\Nirvanix;

/**
 * @category   Zend
 * @package    Zend_Service_Nirvanix
 * @subpackage UnitTests
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
