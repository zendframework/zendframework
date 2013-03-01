<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Json
 */

namespace ZendTest\Json;

use Zend\Json\Server;
use Zend\Json;
use Zend\Json\Server\Request;
use Zend\Json\Server\Response;

/**
 * Test class for Zend_JSON_Server
 *
 * @category   Zend
 * @package    Zend_JSON_Server
 * @subpackage UnitTests
 * @group      Zend_JSON
 * @group      Zend_JSON_Server
 */
class ServerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->server = new Server\Server();
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
        try {
            $this->server->addFunction(array($this, 'setUp'));
        } catch (\Zend\Server\Reflection\Exception\RuntimeException $e) {
            $this->markTestIncomplete('PHPUnit docblocks may be incorrect');
        }
        $methods = $this->server->getFunctions();
        $this->assertTrue($methods->hasMethod('setUp'));
    }

    public function testShouldBeAbleToBindClassToServer()
    {
        $this->server->setClass('Zend\Json\Server\Server');
        $test = $this->server->getFunctions();
        $this->assertTrue(0 < count($test));
    }

    public function testBindingClassToServerShouldRegisterAllPublicMethods()
    {
        $this->server->setClass('Zend\Json\Server\Server');
        $test = $this->server->getFunctions();
        $methods = get_class_methods('Zend\Json\Server\Server');
        foreach ($methods as $method) {
            if ('_' == $method[0]) {
                continue;
            }
            $this->assertTrue($test->hasMethod($method), 'Testing for method ' . $method . ' against ' . var_export($test, 1));
        }
    }

    public function testShouldBeAbleToBindObjectToServer()
    {
        $object = new Server\Server();
        $this->server->setClass($object);
        $test = $this->server->getFunctions();
        $this->assertTrue(0 < count($test));
    }

    public function testBindingObjectToServerShouldRegisterAllPublicMethods()
    {
        $object = new Server\Server();
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
        $this->server->setClass('Zend\Json\Server\Server')
                     ->setClass(new Json\Json());
        $methods = $this->server->getFunctions();
        $zjsMethods = get_class_methods('Zend\Json\Server\Server');
        $zjMethods  = get_class_methods('Zend_JSON');
        $this->assertTrue(count($zjsMethods) < count($methods));
        $this->assertTrue(count($zjMethods) < count($methods));
    }

    public function testNamingCollisionsShouldResolveToLastRegisteredMethod()
    {
        $this->server->setClass('Zend\Json\Server\Request')
                     ->setClass('Zend\Json\Server\Response');
        $methods = $this->server->getFunctions();
        $this->assertTrue($methods->hasMethod('toJson'));
        $toJSON = $methods->getMethod('toJson');
        $this->assertEquals('Zend\Json\Server\Response', $toJSON->getCallback()->getClass());
    }

    public function testGetRequestShouldInstantiateRequestObjectByDefault()
    {
        $request = $this->server->getRequest();
        $this->assertTrue($request instanceof Request);
    }

    public function testShouldAllowSettingRequestObjectManually()
    {
        $orig = $this->server->getRequest();
        $new  = new Request();
        $this->server->setRequest($new);
        $test = $this->server->getRequest();
        $this->assertSame($new, $test);
        $this->assertNotSame($orig, $test);
    }

    public function testGetResponseShouldInstantiateResponseObjectByDefault()
    {
        $response = $this->server->getResponse();
        $this->assertTrue($response instanceof Response);
    }

    public function testShouldAllowSettingResponseObjectManually()
    {
        $orig = $this->server->getResponse();
        $new  = new Response();
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
        $this->assertFalse($this->server->getReturnResponse());
    }

    public function testShouldBeAbleToDisableAutomaticResponseEmission()
    {
        $this->testResponseShouldBeEmittedAutomaticallyByDefault();
        $this->server->setReturnResponse(true);
        $this->assertTrue($this->server->getReturnResponse());
    }

    public function testShouldBeAbleToRetrieveSmdObject()
    {
        $smd = $this->server->getServiceMap();
        $this->assertTrue($smd instanceof \Zend\Json\Server\Smd);
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
                     ->setClass('Zend\Json\Server\Server')
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
        $methods = get_class_methods('Zend\Json\Server\Server');
        foreach ($methods as $method) {
            if ('_' == $method[0]) {
                continue;
            }
            $this->assertTrue(array_key_exists($method, $services));
        }
    }

    public function testHandleValidMethodShouldWork()
    {
        $this->server->setClass('ZendTest\\Json\\Foo')
                     ->addFunction('ZendTest\\Json\\FooFunc')
                     ->setReturnResponse(true);
        $request = $this->server->getRequest();
        $request->setMethod('bar')
                ->setParams(array(true, 'foo', 'bar'))
                ->setId('foo');
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Response);
        $this->assertFalse($response->isError());


        $request->setMethod('ZendTest\\Json\\FooFunc')
                ->setId('foo');
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Response);
        $this->assertFalse($response->isError());
    }

    public function testHandleValidMethodWithNULLParamValueShouldWork()
    {
        $this->server->setClass('ZendTest\\Json\\Foo')
                     ->addFunction('ZendTest\\Json\\FooFunc')
                     ->setReturnResponse(true);
        $request = $this->server->getRequest();
        $request->setMethod('bar')
                ->setParams(array(true, NULL, 'bar'))
                ->setId('foo');
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Response);
        $this->assertFalse($response->isError());
    }

    public function testHandleValidMethodWithTooFewParamsShouldPassDefaultsOrNullsForMissingParams()
    {
        $this->server->setClass('ZendTest\Json\Foo')
                     ->setReturnResponse(true);
        $request = $this->server->getRequest();
        $request->setMethod('bar')
                ->setParams(array(true))
                ->setId('foo');
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Response);
        $this->assertFalse($response->isError());
        $result = $response->getResult();
        $this->assertTrue(is_array($result));
        $this->assertTrue(3 == count($result));
        $this->assertEquals('two', $result[1], var_export($result, 1));
        $this->assertNull($result[2]);
    }

    public function testHandleValidMethodWithTooFewAssociativeParamsShouldPassDefaultsOrNullsForMissingParams()
    {
        $this->server->setClass('ZendTest\Json\Foo')
                     ->setReturnResponse(true);
        $request = $this->server->getRequest();
        $request->setMethod('bar')
                ->setParams(array('one' => true))
                ->setId('foo');
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Response);
        $this->assertFalse($response->isError());
        $result = $response->getResult();
        $this->assertTrue(is_array($result));
        $this->assertTrue(3 == count($result));
        $this->assertEquals('two', $result[1], var_export($result, 1));
        $this->assertNull($result[2]);
    }

    public function testHandleValidMethodWithTooManyParamsShouldWork()
    {
        $this->server->setClass('ZendTest\Json\Foo')
                     ->setReturnResponse(true);
        $request = $this->server->getRequest();
        $request->setMethod('bar')
                ->setParams(array(true, 'foo', 'bar', 'baz'))
                ->setId('foo');
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Response);
        $this->assertFalse($response->isError());
        $result = $response->getResult();
        $this->assertTrue(is_array($result));
        $this->assertTrue(3 == count($result));
        $this->assertEquals('foo', $result[1]);
        $this->assertEquals('bar', $result[2]);
    }

    public function testHandleShouldAllowNamedParamsInAnyOrder1()
    {
        $this->server->setClass('ZendTest\Json\Foo')
                     ->setReturnResponse(true);
        $request = $this->server->getRequest();
        $request->setMethod('bar')
                ->setParams( array(
                    'three' => 3,
                    'two'   => 2,
                    'one'   => 1
                ))
                ->setId( 'foo' );
        $response = $this->server->handle();
        $result = $response->getResult();

        $this->assertTrue( is_array( $result ) );
        $this->assertEquals( 1, $result[0] );
        $this->assertEquals( 2, $result[1] );
        $this->assertEquals( 3, $result[2] );
    }

    public function testHandleShouldAllowNamedParamsInAnyOrder2()
    {
        $this->server->setClass('ZendTest\Json\Foo')
                     ->setReturnResponse(true);
        $request = $this->server->getRequest();
        $request->setMethod('bar')
                ->setParams( array(
                    'three' => 3,
                    'one'   => 1,
                    'two'   => 2,
                ) )
                ->setId( 'foo' );
        $response = $this->server->handle();
        $result = $response->getResult();

        $this->assertTrue( is_array( $result ) );
        $this->assertEquals( 1, $result[0] );
        $this->assertEquals( 2, $result[1] );
        $this->assertEquals( 3, $result[2] );
    }

    public function testHandleValidWithoutRequiredParamShouldReturnError()
    {
        $this->server->setClass('ZendTest\Json\Foo')
                     ->setReturnResponse(true);
        $request = $this->server->getRequest();
        $request->setMethod('bar')
                ->setParams( array(
                    'three' => 3,
                    'two'   => 2,
                 ) )
                ->setId( 'foo' );
        $response = $this->server->handle();

        $this->assertTrue($response instanceof Response);
        $this->assertTrue($response->isError());
        $this->assertEquals(Server\Error::ERROR_INVALID_PARAMS, $response->getError()->getCode());
    }

    public function testHandleRequestWithErrorsShouldReturnErrorResponse()
    {
        $this->server->setClass('ZendTest\Json\Foo')
                     ->setReturnResponse(true);
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Response);
        $this->assertTrue($response->isError());
        $this->assertEquals(Server\Error::ERROR_INVALID_REQUEST, $response->getError()->getCode());
    }

    public function testHandleRequestWithInvalidMethodShouldReturnErrorResponse()
    {
        $this->server->setClass('ZendTest\Json\Foo')
                     ->setReturnResponse(true);
        $request = $this->server->getRequest();
        $request->setMethod('bogus')
                ->setId('foo');
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Response);
        $this->assertTrue($response->isError());
        $this->assertEquals(Server\Error::ERROR_INVALID_METHOD, $response->getError()->getCode());
    }

    public function testHandleRequestWithExceptionShouldReturnErrorResponse()
    {
        $this->server->setClass('ZendTest\Json\Foo')
                     ->setReturnResponse(true);
        $request = $this->server->getRequest();
        $request->setMethod('baz')
                ->setId('foo');
        $response = $this->server->handle();
        $this->assertTrue($response instanceof Response);
        $this->assertTrue($response->isError());
        $this->assertEquals(Server\Error::ERROR_OTHER, $response->getError()->getCode());
        $this->assertEquals('application error', $response->getError()->getMessage());
    }

    public function testHandleShouldEmitResponseByDefault()
    {
        $this->server->setClass('ZendTest\Json\Foo');
        $request = $this->server->getRequest();
        $request->setMethod('bar')
                ->setParams(array(true, 'foo', 'bar'))
                ->setId('foo');
        ob_start();
        $this->server->handle();
        $buffer = ob_get_clean();

        $decoded = Json\Json::decode($buffer, Json\Json::TYPE_ARRAY);
        $this->assertTrue(is_array($decoded));
        $this->assertTrue(array_key_exists('result', $decoded));
        $this->assertTrue(array_key_exists('id', $decoded));

        $response = $this->server->getResponse();
        $this->assertEquals($response->getResult(), $decoded['result']);
        $this->assertEquals($response->getId(), $decoded['id']);
    }

    public function testResponseShouldBeEmptyWhenRequestHasNoId()
    {
        $this->server->setClass('ZendTest\Json\Foo');
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
        $this->server->setClass('ZendTest\Json\Foo');
        $functions = $this->server->getFunctions();
        $server = new Server\Server();
        $server->loadFunctions($functions);
        $this->assertEquals($functions->toArray(), $server->getFunctions()->toArray());
    }
}

/**
 * Class for testing JSON-RPC server
 */
class Foo
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
        throw new \Exception('application error');
    }
}

/**
 * Test function for JSON-RPC server
 *
 * @return bool
 */
function FooFunc()
{
    return true;
}
