<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http;

use Zend\Uri\Http;
use Zend\Http\Client;
use Zend\Http\Cookies;
use Zend\Http\Header\AcceptEncoding;
use Zend\Http\Header\SetCookie;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Http\Client\Adapter\Test;
use ZendTest\Http\TestAsset\ExtendedClient;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testIfCookiesAreSticky()
    {
        $initialCookies = array(
            new SetCookie('foo', 'far', null, '/', 'www.domain.com'),
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

        $client->addCookie($cookies->getMatchingCookies($client->getUri()));

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

    public function testIfMaxredirectWorksCorrectly()
    {
        $testAdapter = new Test();
        // first response, contains a redirect
        $testAdapter->setResponse(
            "HTTP/1.1 303 See Other\r\n"
            . "Location: http://www.example.org/part2\r\n\r\n"
            . "Page #1"
        );
        // seconds response, contains a redirect
        $testAdapter->addResponse(
            "HTTP/1.1 303 See Other\r\n"
            . "Location: http://www.example.org/part3\r\n\r\n"
            . "Page #2"
        );
        // third response
        $testAdapter->addResponse(
            "HTTP/1.1 303 See Other\r\n\r\n"
            . "Page #3"
        );

        // create a client which allows one redirect at most!
        $client = new Client('http://www.example.org/part1', array(
            'adapter' => $testAdapter,
            'maxredirects' => 1,
            'storeresponse' => true
        ));

        // do the request
        $response = $client->setMethod('GET')->send();

        // response should be the second response, since third response should not
        // be requested, due to the maxredirects = 1 limit
        $this->assertEquals($response->getContent(), "Page #2");
    }

    public function testIfClientDoesNotLooseAuthenticationOnRedirect()
    {
        // set up user credentials
        $user = 'username123';
        $password = 'password456';
        $encoded = Client::encodeAuthHeader($user, $password, Client::AUTH_BASIC);

        // set up two responses that simulate a redirection
        $testAdapter = new Test();
        $testAdapter->setResponse(
            "HTTP/1.1 303 See Other\r\n"
            . "Location: http://www.example.org/part2\r\n\r\n"
            . "The URL of this page has changed."
        );
        $testAdapter->addResponse(
            "HTTP/1.1 200 OK\r\n\r\n"
            . "Welcome to this Website."
        );

        // create client with HTTP basic authentication
        $client = new Client('http://www.example.org/part1', array(
            'adapter' => $testAdapter,
            'maxredirects' => 1
        ));
        $client->setAuth($user, $password, Client::AUTH_BASIC);

        // do request
        $response = $client->setMethod('GET')->send();

        // the last request should contain the Authorization header
        $this->assertContains($encoded, $client->getLastRawRequest());
    }

    public function testIfClientDoesNotForwardAuthenticationToForeignHost()
    {
        // set up user credentials
        $user = 'username123';
        $password = 'password456';
        $encoded = Client::encodeAuthHeader($user, $password, Client::AUTH_BASIC);

        $testAdapter = new Test();
        $client = new Client(null, array('adapter' => $testAdapter));

        // set up two responses that simulate a redirection from example.org to example.com
        $testAdapter->setResponse(
            "HTTP/1.1 303 See Other\r\n"
            . "Location: http://example.com/part2\r\n\r\n"
            . "The URL of this page has changed."
        );
        $testAdapter->addResponse(
            "HTTP/1.1 200 OK\r\n\r\n"
            . "Welcome to this Website."
        );

        // set auth and do request
        $client->setUri('http://example.org/part1')
            ->setAuth($user, $password, Client::AUTH_BASIC);
        $response = $client->setMethod('GET')->send();

        // the last request should NOT contain the Authorization header,
        // because example.com is different from example.org
        $this->assertNotContains($encoded, $client->getLastRawRequest());

        // set up two responses that simulate a rediration from example.org to sub.example.org
        $testAdapter->setResponse(
            "HTTP/1.1 303 See Other\r\n"
            . "Location: http://sub.example.org/part2\r\n\r\n"
            . "The URL of this page has changed."
        );
        $testAdapter->addResponse(
            "HTTP/1.1 200 OK\r\n\r\n"
            . "Welcome to this Website."
        );

        // set auth and do request
        $client->setUri('http://example.org/part1')
            ->setAuth($user, $password, Client::AUTH_BASIC);
        $response = $client->setMethod('GET')->send();

        // the last request should contain the Authorization header,
        // because sub.example.org is a subdomain unter example.org
        $this->assertContains($encoded, $client->getLastRawRequest());

        // set up two responses that simulate a rediration from sub.example.org to example.org
        $testAdapter->setResponse(
            "HTTP/1.1 303 See Other\r\n"
            . "Location: http://example.org/part2\r\n\r\n"
            . "The URL of this page has changed."
        );
        $testAdapter->addResponse(
            "HTTP/1.1 200 OK\r\n\r\n"
            . "Welcome to this Website."
        );

        // set auth and do request
        $client->setUri('http://sub.example.org/part1')
            ->setAuth($user, $password, Client::AUTH_BASIC);
        $response = $client->setMethod('GET')->send();

        // the last request should NOT contain the Authorization header,
        // because example.org is not a subdomain unter sub.example.org
        $this->assertNotContains($encoded, $client->getLastRawRequest());
    }

    public function testAdapterAlwaysReachableIfSpecified()
    {
        $testAdapter = new Test();
        $client = new Client('http://www.example.org/', array(
            'adapter' => $testAdapter,
        ));

        $this->assertSame($testAdapter, $client->getAdapter());
    }

    public function testPrepareHeadersCreateRightHttpField()
    {
        $body = json_encode(array('foofoo'=>'barbar'));

        $client = new Client();
        $prepareHeadersReflection = new \ReflectionMethod($client, 'prepareHeaders');
        $prepareHeadersReflection->setAccessible(true);

        $request= new Request();
        $request->getHeaders()->addHeaderLine('content-type', 'application/json');
        $request->getHeaders()->addHeaderLine('content-length', strlen($body));
        $client->setRequest($request);

        $client->setEncType('application/json');

        $this->assertSame($client->getRequest(), $request);

        $headers = $prepareHeadersReflection->invoke($client, $body, new Http('http://localhost:5984'));

        $this->assertArrayNotHasKey('content-type', $headers);
        $this->assertArrayHasKey('Content-Type', $headers);

        $this->assertArrayNotHasKey('content-length', $headers);
        $this->assertArrayHasKey('Content-Length', $headers);
    }

    public function testPrepareHeadersCurlDigestAuthentication()
    {
        $body = json_encode(array('foofoo'=>'barbar'));

        $client = new Client();
        $prepareHeadersReflection = new \ReflectionMethod($client, 'prepareHeaders');
        $prepareHeadersReflection->setAccessible(true);

        $request = new Request();
        $request->getHeaders()->addHeaderLine('Authorization: Digest');
        $request->getHeaders()->addHeaderLine('content-type', 'application/json');
        $request->getHeaders()->addHeaderLine('content-length', strlen($body));
        $client->setRequest($request);

        $this->assertSame($client->getRequest(), $request);

        $headers = $prepareHeadersReflection->invoke($client, $body, new Http('http://localhost:5984'));

        $this->assertInternalType('array', $headers);
        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertContains('Digest', $headers['Authorization']);
    }

    /**
     * @group 6301
     */
    public function testCanSpecifyCustomAuthMethodsInExtendingClasses()
    {
        $client = new ExtendedClient();

        $client->setAuth('username', 'password', ExtendedClient::AUTH_CUSTOM);

        $this->assertAttributeEquals(
            array(
                'user'     => 'username',
                'password' => 'password',
                'type'     => ExtendedClient::AUTH_CUSTOM,
            ),
            'auth',
            $client
        );
    }

    /**
     * @group 6231
     */
    public function testHttpQueryParametersCastToString()
    {
        $client = new Client();

        /* @var $adapter \PHPUnit_Framework_MockObject_MockObject|\Zend\Http\Client\Adapter\AdapterInterface */
        $adapter = $this->getMock('Zend\Http\Client\Adapter\AdapterInterface');

        $client->setAdapter($adapter);

        $request = new Request();

        $request->setUri('http://example.com/');
        $request->getQuery()->set('foo', 'bar');

        $response = new Response();

        $adapter
            ->expects($this->once())
            ->method('write')
            ->with(Request::METHOD_GET, 'http://example.com/?foo=bar');

        $adapter
            ->expects($this->any())
            ->method('read')
            ->will($this->returnValue($response->toString()));

        $client->send($request);
    }

    /**
     * @group 6959
     */
    public function testClientRequestMethod()
    {
        $request = new Request;
        $request->setMethod(Request::METHOD_POST);
        $request->getPost()->set('data', 'random');

        $client = new Client;
        $client->setAdapter('Zend\Http\Client\Adapter\Test');
        $client->send($request);

        $this->assertSame(Client::ENC_URLENCODED, $client->getEncType());
    }

    /**
     * @group 7332
     */
    public function testAllowsClearingEncType()
    {
        $client = new Client();
        $client->setEncType('application/x-www-form-urlencoded');

        $this->assertEquals('application/x-www-form-urlencoded', $client->getEncType());

        $client->setEncType(null);
        $this->assertNull($client->getEncType());
    }
}
