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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\REST;

use Zend\REST\Client,
    Zend\Uri,
    Zend\HTTP\Response;

/**
 * Test cases for RESTClient
 *
 * @category   Zend
 * @package    Zend_Rest
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Rest
 * @group      Zend_Rest_Client
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->path = __DIR__ . '/TestAsset/responses/';

        $this->adapter = new \Zend\HTTP\Client\Adapter\Test();
        $client        = new \Zend\HTTP\Client(null, array(
            'adapter' => $this->adapter
        ));
        Client\RESTClient::setDefaultHTTPClient($client);

        $this->rest = new Client\RESTClient('http://framework.zend.com/');
    }

    public function testUri()
    {
        $client = new Client\RESTClient('http://framework.zend.com/rest/');
        $uri = $client->getUri();
        $this->assertTrue($uri instanceof URI\URI);
        $this->assertEquals('http://framework.zend.com/rest/', $uri->generate());

        $client->setUri(new URI\URL('http://framework.zend.com/soap/'));
        $uri = $client->getUri();
        $this->assertTrue($uri instanceof URI\URL);
        $this->assertEquals('http://framework.zend.com/soap/', $uri->generate());

        $client->setUri('http://framework.zend.com/xmlrpc/');
        $uri = $client->getUri();
        $this->assertTrue($uri instanceof URI\URL);
        $this->assertEquals('http://framework.zend.com/xmlrpc/', $uri->generate());
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

        $rest = new Client\RESTClient();

        $this->setExpectedException('Zend\\REST\\Client\\Exception');
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

        $rest = new Client\RESTClient('http://framework.zend.com');

        $response = $rest->restGet('rest');
        $this->assertTrue($response instanceof Response\Response);
        $this->assertContains($expXml, $response->getBody());
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
        $this->assertTrue($response instanceof Response\Response);
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
        $this->assertTrue($response instanceof Response\Response);
        $body = $response->getBody();
        $this->assertContains($expXml, $response->getBody());

        $request = Client\RESTClient::getDefaultHTTPClient()->getLastRequest();
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
        $this->assertTrue($response instanceof Response\Response);
        $body = $response->getBody();
        $this->assertContains($expXml, $response->getBody());

        $request = Client\RESTClient::getDefaultHTTPClient()->getLastRequest();
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
        $this->assertTrue($response instanceof Response\Response);
        $body = $response->getBody();
        $this->assertContains($expXml, $response->getBody());

        $request = Client\RESTClient::getDefaultHTTPClient()->getLastRequest();
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
        $this->assertTrue($response instanceof Response\Response);
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
        $this->assertTrue($response instanceof Client\RESTClient);
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
        $this->setExpectedException('Zend\\REST\\Client\\ResultException');
        $result = new Client\Result("invalidxml");
    }
}
