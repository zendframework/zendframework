<?php
// Call Zend_XmlRpc_ClientTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_XmlRpc_ClientTest::main");
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Zend/XmlRpc/Client.php';

require_once 'Zend/XmlRpc/Response.php';

require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * Test case for Zend_XmlRpc_Value
 *
 * @package Zend_XmlRpc
 * @subpackage UnitTests
 * @version $Id$
 */
class Zend_XmlRpc_ClientTest extends PHPUnit_Framework_TestCase 
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

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_XmlRpc_ClientTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->httpAdapter = new Zend_Http_Client_Adapter_Test();
        $this->httpClient = new Zend_Http_Client('http://foo', 
                                    array('adapter' => $this->httpAdapter));

        $this->xmlrpcClient = new Zend_XmlRpc_Client('http://foo');
        $this->xmlrpcClient->setHttpClient($this->httpClient);
    }

    // HTTP Client

    public function testGettingDefaultHttpClient()
    {
        $xmlrpcClient = new Zend_XmlRpc_Client('http://foo');
        $httpClient = $xmlrpcClient->getHttpClient();
        $this->assertType('Zend_Http_Client', $httpClient);
        $this->assertSame($httpClient, $xmlrpcClient->getHttpClient());
    }

    public function testSettingAndGettingHttpClient()
    {
        $xmlrpcClient = new Zend_XmlRpc_Client('http://foo');
        $httpClient = new Zend_Http_Client('http://foo');
        $this->assertNotSame($httpClient, $xmlrpcClient->getHttpClient());

        $xmlrpcClient->setHttpClient($httpClient);
        $this->assertSame($httpClient, $xmlrpcClient->getHttpClient());
    }

    public function testSettingHttpClientViaContructor()
    {
        $xmlrpcClient = new Zend_XmlRpc_Client('http://foo', $this->httpClient);
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
        
        $this->assertType('Zend_XmlRpc_Request', $this->xmlrpcClient->getLastRequest());
        $this->assertType('Zend_XmlRpc_Response', $this->xmlrpcClient->getLastResponse());
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
        $this->assertSame($expectedParams, $request->getParams());
        $this->assertSame($expectedReturn, $response->getReturnValue());
        $this->assertFalse($response->isFault());
    }

    /**
     * @see ZF-2090
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

        $this->assertSame($expectedParams, $request->getParams());
    }
    
    /**
     * Test for ZF-1412
     * 
     * @return void
     */
    public function testSuccessfulRpcMethodCallWithMixedDateParameters()
    {
        $time = time();
        $expectedMethod = 'foo.bar';
        $expectedParams = array(
            'username', 
            new Zend_XmlRpc_Value_DateTime($time)
        );
        $expectedReturn = array('username', $time);

        $this->setServerResponseTo($expectedReturn);
        
        $actualReturn = $this->xmlrpcClient->call($expectedMethod, $expectedParams);
        $this->assertSame($expectedReturn, $actualReturn);

        $request  = $this->xmlrpcClient->getLastRequest();
        $response = $this->xmlrpcClient->getLastResponse();

        $this->assertSame($expectedMethod, $request->getMethod());
        $this->assertSame($expectedParams, $request->getParams());
        $this->assertSame($expectedReturn, $response->getReturnValue());
        $this->assertFalse($response->isFault());
    }

    /**
     * @see ZF-1797
     */
    public function testSuccesfulRpcMethodCallWithXmlRpcValueParameters()
    {
        $time   = time();
        $params = array(
            new Zend_XmlRpc_Value_Boolean(true),
            new Zend_XmlRpc_Value_Integer(4),
            new Zend_XmlRpc_Value_String('foo')
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

    /**#@+
     * @see ZF-2978
     */
    public function testSkippingSystemCallDisabledByDefault()
    {
        $this->assertFalse($this->xmlrpcClient->skipSystemLookup());
    }

    public function testAllowsSkippingSystemCallForArrayStructLookup()
    {
        $this->xmlrpcClient->setSkipSystemLookup(true);
        $this->assertTrue($this->xmlrpcClient->skipSystemLookup());
    }

    public function testSkipsSystemCallWhenDirected()
    {
        $this->markTestIncomplete('Cannot complete this test until we add logging of requests sent to HTTP test client');
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

        try {
            $this->xmlrpcClient->call('foo');
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_XmlRpc_Client_HttpException', $e);
            $this->assertEquals($message, $e->getMessage());
            $this->assertEquals($status, $e->getCode());
        }
    }

    public function testRpcMethodCallThrowsOnXmlRpcFault()
    {
        $code = 9;
        $message = 'foo';
        
        $fault = new Zend_XmlRpc_Fault($code, $message);
        $xml = $fault->saveXML();

        $response = $this->makeHttpResponseFrom($xml);
        $this->httpAdapter->setResponse($response);        

        try {
            $this->xmlrpcClient->call('foo');
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_XmlRpc_Client_FaultException', $e);
            $this->assertEquals($message, $e->getMessage());
            $this->assertEquals($code, $e->getCode());
        }
    }
    
    // Server Proxy
    
    public function testGetProxyReturnsServerProxy()
    {
        $class = 'Zend_XmlRpc_Client_ServerProxy';
        $this->assertType($class, $this->xmlrpcClient->getProxy());
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
        $xmlrpcClient = new Zend_XmlRpc_Client('http://foo');
        $introspector = $xmlrpcClient->getIntrospector();
        $this->assertType('Zend_XmlRpc_Client_ServerIntrospection', $introspector);
        $this->assertSame($introspector, $xmlrpcClient->getIntrospector());
    }

    public function testSettingAndGettingIntrospector()
    {
        $xmlrpcClient = new Zend_XmlRpc_Client('http://foo');
        $introspector = new Zend_XmlRpc_Client_ServerIntrospection($xmlrpcClient);
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

        try {
            $i->getSignatureForEachMethodByMulticall();
        } catch (Zend_XmlRpc_Client_IntrospectException $e) {
            $this->assertRegexp('/bad number/i', $e->getMessage());
        }
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

        try {
            $i->getSignatureForEachMethodByMulticall();
        } catch (Zend_XmlRpc_Client_IntrospectException $e) {
            $this->assertRegexp('/got integer/i', $e->getMessage());
        }
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
    
    public function testGettingAllMethodSignaturesDegradesToLooping()
    {
        // system.listMethods() will return ['foo', 'bar']
        $whatListMethodsReturns = array('foo', 'bar');
        $response = $this->getServerResponseFor($whatListMethodsReturns);
        $this->httpAdapter->setResponse($response);

        // system.multicall() will return a fault
        $fault = new Zend_XmlRpc_Fault(7, 'bad method');
        $xml = $fault->saveXML();
        $response = $this->makeHttpResponseFrom($xml);
        $this->httpAdapter->addResponse($response);  

        // system.methodSignature('foo') will return [['int'], ['int', 'string']]
        $fooSignatures = array(array('int'), array('int', 'string'));
        $response = $this->getServerResponseFor($fooSignatures);
        $this->httpAdapter->addResponse($response);

        // system.methodSignature('bar') will return [['boolean']]
        $barSignatures = array(array('boolean'));
        $response = $this->getServerResponseFor($barSignatures);
        $this->httpAdapter->addResponse($response);
        
        $i = $this->xmlrpcClient->getIntrospector();
        
        $expected = array('foo' => $fooSignatures,
                          'bar' => $barSignatures);
        $this->assertEquals($expected, $i->getSignatureForEachMethod());
        
        $request = $this->xmlrpcClient->getLastRequest();
        $this->assertEquals('system.methodSignature', $request->getMethod());
    }

    /**
     * @group ZF-4372
     */
    public function testSettingUriOnHttpClientIsNotOverwrittenByXmlRpcClient()
    {
        $changedUri = "http://bar:80";
        // Overwrite: http://foo:80
        $this->setServerResponseTo(array());
        $this->xmlrpcClient->getHttpClient()->setUri($changedUri);
        $this->xmlrpcClient->call("foo");
        $uri = $this->xmlrpcClient->getHttpClient()->getUri(true);

        $this->assertEquals($changedUri, $uri);
    }

    /**
     * @group ZF-4372
     */
    public function testSettingNoHttpClientUriForcesClientToSetUri()
    {
        $baseUri = "http://foo:80";
        $this->httpAdapter = new Zend_Http_Client_Adapter_Test();
        $this->httpClient = new Zend_Http_Client(null, array('adapter' => $this->httpAdapter));

        $this->xmlrpcClient = new Zend_XmlRpc_Client($baseUri);
        $this->xmlrpcClient->setHttpClient($this->httpClient);

        $this->setServerResponseTo(array());
        $this->assertNull($this->xmlrpcClient->getHttpClient()->getUri());
        $this->xmlrpcClient->call("foo");
        $uri = $this->xmlrpcClient->getHttpClient()->getUri(true);

        $this->assertEquals($baseUri, $uri);
    }
    
    
    // Helpers
    
    public function setServerResponseTo($nativeVars)
    {
        $response = $this->getServerResponseFor($nativeVars);
        $this->httpAdapter->setResponse($response);
    }

    public function getServerResponseFor($nativeVars)
    {
        $response = new Zend_XmlRpc_Response();
        $response->setReturnValue($nativeVars);
        $xml = $response->saveXML();

        $response = $this->makeHttpResponseFrom($xml);
        return $response;
    }

    public function makeHttpResponseFrom($data, $status=200, $message='OK') 
    {
        $headers = array("HTTP/1.1 $status $message",
                         "Status: $status",
                         'Content_Type: text/xml; charset=utf-8',
                         'Content-Length: ' . strlen($data)
                         );
        return implode("\r\n", $headers) . "\r\n\r\n$data\r\n\r\n";
    }
}

// Call Zend_XmlRpc_ClientTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_XmlRpc_ClientTest::main") {
    Zend_XmlRpc_ClientTest::main();
}
