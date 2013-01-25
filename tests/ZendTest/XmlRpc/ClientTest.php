<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_XmlRpc
 */

namespace ZendTest\XmlRpc;

use Zend\Http\Client\Adapter;
use Zend\Http;
use Zend\Http\Response as HttpResponse;
use Zend\XmlRpc\Client;
use Zend\XmlRpc\AbstractValue;
use Zend\XmlRpc\Value;
use Zend\XmlRpc;

/**
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @group      Zend_XmlRpc
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Http_Client_Adapter_Abstract
     */
    protected $httpAdapter;

    /**
     * @var Zend_Http_Client
     */
    protected $httpClient;

    /**
     * @var Zend_XmlRpc_Client
     */
    protected $xmlrpcClient;

    public function setUp()
    {
        $this->httpAdapter = new Adapter\Test();
        $this->httpClient = new Http\Client('http://foo',
                                    array('adapter' => $this->httpAdapter));

        $this->xmlrpcClient = new Client('http://foo');
        $this->xmlrpcClient->setHttpClient($this->httpClient);
    }

    // HTTP Client

    public function testGettingDefaultHttpClient()
    {
        $xmlrpcClient = new Client('http://foo');
        $httpClient = $xmlrpcClient->getHttpClient();
        $this->assertInstanceOf('Zend\\Http\\Client', $httpClient);
        $this->assertSame($httpClient, $xmlrpcClient->getHttpClient());
    }

    public function testSettingAndGettingHttpClient()
    {
        $xmlrpcClient = new Client('http://foo');
        $httpClient = new Http\Client('http://foo');
        $this->assertNotSame($httpClient, $xmlrpcClient->getHttpClient());

        $xmlrpcClient->setHttpClient($httpClient);
        $this->assertSame($httpClient, $xmlrpcClient->getHttpClient());
    }

    public function testSettingHttpClientViaContructor()
    {
        $xmlrpcClient = new Client('http://foo', $this->httpClient);
        $httpClient   = $xmlrpcClient->getHttpClient();
        $this->assertSame($this->httpClient, $httpClient);
    }

    // Request & Response

    public function testLastRequestAndResponseAreInitiallyNull()
    {
        $this->assertNull($this->xmlrpcClient->getLastRequest());
        $this->assertNull($this->xmlrpcClient->getLastResponse());
    }

    public function testLastRequestAndResponseAreSetAfterRpcMethodCall()
    {
        $this->setServerResponseTo(true);
        $this->xmlrpcClient->call('foo');

        $this->assertInstanceOf('Zend\\XmlRpc\\Request', $this->xmlrpcClient->getLastRequest());
        $this->assertInstanceOf('Zend\\XmlRpc\\Response', $this->xmlrpcClient->getLastResponse());
    }

    public function testSuccessfulRpcMethodCallWithNoParameters()
    {
        $expectedMethod = 'foo.bar';
        $expectedReturn = 7;

        $this->setServerResponseTo($expectedReturn);
        $this->assertSame($expectedReturn, $this->xmlrpcClient->call($expectedMethod));

        $request  = $this->xmlrpcClient->getLastRequest();
        $response = $this->xmlrpcClient->getLastResponse();

        $this->assertSame($expectedMethod, $request->getMethod());
        $this->assertSame(array(), $request->getParams());
        $this->assertSame($expectedReturn, $response->getReturnValue());
        $this->assertFalse($response->isFault());
    }

    public function testSuccessfulRpcMethodCallWithParameters()
    {
        $expectedMethod = 'foo.bar';
        $expectedParams = array(1, 'foo' => 'bar', 1.1, true);
        $expectedReturn = array(7, false, 'foo' => 'bar');

        $this->setServerResponseTo($expectedReturn);

        $actualReturn = $this->xmlrpcClient->call($expectedMethod, $expectedParams);
        $this->assertSame($expectedReturn, $actualReturn);

        $request  = $this->xmlrpcClient->getLastRequest();
        $response = $this->xmlrpcClient->getLastResponse();

        $this->assertSame($expectedMethod, $request->getMethod());
        $params = $request->getParams();
        $this->assertSame(count($expectedParams), count($params));
        $this->assertSame($expectedParams[0], $params[0]->getValue());
        $this->assertSame($expectedParams[1], $params[1]->getValue());
        $this->assertSame($expectedParams[2], $params[2]->getValue());
        $this->assertSame($expectedParams['foo'], $params['foo']->getValue());

        $this->assertSame($expectedReturn, $response->getReturnValue());
        $this->assertFalse($response->isFault());
    }

    /**
     * @group ZF-2090
     */
    public function testSuccessfullyDetectsEmptyArrayParameterAsArray()
    {
        $expectedMethod = 'foo.bar';
        $expectedParams = array(array());
        $expectedReturn = array(true);

        $this->setServerResponseTo($expectedReturn);

        $actualReturn = $this->xmlrpcClient->call($expectedMethod, $expectedParams);
        $this->assertSame($expectedReturn, $actualReturn);

        $request  = $this->xmlrpcClient->getLastRequest();

        $params = $request->getParams();
        $this->assertSame(count($expectedParams), count($params));
        $this->assertSame($expectedParams[0], $params[0]->getValue());
    }

    /**
     * @group ZF-1412
     */
    public function testSuccessfulRpcMethodCallWithMixedDateParameters()
    {
        $time = time();
        $expectedMethod = 'foo.bar';
        $expectedParams = array(
            'username',
            new Value\DateTime($time)
        );
        $expectedReturn = array('username', $time);

        $this->setServerResponseTo($expectedReturn);

        $actualReturn = $this->xmlrpcClient->call($expectedMethod, $expectedParams);
        $this->assertSame($expectedReturn, $actualReturn);

        $request  = $this->xmlrpcClient->getLastRequest();
        $response = $this->xmlrpcClient->getLastResponse();

        $this->assertSame($expectedMethod, $request->getMethod());
        $params = $request->getParams();
        $this->assertSame(count($expectedParams), count($params));
        $this->assertSame($expectedParams[0], $params[0]->getValue());
        $this->assertSame($expectedParams[1], $params[1]);
        $this->assertSame($expectedReturn, $response->getReturnValue());
        $this->assertFalse($response->isFault());
    }

    /**
     * @group ZF-1797
     */
    public function testSuccesfulRpcMethodCallWithXmlRpcValueParameters()
    {
        $time   = time();
        $params = array(
            new Value\Boolean(true),
            new Value\Integer(4),
            new Value\String('foo')
        );
        $expect = array(true, 4, 'foo');

        $this->setServerResponseTo($expect);

        $result = $this->xmlrpcClient->call('foo.bar', $params);
        $this->assertSame($expect, $result);

        $request  = $this->xmlrpcClient->getLastRequest();
        $response = $this->xmlrpcClient->getLastResponse();

        $this->assertSame('foo.bar', $request->getMethod());
        $this->assertSame($params, $request->getParams());
        $this->assertSame($expect, $response->getReturnValue());
        $this->assertFalse($response->isFault());
    }

    /**
     * @group ZF-2978
     */
    public function testSkippingSystemCallDisabledByDefault()
    {
        $this->assertFalse($this->xmlrpcClient->skipSystemLookup());
    }

    /**
     * @group ZF-6993
     */
    public function testWhenPassingAStringAndAnIntegerIsExpectedParamIsConverted()
    {
        $this->mockIntrospector();
        $this->mockedIntrospector
             ->expects($this->exactly(2))
             ->method('getMethodSignature')
             ->with('test.method')
             ->will($this->returnValue(array(array('parameters' => array('int')))));

        $expect = 'test.method response';
        $this->setServerResponseTo($expect);

        $this->assertSame($expect, $this->xmlrpcClient->call('test.method', array('1')));
        $params = $this->xmlrpcClient->getLastRequest()->getParams();
        $this->assertSame(1, $params[0]->getValue());

        $this->setServerResponseTo($expect);
        $this->assertSame($expect, $this->xmlrpcClient->call('test.method', '1'));
        $params = $this->xmlrpcClient->getLastRequest()->getParams();
        $this->assertSame(1, $params[0]->getValue());
    }

    /**
     * @group ZF-8074
     */
    public function testXmlRpcObjectsAreNotConverted()
    {
        $this->mockIntrospector();
        $this->mockedIntrospector
             ->expects($this->exactly(1))
             ->method('getMethodSignature')
             ->with('date.method')
             ->will($this->returnValue(array(array('parameters' => array('dateTime.iso8601', 'string')))));

        $expects = 'date.method response';
        $this->setServerResponseTo($expects);
        $this->assertSame($expects, $this->xmlrpcClient->call('date.method', array(AbstractValue::getXmlRpcValue(time(), AbstractValue::XMLRPC_TYPE_DATETIME), 'foo')));
    }

    public function testAllowsSkippingSystemCallForArrayStructLookup()
    {
        $this->xmlrpcClient->setSkipSystemLookup(true);
        $this->assertTrue($this->xmlrpcClient->skipSystemLookup());
    }

    public function testSkipsSystemCallWhenDirected()
    {
        $httpAdapter = $this->httpAdapter;
        $response    = $this->makeHttpResponseFor('foo');
        $httpAdapter->setResponse($response);
        $this->xmlrpcClient->setSkipSystemLookup(true);
        $this->assertSame('foo', $this->xmlrpcClient->call('test.method'));
    }

    /**#@-*/

    // Faults

    public function testRpcMethodCallThrowsOnHttpFailure()
    {
        $status  = 404;
        $message = 'Not Found';
        $body    = 'oops';

        $response = $this->makeHttpResponseFrom($body, $status, $message);
        $this->httpAdapter->setResponse($response);

        $this->setExpectedException('Zend\XmlRpc\Client\Exception\HttpException', $message, $status);
        $this->xmlrpcClient->call('foo');
    }

    public function testRpcMethodCallThrowsOnXmlRpcFault()
    {
        $code = 9;
        $message = 'foo';

        $fault = new XmlRpc\Fault($code, $message);
        $xml = $fault->saveXml();

        $response = $this->makeHttpResponseFrom($xml);
        $this->httpAdapter->setResponse($response);

        $this->setExpectedException('Zend\XmlRpc\Client\Exception\FaultException', $message, $code);
        $this->xmlrpcClient->call('foo');
    }

    // Server Proxy

    public function testGetProxyReturnsServerProxy()
    {
        $this->assertInstanceOf('Zend\\XmlRpc\\Client\\ServerProxy', $this->xmlrpcClient->getProxy());
    }

    public function testRpcMethodCallsThroughServerProxy()
    {
        $expectedReturn = array(7, false, 'foo' => 'bar');
        $this->setServerResponseTo($expectedReturn);

        $server = $this->xmlrpcClient->getProxy();
        $this->assertSame($expectedReturn, $server->listMethods());

        $request = $this->xmlrpcClient->getLastRequest();
        $this->assertEquals('listMethods', $request->getMethod());
    }

    public function testRpcMethodCallsThroughNestedServerProxies()
    {
        $expectedReturn = array(7, false, 'foo' => 'bar');
        $this->setServerResponseTo($expectedReturn);

        $server = $this->xmlrpcClient->getProxy('foo');
        $this->assertSame($expectedReturn, $server->bar->baz->boo());

        $request = $this->xmlrpcClient->getLastRequest();
        $this->assertEquals('foo.bar.baz.boo', $request->getMethod());
    }

    public function testClientCachesServerProxies()
    {
        $proxy = $this->xmlrpcClient->getProxy();
        $this->assertSame($proxy, $this->xmlrpcClient->getProxy());

        $proxy = $this->xmlrpcClient->getProxy('foo');
        $this->assertSame($proxy, $this->xmlrpcClient->getProxy('foo'));
    }

    public function testServerProxyCachesNestedProxies()
    {
        $proxy = $this->xmlrpcClient->getProxy();

        $foo = $proxy->foo;
        $this->assertSame($foo, $proxy->foo);

        $bar = $proxy->foo->bar;
        $this->assertSame($bar, $proxy->foo->bar);
    }

    // Introspection

    public function testGettingDefaultIntrospector()
    {
        $xmlrpcClient = new Client('http://foo');
        $introspector = $xmlrpcClient->getIntrospector();
        $this->assertInstanceOf('Zend\\XmlRpc\\Client\\ServerIntrospection', $introspector);
        $this->assertSame($introspector, $xmlrpcClient->getIntrospector());
    }

    public function testSettingAndGettingIntrospector()
    {
        $xmlrpcClient = new Client('http://foo');
        $introspector = new Client\ServerIntrospection($xmlrpcClient);
        $this->assertNotSame($introspector, $xmlrpcClient->getIntrospector());

        $xmlrpcClient->setIntrospector($introspector);
        $this->assertSame($introspector, $xmlrpcClient->getIntrospector());
    }

    public function testGettingMethodSignature()
    {
        $method = 'foo';
        $signatures = array(array('int', 'int', 'int'));
        $this->setServerResponseTo($signatures);

        $i = $this->xmlrpcClient->getIntrospector();
        $this->assertEquals($signatures, $i->getMethodSignature($method));

        $request = $this->xmlrpcClient->getLastRequest();
        $this->assertEquals('system.methodSignature', $request->getMethod());
        $this->assertEquals(array($method), $request->getParams());
    }

    public function testListingMethods()
    {
        $methods = array('foo', 'bar', 'baz');
        $this->setServerResponseTo($methods);

        $i = $this->xmlrpcClient->getIntrospector();
        $this->assertEquals($methods, $i->listMethods());

        $request = $this->xmlrpcClient->getLastRequest();
        $this->assertEquals('system.listMethods', $request->getMethod());
        $this->assertEquals(array(), $request->getParams());
    }

    public function testGettingAllMethodSignaturesByLooping()
    {
        // system.listMethods() will return ['foo', 'bar']
        $methods = array('foo', 'bar');
        $response = $this->getServerResponseFor($methods);
        $this->httpAdapter->setResponse($response);

        // system.methodSignature('foo') will return [['int'], ['int', 'string']]
        $fooSignatures = array(array('int'), array('int', 'string'));
        $response = $this->getServerResponseFor($fooSignatures);
        $this->httpAdapter->addResponse($response);

        // system.methodSignature('bar') will return [['boolean']]
        $barSignatures = array(array('boolean'));
        $response = $this->getServerResponseFor($barSignatures);
        $this->httpAdapter->addResponse($response);

        $expected = array('foo' => $fooSignatures,
                          'bar' => $barSignatures);

        $i = $this->xmlrpcClient->getIntrospector();
        $this->assertEquals($expected, $i->getSignatureForEachMethodByLooping());

        $request = $this->xmlrpcClient->getLastRequest();
        $this->assertEquals('system.methodSignature', $request->getMethod());
        $this->assertEquals(array('bar'), $request->getParams());
    }

    public function testGettingAllMethodSignaturesByMulticall()
    {
        // system.listMethods() will return ['foo', 'bar']
        $whatListMethodsReturns = array('foo', 'bar');
        $response = $this->getServerResponseFor($whatListMethodsReturns);
        $this->httpAdapter->setResponse($response);

        // after system.listMethods(), these system.multicall() params are expected
        $multicallParams = array(array('methodName' => 'system.methodSignature',
                                       'params'     => array('foo')),
                                 array('methodName' => 'system.methodSignature',
                                       'params'     => array('bar')));

        // system.multicall() will then return [fooSignatures, barSignatures]
        $fooSignatures = array(array('int'), array('int', 'string'));
        $barSignatures = array(array('boolean'));
        $whatMulticallReturns = array($fooSignatures, $barSignatures);
        $response = $this->getServerResponseFor($whatMulticallReturns);
        $this->httpAdapter->addResponse($response);

        $i = $this->xmlrpcClient->getIntrospector();

        $expected = array('foo' => $fooSignatures,
                          'bar' => $barSignatures);
        $this->assertEquals($expected, $i->getSignatureForEachMethodByMulticall());

        $request = $this->xmlrpcClient->getLastRequest();
        $this->assertEquals('system.multicall', $request->getMethod());
        $this->assertEquals(array($multicallParams), $request->getParams());
    }

    public function testGettingAllMethodSignaturesByMulticallThrowsOnBadCount()
    {
        // system.listMethods() will return ['foo', 'bar']
        $whatListMethodsReturns = array('foo', 'bar');
        $response = $this->getServerResponseFor($whatListMethodsReturns);
        $this->httpAdapter->setResponse($response);

        // system.multicall() will then return only [fooSignatures]
        $fooSignatures = array(array('int'), array('int', 'string'));
        $whatMulticallReturns = array($fooSignatures);  // error! no bar signatures!

        $response = $this->getServerResponseFor($whatMulticallReturns);
        $this->httpAdapter->addResponse($response);

        $i = $this->xmlrpcClient->getIntrospector();

        $this->setExpectedException('Zend\XmlRpc\Client\Exception\IntrospectException', 'Bad number of signatures received from multicall');
        $i->getSignatureForEachMethodByMulticall();
    }

    public function testGettingAllMethodSignaturesByMulticallThrowsOnBadType()
    {
        // system.listMethods() will return ['foo', 'bar']
        $whatListMethodsReturns = array('foo', 'bar');
        $response = $this->getServerResponseFor($whatListMethodsReturns);
        $this->httpAdapter->setResponse($response);

        // system.multicall() will then return only an int
        $whatMulticallReturns = 1;  // error! no signatures?

        $response = $this->getServerResponseFor($whatMulticallReturns);
        $this->httpAdapter->addResponse($response);

        $i = $this->xmlrpcClient->getIntrospector();

        $this->setExpectedException('Zend\XmlRpc\Client\Exception\IntrospectException', 'Multicall return is malformed.  Expected array, got integer');
        $i->getSignatureForEachMethodByMulticall();
    }

    public function testGettingAllMethodSignaturesDefaultsToMulticall()
    {
        // system.listMethods() will return ['foo', 'bar']
        $whatListMethodsReturns = array('foo', 'bar');
        $response = $this->getServerResponseFor($whatListMethodsReturns);
        $this->httpAdapter->setResponse($response);

        // system.multicall() will then return [fooSignatures, barSignatures]
        $fooSignatures = array(array('int'), array('int', 'string'));
        $barSignatures = array(array('boolean'));
        $whatMulticallReturns = array($fooSignatures, $barSignatures);
        $response = $this->getServerResponseFor($whatMulticallReturns);
        $this->httpAdapter->addResponse($response);

        $i = $this->xmlrpcClient->getIntrospector();

        $expected = array('foo' => $fooSignatures,
                          'bar' => $barSignatures);
        $this->assertEquals($expected, $i->getSignatureForEachMethod());

        $request = $this->xmlrpcClient->getLastRequest();
        $this->assertEquals('system.multicall', $request->getMethod());
    }

    /**
     * @group ZF-4372
     */
    public function testSettingUriOnHttpClientIsNotOverwrittenByXmlRpcClient()
    {
        $changedUri = 'http://bar:80/';
        // Overwrite: http://foo:80
        $this->setServerResponseTo(array());
        $this->xmlrpcClient->getHttpClient()->setUri($changedUri);
        $this->xmlrpcClient->call('foo');
        $uri = $this->xmlrpcClient->getHttpClient()->getUri()->toString();

        $this->assertEquals($changedUri, $uri);
    }

    /**
     * @group ZF-4372
     */
    public function testSettingNoHttpClientUriForcesClientToSetUri()
    {
        $baseUri = 'http://foo:80/';
        $this->httpAdapter = new Adapter\Test();
        $this->httpClient = new Http\Client(null, array('adapter' => $this->httpAdapter));

        $this->xmlrpcClient = new Client($baseUri);
        $this->xmlrpcClient->setHttpClient($this->httpClient);

        $this->setServerResponseTo(array());
        $this->assertNull($this->xmlrpcClient->getHttpClient()->getRequest()->getUriString());
        $this->xmlrpcClient->call('foo');
        $uri = $this->xmlrpcClient->getHttpClient()->getUri();

        $this->assertEquals($baseUri, $uri->toString());
    }

    /**
     * @group ZF-3288
     */
    public function testCustomHttpClientUserAgentIsNotOverridden()
    {
        $this->assertFalse(
            $this->httpClient->getHeader('user-agent'),
            'UA is null if no request was made'
        );
        $this->setServerResponseTo(true);
        $this->assertTrue($this->xmlrpcClient->call('method'));
        $this->assertSame(
            'Zend_XmlRpc_Client',
            $this->httpClient->getHeader('user-agent'),
            'If no custom UA is set, set Zend_XmlRpc_Client'
        );

        $expectedUserAgent = 'Zend_XmlRpc_Client (custom)';
        $this->httpClient->setHeaders(array('user-agent' => $expectedUserAgent));

        $this->setServerResponseTo(true);
        $this->assertTrue($this->xmlrpcClient->call('method'));
        $this->assertSame($expectedUserAgent, $this->httpClient->getHeader('user-agent'));
    }

    /**
     * @group ZF-8478
     */
    public function testPythonSimpleXMLRPCServerWithUnsupportedMethodSignatures()
    {
        $introspector = new Client\ServerIntrospection(
            new TestClient('http://localhost/')
            );

        $this->setExpectedException('Zend\XmlRpc\Client\Exception\IntrospectException', 'Invalid signature for method "add"');
        $signature = $introspector->getMethodSignature('add');
    }


    /**
     * @group ZF-8580
     */
    public function testCallSelectsCorrectSignatureIfMoreThanOneIsAvailable()
    {
        $this->mockIntrospector();

        $this->mockedIntrospector
             ->expects($this->exactly(2))
             ->method('getMethodSignature')
             ->with('get')
             ->will($this->returnValue(array(
                 array('parameters' => array('int')),
                 array('parameters' => array('array'))
             )));

          $expectedResult = 'array';
          $this->setServerResponseTo($expectedResult);

          $this->assertSame(
              $expectedResult,
              $this->xmlrpcClient->call('get', array(array(1)))
          );

          $expectedResult = 'integer';
          $this->setServerResponseTo($expectedResult);

          $this->assertSame(
              $expectedResult,
              $this->xmlrpcClient->call('get', array(1))
          );
    }

    /**
     * @group ZF-1897
     */
    public function testHandlesLeadingOrTrailingWhitespaceInChunkedResponseProperly()
    {
        $baseUri = "http://foo:80";
        $this->httpAdapter = new Adapter\Test();
        $this->httpClient = new Http\Client(null, array('adapter' => $this->httpAdapter));

        $respBody = file_get_contents(dirname(__FILE__) . "/_files/ZF1897-response-chunked.txt");
        $this->httpAdapter->setResponse($respBody);

        $this->xmlrpcClient = new Client($baseUri);
        $this->xmlrpcClient->setHttpClient($this->httpClient);

        $this->assertEquals('FOO', $this->xmlrpcClient->call('foo'));
    }

    // Helpers
    public function setServerResponseTo($nativeVars)
    {
        $response = $this->getServerResponseFor($nativeVars);
        $this->httpAdapter->setResponse($response);
    }

    public function getServerResponseFor($nativeVars)
    {
        $response = new XmlRpc\Response();
        $response->setReturnValue($nativeVars);
        $xml = $response->saveXml();

        $response = $this->makeHttpResponseFrom($xml);
        return $response;
    }

    public function makeHttpResponseFrom($data, $status=200, $message='OK')
    {
        $headers = array("HTTP/1.1 $status $message",
                         "Status: $status",
                         'Content-Type: text/xml; charset=utf-8',
                         'Content-Length: ' . strlen($data)
                         );
        return implode("\r\n", $headers) . "\r\n\r\n$data\r\n\r\n";
    }

    public function makeHttpResponseFor($nativeVars)
    {
        $response = $this->getServerResponseFor($nativeVars);
        return HttpResponse::fromString($response);
    }

    public function mockIntrospector()
    {
        $this->mockedIntrospector = $this->getMock(
            'Zend\\XmlRpc\\Client\\ServerIntrospection',
            array(),
            array(),
            '',
            false,
            false
        );
        $this->xmlrpcClient->setIntrospector($this->mockedIntrospector);
    }

    public function mockHttpClient()
    {
        $this->mockedHttpClient = $this->getMock('Zend\\Http\\Client');
        $this->xmlrpcClient->setHttpClient($this->mockedHttpClient);
    }
}

/** related to ZF-8478 */
class PythonSimpleXMLRPCServerWithUnsupportedIntrospection extends Client\ServerProxy
{
    public function __call($method, $args)
    {
        if ($method == 'methodSignature') {
            return 'signatures not supported';
        }
        return parent::__call($method, $args);
    }
}

/** related to ZF-8478 */
class TestClient extends Client
{
    public function getProxy($namespace = '')
    {
        if (empty($this->proxyCache[$namespace])) {
            $this->proxyCache[$namespace] = new PythonSimpleXMLRPCServerWithUnsupportedIntrospection($this, $namespace);
        }
        return parent::getProxy($namespace);
    }
}
