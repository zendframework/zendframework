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
 * @package    Zend_Rest
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Rest;

use Zend\Rest\Client,
    Zend\Uri,
    Zend\Http\Response;

/**
 * Test cases for RESTClient
 *
 * @category   Zend
 * @package    Zend_Rest
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Rest
 * @group      Zend_Rest_Client
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->path = __DIR__ . '/TestAsset/responses/';

        $this->adapter = new \Zend\Http\Client\Adapter\Test();
        $client        = new \Zend\Http\Client(null, array(
            'adapter' => $this->adapter
        ));

        $this->rest = new Client\RestClient('http://framework.zend.com/');
        $this->rest->setHttpClient($client);
    }

    public function testUri()
    {
        $client = new Client\RestClient('http://framework.zend.com/rest/');
        $uri = $client->getUri();
        $this->assertTrue($uri instanceof Uri\Uri);
        $this->assertEquals('http://framework.zend.com/rest/', $uri->toString());

        $client->setUri(Uri\UriFactory::factory('http://framework.zend.com/soap/'));
        $uri = $client->getUri();
        $this->assertTrue($uri instanceof Uri\Uri);
        $this->assertEquals('http://framework.zend.com/soap/', $uri->toString());

        $client->setUri('http://framework.zend.com/xmlrpc/');
        $uri = $client->getUri();
        $this->assertTrue($uri instanceof Uri\Uri);
        $this->assertEquals('http://framework.zend.com/xmlrpc/', $uri->toString());
    }

    public function testRestGetThrowsExceptionWithNoUri()
    {
        $expXml   = file_get_contents($this->path . 'returnString.xml');
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/5.2.0\r\n"
                  . "Content-type: text/xml\r\n"
                  . "Content-length: " . strlen($expXml) . "\r\n"
                  . "Server: Apache/1.3.34 (Unix) PHP/5.2.0)\r\n"
                  . "Date: Tue, 06 Feb 2007 15:01:47 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . $expXml;
        $this->adapter->setResponse($response);

        $rest = new Client\RestClient();

        $this->setExpectedException('Zend\Rest\Client\Exception\UnexpectedValueException', 'URI object must be set before performing call');
        $response = $rest->restGet('/rest/');
    }

    public function testRestFixesPathWithMissingSlashes()
    {
        $expXml   = file_get_contents($this->path . 'returnString.xml');
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/5.2.0\r\n"
                  . "Content-type: text/xml\r\n"
                  . "Content-length: " . strlen($expXml) . "\r\n"
                  . "Server: Apache/1.3.34 (Unix) PHP/5.2.0)\r\n"
                  . "Date: Tue, 06 Feb 2007 15:01:47 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . $expXml;
        $this->adapter->setResponse($response);

        $rest = new Client\RestClient('http://framework.zend.com');
        $rest->setHttpClient($this->rest->getHttpClient());

        $response = $rest->restGet('rest');
        $this->assertTrue($response instanceof Response);
        $this->assertContains($expXml, $response->getBody(), $response->getBody());
    }

    public function testRestGet()
    {
        $expXml   = file_get_contents($this->path . 'returnString.xml');
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/5.2.0\r\n"
                  . "Content-type: text/xml\r\n"
                  . "Content-length: " . strlen($expXml) . "\r\n"
                  . "Server: Apache/1.3.34 (Unix) PHP/5.2.0)\r\n"
                  . "Date: Tue, 06 Feb 2007 15:01:47 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . $expXml;
        $this->adapter->setResponse($response);

        $response = $this->rest->restGet('/rest/');
        $this->assertTrue($response instanceof Response);
        $this->assertContains($expXml, $response->getBody());
    }

    public function testRestPost()
    {
        $expXml   = file_get_contents($this->path . 'returnString.xml');
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/5.2.0\r\n"
                  . "Content-type: text/xml\r\n"
                  . "Content-length: " . strlen($expXml) . "\r\n"
                  . "Server: Apache/1.3.34 (Unix) PHP/5.2.0)\r\n"
                  . "Date: Tue, 06 Feb 2007 15:01:47 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . $expXml;
        $this->adapter->setResponse($response);

        $reqXml   = file_get_contents($this->path . 'returnInt.xml');
        $response = $this->rest->restPost('/rest/', $reqXml);
        $this->assertTrue($response instanceof Response);
        $body = $response->getBody();
        $this->assertContains($expXml, $response->getBody());

        $request = $this->rest->getHttpClient()->getLastRawRequest();
        $this->assertContains($reqXml, $request, $request);
    }

    public function testRestPostWithArrayData()
    {
        $expXml   = file_get_contents($this->path . 'returnString.xml');
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/5.2.0\r\n"
                  . "Content-type: text/xml\r\n"
                  . "Content-length: " . strlen($expXml) . "\r\n"
                  . "Server: Apache/1.3.34 (Unix) PHP/5.2.0)\r\n"
                  . "Date: Tue, 06 Feb 2007 15:01:47 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . $expXml;
        $this->adapter->setResponse($response);

        $response = $this->rest->restPost('/rest/', array('foo' => 'bar', 'baz' => 'bat'));
        $this->assertTrue($response instanceof Response);
        $body = $response->getBody();
        $this->assertContains($expXml, $response->getBody());

        $request = $this->rest->getHttpClient()->getLastRawRequest();
        $this->assertContains('foo=bar&baz=bat', $request, $request);
    }

    public function testRestPut()
    {
        $expXml   = file_get_contents($this->path . 'returnString.xml');
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/5.2.0\r\n"
                  . "Content-type: text/xml\r\n"
                  . "Content-length: " . strlen($expXml) . "\r\n"
                  . "Server: Apache/1.3.34 (Unix) PHP/5.2.0)\r\n"
                  . "Date: Tue, 06 Feb 2007 15:01:47 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . $expXml;
        $this->adapter->setResponse($response);

        $reqXml   = file_get_contents($this->path . 'returnInt.xml');
        $response = $this->rest->restPut('/rest/', $reqXml);
        $this->assertTrue($response instanceof Response);
        $body = $response->getBody();
        $this->assertContains($expXml, $response->getBody());

        $request = $this->rest->getHttpClient()->getLastRawRequest();
        $this->assertContains($reqXml, $request, $request);
    }

    public function testRestDelete()
    {
        $expXml   = file_get_contents($this->path . 'returnString.xml');
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/5.2.0\r\n"
                  . "Content-type: text/xml\r\n"
                  . "Content-length: " . strlen($expXml) . "\r\n"
                  . "Server: Apache/1.3.34 (Unix) PHP/5.2.0)\r\n"
                  . "Date: Tue, 06 Feb 2007 15:01:47 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . $expXml;
        $this->adapter->setResponse($response);

        $reqXml   = file_get_contents($this->path . 'returnInt.xml');
        $response = $this->rest->restDelete('/rest/');
        $this->assertTrue($response instanceof Response);
        $body = $response->getBody();
        $this->assertContains($expXml, $response->getBody());
    }

    public function testCallWithHttpMethod()
    {
        $expXml   = file_get_contents($this->path . 'returnString.xml');
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/5.2.0\r\n"
                  . "Content-type: text/xml\r\n"
                  . "Content-length: " . strlen($expXml) . "\r\n"
                  . "Server: Apache/1.3.34 (Unix) PHP/5.2.0)\r\n"
                  . "Date: Tue, 06 Feb 2007 15:01:47 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . $expXml;
        $this->adapter->setResponse($response);

        $response = $this->rest->get('/rest/');
        $this->assertTrue($response instanceof Client\Result);
        $this->assertTrue($response->isSuccess());
        $this->assertEquals('string', $response->response());
    }

    public function testCallAsObjectMethodReturnsClient()
    {
        $expXml   = file_get_contents($this->path . 'returnString.xml');
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/5.2.0\r\n"
                  . "Content-type: text/xml\r\n"
                  . "Content-length: " . strlen($expXml) . "\r\n"
                  . "Server: Apache/1.3.34 (Unix) PHP/5.2.0)\r\n"
                  . "Date: Tue, 06 Feb 2007 15:01:47 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . $expXml;
        $this->adapter->setResponse($response);

        $response = $this->rest->doStuff('why', 'not');
        $this->assertTrue($response instanceof Client\RestClient);
        $this->assertSame($this->rest, $response);
    }

    public function testCallAsObjectMethodChainPerformsRequest()
    {
        $expXml   = file_get_contents($this->path . 'returnString.xml');
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/5.2.0\r\n"
                  . "Content-type: text/xml\r\n"
                  . "Content-length: " . strlen($expXml) . "\r\n"
                  . "Server: Apache/1.3.34 (Unix) PHP/5.2.0)\r\n"
                  . "Date: Tue, 06 Feb 2007 15:01:47 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . $expXml;
        $this->adapter->setResponse($response);

        $response = $this->rest->doStuff('why', 'not')->get();
        $this->assertTrue($response instanceof Client\Result);
        $this->assertEquals('string', $response->response());
    }

    /**
     * @group ZF-3705
     * @group ZF-3647
     */
    public function testInvalidXmlInClientResultLeadsToException()
    {
        $this->setExpectedException('Zend\Rest\Client\Exception\ResultException', 'REST Response Error');
        $result = new Client\Result("invalidxml");
    }
}
