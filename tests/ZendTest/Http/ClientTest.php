<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace ZendTest\Http;

use Zend\Http\Client;

use Zend\Http\Header\AcceptEncoding;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testClientRetrievesUppercaseHttpMethodFromRequestObject()
    {
        $client = new Client;
        $client->setMethod('post');
        $this->assertEquals(Client::ENC_URLENCODED, $client->getEncType());
    }

    public function testIfZeroValueCookiesCanBeSet()
    {
        $client = new Client();
        $client->addCookie("test", 0);
        $client->addCookie("test2", "0");
        $client->addCookie("test3", false);
    }

    /**
    * @expectedException Zend\Http\Exception\InvalidArgumentException
    */
    public function testIfNullValueCookiesThrowsException()
    {
        $client = new Client();
        $client->addCookie("test", null);
    }

    public function testIfCookieHeaderCanBeSet()
    {
        $header = new SetCookie('foo');

        $client = new Client();
        $client->addCookie($header);

        $cookies = $client->getCookies();
        $this->assertEquals(1, count($cookies));
        $this->assertEquals($header, $cookies['foo']);
    }

    public function testIfArrayOfHeadersCanBeSet()
    {
        $headers = array(
            new SetCookie('foo'),
            new SetCookie('bar')
        );

        $client = new Client();
        $client->addCookie($headers);

        $cookies = $client->getCookies();
        $this->assertEquals(2, count($cookies));
    }

    public function testIfArrayIteratorOfHeadersCanBeSet()
    {
        $headers = new \ArrayIterator(array(
            new SetCookie('foo'),
            new SetCookie('bar')
        ));

        $client = new Client();
        $client->addCookie($headers);

        $cookies = $client->getCookies();
        $this->assertEquals(2, count($cookies));
    }

    public function testClientUsesAcceptEncodingHeaderFromRequestObject()
    {
        $client = new Client();

        $client->setAdapter('Zend\Http\Client\Adapter\Test');

        $request = $client->getRequest();

        $acceptEncodingHeader = new AcceptEncoding();
        $acceptEncodingHeader->addEncoding('foo', 1);
        $request->getHeaders()->addHeader($acceptEncodingHeader);

        $client->send();

        $rawRequest = $client->getLastRawRequest();

        $this->assertNotContains('Accept-Encoding: gzip, deflate', $rawRequest, null, true);
        $this->assertNotContains('Accept-Encoding: identity', $rawRequest, null, true);

        $this->assertContains('Accept-Encoding: foo', $rawRequest);
    }
}
