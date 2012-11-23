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
use Zend\Http\Cookies;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Http\Exception;
use Zend\Http\Header\SetCookie;

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
}
