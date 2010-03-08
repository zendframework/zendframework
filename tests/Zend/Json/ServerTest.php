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
 * @package    Zend_Json_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Json_ServerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Json_ServerTest::main");
}



/**
 * Test class for Zend_Json_Server
 *
 * @category   Zend
 * @package    Zend_Json_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Json
 * @group      Zend_Json_Server
 */
class Zend_Json_ServerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Json_ServerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->server = new Zend_Json_Server();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testShouldBeAbleToBindFunctionToServer()
    {
        $this->server->addFunction('strtolower');
        $methods = $this->server->getFunctions();
        $this->assertTrue($methods->hasMethod('strtolower'));
    }

    public function testShouldBeAbleToBindCallbackToServer()
    {
        $this->server->addFunction(array($this, 'setUp'));
        $methods = $this->server->getFunctions();
        $this->assertTrue($methods->hasMethod('setUp'));
    }

    public function testShouldBeAbleToBindClassToServer()
    {
        $this->server->setClass('Zend_Json_Server');
        $test = $this->server->getFunctions();
        $this->assertTrue(0 < count($test));
    }

    public function testBindingClassToServerShouldRegisterAllPublicMethods()
    {
        $this->server->setClass('Zend_Json_Server');
        $test = $this->server->getFunctions();
        $methods = get_class_methods('Zend_Json_Server');
        foreach ($methods as $method) {
            if ('_' == $method[0]) {
                continue;
            }
            $this->assertTrue($test->hasMethod($method), 'Testing for method ' . $method . ' against ' . var_export($test, 1));
        }
    }

    public function testShouldBeAbleToBindObjectToServer()
    {
        $object = new Zend_Json_Server();
        $this->server->setClass($object);
        $test = $this->server->getFunctions();
        $this->assertTrue(0 < count($test));
    }

    public function testBindingObjectToServerShouldRegisterAllPublicMethods()
    {
        $object = new Zend_Json_Server();
        $this->server->setClass($object);
        $test = $this->server->getFunctions();
        $methods = get_class_methods($object);
        foreach ($methods as $method) {
            if ('_' == $method[0]) {
                continue;
            }
            $this->assertTrue($test->hasMethod($method), 'Testing for method ' . $method . ' against ' . var_export($test, 1));
        }
    }

    public function testShouldBeAbleToBindMultipleClassesAndObjectsToServer()
    {
        $this->server->setClass('Zend_Json_Server')
                     ->setClass(new Zend_Json());
        $methods = $this->server->getFunctions();
        $zjsMethods = get_class_methods('Zend_Json_Server');
        $zjMethods  = get_class_methods('Zend_Json');
        $this->assertTrue(count($zjsMethods) < count($methods));
        $this->assertTrue(count($zjMethods) < count($methods));
    }

    public function testNamingCollisionsShouldResolveToLastRegisteredMethod()
    {
        $this->server->setClass('Zend_Json_Server_Request')
                     ->setClass('Zend_Json_Server_Response');
        $methods = $this->server->getFunctions();
        $this->assertTrue($methods->hasMethod('toJson'));
        $toJson = $methods->getMethod('toJson');
        $this->assertEquals('Zend_Json_Server_Response', $toJson->getCallback()->getClass());
    }

    public function testGetRequestShouldInstantiateRequestObjectByDefault()
    {
        $request = $this->server->getRequest();
        $this->assertTrue($request instanceof Zend_Json_Server_Request);
    }

    public function testShouldAllowSettingRequestObjectManually()
    {
        $orig = $this->server->getRequest();
        $new  = new Zend_Json_Server_Request();
        $this->server->setRequest($new);
        $test = $this->server->getRequest();
        $this->assertSame($new, $test);
        $this->assertNotSame($orig, $test);
    }

    public function testGetResponseShouldInstantiateResponseObjectByDefault()
    {
        $response = $this->server->getResponse();
        $this->assertTrue($response instanceof Zend_Json_Server_Response);
    }

    public function testShouldAllowSettingResponseObjectManually()
    {
        $orig = $this->server->getResponse();
        $new  = new Zend_Json_Server_Response();
        $this->server->setResponse($new);
        $test = $this->server->getResponse();
        $this->assertSame($new, $test);
        $this->assertNotSame($orig, $test);
    }

    public function testFaultShouldCreateErrorResponse()
    {
        $response = $this->server->getResponse();
        $this->assertFalse($response->isError());
        $this->server->fault('error condition', -32000);
        $this->assertTrue($response->isError());
        $error = $response->getError();
        $this->assertEquals(-32000, $error->getCode());
        $this->assertEquals('error condition', $error->getMessage());
    }

    public function testResponseShouldBeEmittedAutomaticallyByDefault()
    {
        $this->assertTrue($this->server->autoEmitResponse());
    }

    public function testShouldBeAbleToDisableAutomaticResponseEmission()
    {
        $this->testResponseShouldBeEmittedAutomaticallyByDefault();
        $this->server->setAutoEmitResponse(false);
        $this->assertFalse($this->server->autoEmitResponse());
    }

    public function testShouldBeAbleToRetrieveSmdObject()
    {
        $smd = $this->server->getServiceMap();
        $this->assertTrue($smd instanceof Zend_Json_Server_Smd);
    }

    public function testShouldBeAbleToSetArbitrarySmdMetadata()
    {
        $this->server->setTransport('POST')
                     ->setEnvelope('JSON-RPC-1.0')
                     ->setContentType('application/x-json')
                     ->setTarget('/foo/bar')
                     ->setId('foobar')
                     ->setDescription('This is a test service');

        $this->assertEquals('POST', $this->server->getTransport());
        $this->assertEquals('JSON-RPC-1.0', $this->server->getEnvelope());
        $this->assertEquals('application/x-json', $this->server->getContentType());
        $this->assertEquals('/foo/bar', $this->server->getTarget());
        $this->assertEquals('foobar', $this->server->getId());
        $this->assertEquals('This is a test service', $this->server->getDescription());
    }

    public function testSmdObjectRetrievedFromServerShouldReflectServerState()
    {
        $this->server->addFunction('strtolower')
                     ->setClass('Zend_Json_Server')
                     ->setTransport('POST')
                     ->setEnvelope('JSON-RPC-1.0')
                     ->setContentType('application/x-json')
                     ->setTarget('/foo/bar')
                     ->setId('foobar')
                     ->setDescription('This is a test service');
        $smd = $this->server->getServiceMap();
        $this->assertEquals('POST', $this->server->getTransport());
        $this->assertEquals('JSON-RPC-1.0', $this->server->getEnvelope());
        $this->assertEquals('application/x-json', $this->server->getContentType());
        $this->assertEquals('/foo/bar', $this->server->getTarget());
        $this->assertEquals('foobar', $this->server->getId());
        $this->assertEquals('This is a test service', $this->server->getDescription());

        $services = $smd->getServices();
        $this->assertTrue(is_array($services));
        $this->assertTrue(0 < count($services));
        $this->assertTrue(array_key_exists('strtolower', $services));
        $methods = get_class_methods('Zend_Json_Server');
        foreach ($methods as $method) {
            if ('_' == $method[0]) {
                continue;
            }
            $this->assertTrue(array_key_exists($method, $services));
        }
    }

    public function testHandleValidMethodShouldWork()
    {
        $this->server->setClass('Zend_Json_ServerTest_Foo')
                     ->addFunction('Zend_Json_ServerTest_FooFunc')
                     ->setAutoEmitResponse(false);
        $request = $this->server->getRequest();
        $request->setMethod('bar')
                ->setParams(array(true, 'foo', 'bar'))
                ->setId('foo');
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Zend_Json_Server_Response);
        $this->assertFalse($response->isError());


        $request->setMethod('Zend_Json_ServerTest_FooFunc')
                ->setId('foo');
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Zend_Json_Server_Response);
        $this->assertFalse($response->isError());
    }

    public function testHandleValidMethodWithTooFewParamsShouldPassDefaultsOrNullsForMissingParams()
    {
        $this->server->setClass('Zend_Json_ServerTest_Foo')
                     ->setAutoEmitResponse(false);
        $request = $this->server->getRequest();
        $request->setMethod('bar')
                ->setParams(array(true))
                ->setId('foo');
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Zend_Json_Server_Response);
        $this->assertFalse($response->isError());
        $result = $response->getResult();
        $this->assertTrue(is_array($result));
        $this->assertTrue(3 == count($result));
        $this->assertEquals('two', $result[1], var_export($result, 1));
        $this->assertNull($result[2]);
    }

    public function testHandleValidMethodWithTooManyParamsShouldWork()
    {
        $this->server->setClass('Zend_Json_ServerTest_Foo')
                     ->setAutoEmitResponse(false);
        $request = $this->server->getRequest();
        $request->setMethod('bar')
                ->setParams(array(true, 'foo', 'bar', 'baz'))
                ->setId('foo');
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Zend_Json_Server_Response);
        $this->assertFalse($response->isError());
        $result = $response->getResult();
        $this->assertTrue(is_array($result));
        $this->assertTrue(3 == count($result));
        $this->assertEquals('foo', $result[1]);
        $this->assertEquals('bar', $result[2]);
    }

    public function testHandleRequestWithErrorsShouldReturnErrorResponse()
    {
        $this->server->setClass('Zend_Json_ServerTest_Foo')
                     ->setAutoEmitResponse(false);
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Zend_Json_Server_Response);
        $this->assertTrue($response->isError());
        $this->assertEquals(Zend_Json_Server_Error::ERROR_INVALID_REQUEST, $response->getError()->getCode());
    }

    public function testHandleRequestWithInvalidMethodShouldReturnErrorResponse()
    {
        $this->server->setClass('Zend_Json_ServerTest_Foo')
                     ->setAutoEmitResponse(false);
        $request = $this->server->getRequest();
        $request->setMethod('bogus')
                ->setId('foo');
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Zend_Json_Server_Response);
        $this->assertTrue($response->isError());
        $this->assertEquals(Zend_Json_Server_Error::ERROR_INVALID_METHOD, $response->getError()->getCode());
    }

    public function testHandleRequestWithExceptionShouldReturnErrorResponse()
    {
        $this->server->setClass('Zend_Json_ServerTest_Foo')
                     ->setAutoEmitResponse(false);
        $request = $this->server->getRequest();
        $request->setMethod('baz')
                ->setId('foo');
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Zend_Json_Server_Response);
        $this->assertTrue($response->isError());
        $this->assertEquals(Zend_Json_Server_Error::ERROR_OTHER, $response->getError()->getCode());
        $this->assertEquals('application error', $response->getError()->getMessage());
    }

    public function testHandleShouldEmitResponseByDefault()
    {
        $this->server->setClass('Zend_Json_ServerTest_Foo');
        $request = $this->server->getRequest();
        $request->setMethod('bar')
                ->setParams(array(true, 'foo', 'bar'))
                ->setId('foo');
        ob_start();
        $this->server->handle();
        $buffer = ob_get_clean();

        $decoded = Zend_Json::decode($buffer);
        $this->assertTrue(is_array($decoded));
        $this->assertTrue(array_key_exists('result', $decoded));
        $this->assertTrue(array_key_exists('id', $decoded));

        $response = $this->server->getResponse();
        $this->assertEquals($response->getResult(), $decoded['result']);
        $this->assertEquals($response->getId(), $decoded['id']);
    }

    public function testResponseShouldBeEmptyWhenRequestHasNoId()
    {
        $this->server->setClass('Zend_Json_ServerTest_Foo');
        $request = $this->server->getRequest();
        $request->setMethod('bar')
                ->setParams(array(true, 'foo', 'bar'));
        ob_start();
        $this->server->handle();
        $buffer = ob_get_clean();

        $this->assertTrue(empty($buffer));
    }

    public function testLoadFunctionsShouldLoadResultOfGetFunctions()
    {
        $this->server->setClass('Zend_Json_ServerTest_Foo');
        $functions = $this->server->getFunctions();
        $server = new Zend_Json_Server();
        $server->loadFunctions($functions);
        $this->assertEquals($functions->toArray(), $server->getFunctions()->toArray());
    }
}

/**
 * Class for testing JSON-RPC server
 */
class Zend_Json_ServerTest_Foo
{
    /**
     * Bar
     *
     * @param  bool $one
     * @param  string $two
     * @param  mixed $three
     * @return array
     */
    public function bar($one, $two = 'two', $three = null)
    {
        return array($one, $two, $three);
    }

    /**
     * Baz
     *
     * @return void
     */
    public function baz()
    {
        throw new Exception('application error');
    }
}

/**
 * Test function for JSON-RPC server
 *
 * @return bool
 */
function Zend_Json_ServerTest_FooFunc()
{
    return true;
}

// Call Zend_Json_ServerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Json_ServerTest::main") {
    Zend_Json_ServerTest::main();
}
