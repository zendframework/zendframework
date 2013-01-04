<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace ZendTest\Http;

use ReflectionClass;
use Zend\Http\Client;
use Zend\Http\Cookies;
use Zend\Http\Exception;
use Zend\Http\Header\AcceptEncoding;
use Zend\Http\Header\SetCookie;
use Zend\Http\Request;
use Zend\Http\Response;


class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testIfCookiesAreSticky()
    {
        $initialCookies = array(
            new SetCookie('foo', 'far', null, '/', 'www.domain.com' ),
            new SetCookie('bar', 'biz', null, '/', 'www.domain.com')
        );

        $requestString = "GET http://www.domain.com/index.php HTTP/1.1\r\nHost: domain.com\r\nUser-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:16.0) Gecko/20100101 Firefox/16.0\r\nAccept: */*\r\nAccept-Language: en-US,en;q=0.5\r\nAccept-Encoding: gzip, deflate\r\nConnection: keep-alive\r\n";
        $request = Request::fromString($requestString);

        $client = new Client('http://www.domain.com/');
        $client->setRequest($request);
        $client->addCookie($initialCookies);

        $cookies = new Cookies($client->getRequest()->getHeaders());
        $rawHeaders = "HTTP/1.1 200 OK\r\nAccess-Control-Allow-Origin: *\r\nContent-Encoding: gzip\r\nContent-Type: application/javascript\r\nDate: Sun, 18 Nov 2012 16:16:08 GMT\r\nServer: nginx/1.1.19\r\nSet-Cookie: baz=bah; domain=www.domain.com; path=/\r\nSet-Cookie: joe=test; domain=www.domain.com; path=/\r\nVary: Accept-Encoding\r\nX-Powered-By: PHP/5.3.10-1ubuntu3.4\r\nConnection: keep-alive\r\n";
        $response = Response::fromString($rawHeaders);
        $client->setResponse($response);

        $cookies->addCookiesFromResponse($client->getResponse(), $client->getUri());

        $client->addCookie( $cookies->getMatchingCookies($client->getUri()) );

        $this->assertEquals(4, count($client->getCookies()));
    }

    public function testClientRetrievesUppercaseHttpMethodFromRequestObject()
    {
        $client = new Client;
        $client->setMethod('post');
        $this->assertEquals(Client::ENC_URLENCODED, $client->getEncType());
    }

    public function testAcceptEncodingHeaderWorksProperly()
    {
        $method = new \ReflectionMethod('\Zend\Http\Client', 'prepareHeaders');
        $method->setAccessible(true);

        $requestString = "GET http://www.domain.com/index.php HTTP/1.1\r\nHost: domain.com\r\nUser-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:16.0) Gecko/20100101 Firefox/16.0\r\nAccept: */*\r\nAccept-Language: en-US,en;q=0.5\r\nAccept-Encoding: gzip, deflate\r\nConnection: keep-alive\r\n";
        $request = Request::fromString($requestString);

        $adapter = new \Zend\Http\Client\Adapter\Test();

        $client = new \Zend\Http\Client('http://www.domain.com/');
        $client->setAdapter($adapter);
        $client->setRequest($request);

        $rawHeaders = "HTTP/1.1 200 OK\r\nAccess-Control-Allow-Origin: *\r\nContent-Encoding: gzip, deflate\r\nContent-Type: application/javascript\r\nDate: Sun, 18 Nov 2012 16:16:08 GMT\r\nServer: nginx/1.1.19\r\nVary: Accept-Encoding\r\nX-Powered-By: PHP/5.3.10-1ubuntu3.4\r\nConnection: keep-alive\r\n";
        $response = Response::fromString($rawHeaders);
        $client->getAdapter()->setResponse($response);

        $headers = $method->invoke($client, $requestString, $client->getUri());
        $this->assertEquals('gzip, deflate', $headers['Accept-Encoding']);
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
        $header = array(new SetCookie('foo', 'bar'));
        $client = new Client();
        $client->addCookie($header);

        $cookies = $client->getCookies();
        $this->assertEquals(1, count($cookies));
        $this->assertEquals($header[0], $cookies['foo']);
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

    /**
     * @group 2774
     * @group 2745
     */
    public function testArgSeparatorDefaultsToIniSetting()
    {
        $argSeparator = ini_get('arg_separator.output');
        $client = new Client();
        $this->assertEquals($argSeparator, $client->getArgSeparator());
    }

    /**
     * @group 2774
     * @group 2745
     */
    public function testCanOverrideArgSeparator()
    {
        $client = new Client();
        $client->setArgSeparator(';');
        $this->assertEquals(';', $client->getArgSeparator());
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

    public function testEncodeAuthHeaderWorksAsExpected()
    {
        $encoded = Client::encodeAuthHeader('test', 'test');
        $this->assertEquals('Basic ' . base64_encode('test:test'), $encoded);
    }

    /**
     * @expectedException Zend\Http\Client\Exception\InvalidArgumentException
     */
    public function testEncodeAuthHeaderThrowsExceptionWhenUsernameContainsSemiColon()
    {
        $encoded = Client::encodeAuthHeader('test:', 'test');
    }

    /**
     * @expectedException Zend\Http\Client\Exception\InvalidArgumentException
     */
    public function testEncodeAuthHeaderThrowsExceptionWhenInvalidAuthTypeIsUsed()
    {
        $encoded = Client::encodeAuthHeader('test', 'test', 'test');
    }
}
