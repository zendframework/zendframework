<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Json
 */

namespace ZendTest\Json\Server;

use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Test as TestAdapter;
use Zend\Json\Server\Client;
use Zend\Json\Server\Error;
use Zend\Json\Server\Request;
use Zend\Json\Server\Response;

/**
 * @category   Zend
 * @package    Zend_Json
 * @subpackage UnitTests
 * @group      Zend_XmlRpc
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend\Http\Client\Adapter\Test
     */
    protected $httpAdapter;

    /**
     * @var Zend\Http\Client
     */
    protected $httpClient;

    /**
     * @var Zend\Json\Server\Client
     */
    protected $jsonClient;

    public function setUp()
    {
        $this->httpAdapter = new TestAdapter();
        $this->httpClient = new HttpClient('http://foo',
                                    array('adapter' => $this->httpAdapter));

        $this->jsonClient = new Client('http://foo');
        $this->jsonClient->setHttpClient($this->httpClient);
    }

    // HTTP Client

    public function testGettingDefaultHttpClient()
    {
        $jsonClient = new Client('http://foo');
        $httpClient = $jsonClient->getHttpClient();
        //$this->assertInstanceOf('Zend\\Http\\Client', $httpClient);
        $this->assertSame($httpClient, $jsonClient->getHttpClient());
    }

    public function testSettingAndGettingHttpClient()
    {
        $jsonClient = new Client('http://foo');
        $this->assertNotSame($this->httpClient, $jsonClient->getHttpClient());

        $jsonClient->setHttpClient($this->httpClient);
        $this->assertSame($this->httpClient, $jsonClient->getHttpClient());
    }

    public function testSettingHttpClientViaContructor()
    {
        $jsonClient = new Client('http://foo', $this->httpClient);
        $httpClient   = $jsonClient->getHttpClient();
        $this->assertSame($this->httpClient, $httpClient);
    }

    // Request & Response

    public function testLastRequestAndResponseAreInitiallyNull()
    {
        $this->assertNull($this->jsonClient->getLastRequest());
        $this->assertNull($this->jsonClient->getLastResponse());
    }

    public function testLastRequestAndResponseAreSetAfterRpcMethodCall()
    {
        $this->setServerResponseTo(true);
        $this->jsonClient->call('foo');

        //$this->assertInstanceOf('Zend\\Json\\Server\\Request', $this->jsonClient->getLastRequest());
        //$this->assertInstanceOf('Zend\\Json\\Server\\Response', $this->jsonClient->getLastResponse());
    }

    public function testSuccessfulRpcMethodCallWithNoParameters()
    {
        $expectedMethod = 'foo';
        $expectedReturn = 7;

        $this->setServerResponseTo($expectedReturn);
        $this->assertSame($expectedReturn, $this->jsonClient->call($expectedMethod));

        $request  = $this->jsonClient->getLastRequest();
        $response = $this->jsonClient->getLastResponse();

        $this->assertSame($expectedMethod, $request->getMethod());
        $this->assertSame(array(), $request->getParams());
        $this->assertSame($expectedReturn, $response->getResult());
        $this->assertFalse($response->isError());
    }

    public function testSuccessfulRpcMethodCallWithParameters()
    {
        $expectedMethod = 'foobar';
        $expectedParams = array(1, 1.1, true, 'foo' => 'bar');
        $expectedReturn = array(7, false, 'foo' => 'bar');

        $this->setServerResponseTo($expectedReturn);

        $actualReturn = $this->jsonClient->call($expectedMethod, $expectedParams);
        $this->assertSame($expectedReturn, $actualReturn);

        $request  = $this->jsonClient->getLastRequest();
        $response = $this->jsonClient->getLastResponse();

        $this->assertSame($expectedMethod, $request->getMethod());
        $params = $request->getParams();
        $this->assertSame(count($expectedParams), count($params));
        $this->assertSame($expectedParams[0], $params[0]);
        $this->assertSame($expectedParams[1], $params[1]);
        $this->assertSame($expectedParams[2], $params[2]);
        $this->assertSame($expectedParams['foo'], $params['foo']);

        $this->assertSame($expectedReturn, $response->getResult());
        $this->assertFalse($response->isError());
    }

    // Faults

    public function testRpcMethodCallThrowsOnHttpFailure()
    {
        $status  = 404;
        $message = 'Not Found';
        $body    = 'oops';

        $response = $this->makeHttpResponseFrom($body, $status, $message);
        $this->httpAdapter->setResponse($response);

        $this->setExpectedException('Zend\\Json\\Server\\Exception\\HttpException', $message, $status);
        $this->jsonClient->call('foo');
    }

    public function testRpcMethodCallThrowsOnJsonRpcFault()
    {
        $code = -32050;
        $message = 'foo';

        $error = new Error($message, $code);
        $response = new Response();
        $response->setError($error);
        $json = $response->toJson();

        $response = $this->makeHttpResponseFrom($json);
        $this->httpAdapter->setResponse($response);

        $this->setExpectedException('Zend\\Json\\Server\\Exception\\ErrorException', $message, $code);
        $this->jsonClient->call('foo');
    }

    // HTTP handling

    public function testSettingUriOnHttpClientIsNotOverwrittenByJsonRpcClient()
    {
        $changedUri = 'http://bar:80/';
        // Overwrite: http://foo:80
        $this->setServerResponseTo(null);
        $this->jsonClient->getHttpClient()->setUri($changedUri);
        $this->jsonClient->call('foo');
        $uri = $this->jsonClient->getHttpClient()->getUri()->toString();

        $this->assertEquals($changedUri, $uri);
    }

    public function testSettingNoHttpClientUriForcesClientToSetUri()
    {
        $baseUri = 'http://foo:80/';
        $this->httpAdapter = new TestAdapter();
        $this->httpClient = new HttpClient(null, array('adapter' => $this->httpAdapter));

        $this->jsonClient = new Client($baseUri);
        $this->jsonClient->setHttpClient($this->httpClient);

        $this->setServerResponseTo(null);
        $this->assertNull($this->jsonClient->getHttpClient()->getRequest()->getUriString());
        $this->jsonClient->call('foo');
        $uri = $this->jsonClient->getHttpClient()->getUri();

        $this->assertEquals($baseUri, $uri->toString());
    }

    public function testCustomHttpClientUserAgentIsNotOverridden()
    {
        $this->assertFalse(
            $this->httpClient->getHeader('User-Agent'),
            'UA is null if no request was made'
        );
        $this->setServerResponseTo(null);
        $this->assertNull($this->jsonClient->call('method'));
        $this->assertSame(
            'Zend_Json_Server_Client',
            $this->httpClient->getHeader('User-Agent'),
            'If no custom UA is set, set Zend_Json_Server_Client'
        );

        $expectedUserAgent = 'Zend_Json_Server_Client (custom)';
        $this->httpClient->setHeaders(array('User-Agent' => $expectedUserAgent));

        $this->setServerResponseTo(null);
        $this->assertNull($this->jsonClient->call('method'));
        $this->assertSame($expectedUserAgent, $this->httpClient->getHeader('User-Agent'));
    }

    // Helpers
    public function setServerResponseTo($nativeVars)
    {
        $response = $this->getServerResponseFor($nativeVars);
        $this->httpAdapter->setResponse($response);
    }

    public function getServerResponseFor($nativeVars)
    {
        $response = new Response();
        $response->setResult($nativeVars);
        $json = $response->toJson();

        $response = $this->makeHttpResponseFrom($json);
        return $response;
    }

    public function makeHttpResponseFrom($data, $status=200, $message='OK')
    {
        $headers = array("HTTP/1.1 $status $message",
                         "Status: $status",
                         'Content-Type: application/json',
                         'Content-Length: ' . strlen($data)
                         );
        return implode("\r\n", $headers) . "\r\n\r\n$data\r\n\r\n";
    }

    public function makeHttpResponseFor($nativeVars)
    {
        $response = $this->getServerResponseFor($nativeVars);
        return HttpResponse::fromString($response);
    }

    public function mockHttpClient()
    {
        $this->mockedHttpClient = $this->getMock('Zend\\Http\\Client');
        $this->jsonClient->setHttpClient($this->mockedHttpClient);
    }
}
