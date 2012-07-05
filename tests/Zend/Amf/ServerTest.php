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
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Amf;
use Zend\Amf,
    Zend\Amf\Parser,
    Zend\Amf\Value,
    Zend\Amf\Request,
    Zend\Amf\Value\Messaging,
    Zend\Amf\Response,
    Zend\Session;

require_once __DIR__ . '/TestAsset/Server/serverFunctions.php';
require_once __DIR__ . '/TestAsset/Server/ServiceA.php';
require_once __DIR__ . '/TestAsset/Server/ServiceB.php';

/**
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Amf
 */
class ServerTest extends \PHPUnit_Framework_TestCase
{
    protected $_server;

    public function setUp()
    {
        $this->_server = new Amf\Server();
        $this->_server->setProduction(false);
        Parser\TypeLoader::resetMap();
        Session\Container::setDefaultManager(null);
        $config = new Session\Configuration\StandardConfiguration(array(
            'class'   => 'Zend\\Session\\Configuration\\StandardConfiguration',
            'storage' => 'Zend\\Session\\Storage\\ArrayStorage',
        ));
        $this->session = new \ZendTest\Session\TestAsset\TestManager($config);
        Session\Container::setDefaultManager($this->session);
    }

    public function tearDown()
    {
        unset($this->_server);
        //Zend_Amf_Parse_TypeLoader::resetMap();
    }

    /**
     * Call as method call
     *
     * Returns: void
     */
    public function test__construct()
    {
        $this->assertTrue($this->_server instanceof Amf\Server);
    }

    public function testIsProductionByDefault()
    {
        $this->_server = new Amf\Server;
        $this->assertTrue($this->_server->isProduction());
    }

    public function testProductionFlagShouldBeMutable()
    {
        $this->testIsProductionByDefault();
        $this->_server->setProduction(false);
        $this->assertFalse($this->_server->isProduction());
        $this->_server->setProduction(true);
        $this->assertTrue($this->_server->isProduction());
    }

    public function testSetClass()
    {
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass', 'test');
        $methods = $this->_server->listMethods();
        $this->assertTrue(in_array('test.test1', $methods));
        $this->assertTrue(in_array('test.test2', $methods));
        $this->assertFalse(in_array('test._test3', $methods));
        $this->assertFalse(in_array('test.__construct', $methods));
    }

    /**
     * @expectedException Zend\Amf\Exception\ExceptionInterface
     */
    public function testSetClassShouldRaiseExceptionOnInvalidClassname()
    {
        $this->_server->setClass('foobar');
    }

    /**
     * @expectedException Zend\Amf\Exception\ExceptionInterface
     */
    public function testSetClassShouldRaiseExceptionOnInvalidClasstype()
    {
        $this->_server->setClass(array('foobar'));
    }

    /**
     * @expectedException Zend\Amf\Exception\ExceptionInterface
     */
    public function testSetClassShouldRaiseExceptionOnDuplicateMethodName()
    {
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass', 'tc');
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclassPrivate', 'tc');
    }

    /**
     * ZF-5393
     */
    public function testSetClassUsingObject()
    {
        $testClass = new TestAsset\Server\testclass();
        $this->_server->setClass($testClass);
        $this->assertEquals(8, count($this->_server->getFunctions()));
    }

    /**
     * addFunction() test
     *
     * Call as method call
     *
     * Expects:
     * - function:
     * - namespace: Optional; has default;
     *
     * Returns: void
     */
    public function testAddFunction()
    {

        $this->_server->addFunction('ZendTest\\Amf\\TestAsset\\Server\\testFunction', 'test');

        $methods = $this->_server->listMethods();
        $this->assertTrue(in_array('test.ZendTest\\Amf\\TestAsset\\Server\\testFunction', $methods), var_export($methods, 1));

        try {
            $this->_server->addFunction('nosuchfunction');
            $this->fail('nosuchfunction() should not exist and should throw an exception');
        } catch (\Exception $e) {
        }

        $server = new Amf\Server();

        $server->addFunction(
            array(
                'ZendTest\\Amf\\TestAsset\\Server\\testFunction',
                'ZendTest\\Amf\\TestAsset\\Server\\testFunction2',
            ),
            'zsr'
        );

        $methods = $server->listMethods();
        $this->assertTrue(in_array('zsr.ZendTest\\Amf\\TestAsset\\Server\\testFunction', $methods));
        $this->assertTrue(in_array('zsr.ZendTest\\Amf\\TestAsset\\Server\\testFunction2', $methods));
    }

    /**
     * @expectedException Zend\Amf\Exception\ExceptionInterface
     */
    public function testAddFunctionShouldRaiseExceptionForInvalidFunctionName()
    {
        $this->_server->addFunction(true);
    }

    /**
     * @expectedException Zend\Amf\Exception\ExceptionInterface
     */
    public function testAddFunctionShouldRaiseExceptionOnDuplicateMethodName()
    {
        $this->_server->addFunction('ZendTest\\Amf\\TestAsset\\Server\\testFunction', 'tc');
        $this->_server->addFunction('ZendTest\\Amf\\TestAsset\\Server\\testFunction', 'tc');
    }

    /**
     * Test sending data to the remote class and make sure we
     * recieve the proper response.
     *
     */
    public function testHandleLoadedClassAmf0()
    {
        // serialize the data to an AMF output stream
        $data[] = "12345";
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass');
        $newBody = new Value\MessageBody("ZendTest\\Amf\\TestAsset\\Server\\testclass.test1","/1",$data);
        $request = new Request\StreamRequest();
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x00);
        $result = $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        // Now check if the return data was properly set.
        $this->assertTrue(0 < count($responseBody), var_export($responseBody, 1));
        $this->assertTrue(array_key_exists(0, $responseBody), var_export($responseBody, 1));
        $this->assertEquals("String: 12345", $responseBody[0]->getData(), var_export($responseBody, 1));
    }

    public function testShouldAllowHandlingFunctionCallsViaAmf0()
    {
        // serialize the data to an AMF output stream
        $data = array('foo', 'bar');
        $this->_server->addFunction('ZendTest\\Amf\\TestAsset\\Server\\testFunction');
        $newBody = new Value\MessageBody("ZendTest\\Amf\\TestAsset\\Server\\testFunction","/1",$data);
        $request = new Request\StreamRequest();
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x00);
        $result = $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        // Now check if the return data was properly set.
        $this->assertTrue(0 < count($responseBody), var_export($responseBody, 1));
        $this->assertTrue(array_key_exists(0, $responseBody), var_export($responseBody, 1));
        $this->assertEquals("bar: foo", $responseBody[0]->getData(), var_export($responseBody, 1));
    }

    /**
     * Test to make sure that AMF3 basic requests are handled for loading
     * a class.
     * This type of call is sent from NetConnection rather than RemoteObject
     *
     * @group ZF-4680
     */
    public function testHandleLoadedClassAmf3NetConnection()
    {
        // serialize the data to an AMF output stream
        $data[] = "12345";
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass');
        $newBody = new Value\MessageBody("ZendTest\\Amf\\TestAsset\\Server\\testclass.test1","/1",$data);
        $request = new Request\StreamRequest();
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);
        $result = $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        // Now check if the return data was properly set.
        $this->assertTrue(0 < count($responseBody), var_export($responseBody, 1));
        $this->assertTrue(array_key_exists(0, $responseBody), var_export($responseBody, 1));
        $this->assertEquals("String: 12345", $responseBody[0]->getData(), var_export($responseBody, 1));

    }

    /**
     * Test to make sure that AMF3 basic requests are handled for function calls.
     * This type of call is sent from net connection rather than RemoteObject
     *
     * @group ZF-4680
     */
    public function testShouldAllowHandlingFunctionCallsViaAmf3NetConnection()
    {
        // serialize the data to an AMF output stream
        $data = array('foo', 'bar');
        $this->_server->addFunction('ZendTest\\Amf\\TestAsset\\Server\\testFunction');
        $newBody = new Value\MessageBody("ZendTest\\Amf\\TestAsset\\Server\\testFunction","/1",$data);
        $request = new Request\StreamRequest();
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);
        $result = $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        // Now check if the return data was properly set.
        $this->assertTrue(0 < count($responseBody), var_export($responseBody, 1));
        $this->assertTrue(array_key_exists(0, $responseBody), var_export($responseBody, 1));
        $this->assertEquals("bar: foo", $responseBody[0]->getData(), var_export($responseBody, 1));
    }

    /**
     * Test sending data to the remote class and make sure we
     * recieve the proper response.
     *
     */
    public function testHandleLoadedClassAmf3()
    {
        // serialize the data to an AMF output stream
        $data[] = "12345";
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass');
        // create a mock remoting message
        $message = new Messaging\RemotingMessage();
        $message->operation = 'test1';
        $message->source = 'ZendTest\\Amf\\TestAsset\\Server\\testclass';
        $message->body = $data;
        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null,"/1",$message);
        $request = new Request\StreamRequest();
        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);
        // let the server handle mock request
        $result = $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        $this->assertTrue(0 < count($responseBody), var_export($responseBody, 1));
        $this->assertTrue(array_key_exists(0, $responseBody), var_export($responseBody, 1));
        // Now check if the return data was properly set.
        $acknowledgeMessage = $responseBody[0]->getData();
        // check that we have a message beening returned
        $this->assertTrue($acknowledgeMessage instanceof Messaging\AcknowledgeMessage);
        // Check the message body is the expected data to be returned
        $this->assertEquals("String: 12345", $acknowledgeMessage->body);
    }


    /**
     * Test to make sure that you can have the same method name in two different classes.
     *
     * @group ZF-5040
     */
    public function testSameMethodNameInTwoServices()
    {
        $this->_server->setClass('ServiceA');
        $this->_server->setClass('ServiceB');
        // create a mock remoting message
        $message = new Messaging\RemotingMessage();
        $message->operation = 'getMenu';
        $message->source = 'ServiceB';
        $message->body = array();
        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null,"/1",$message);
        $request = new Request\StreamRequest();
        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);
        // let the server handle mock request
        $result = $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        $this->assertTrue(0 < count($responseBody), var_export($responseBody, 1));
        $this->assertTrue(array_key_exists(0, $responseBody), var_export($responseBody, 1));
        // Now check if the return data was properly set.
        $acknowledgeMessage = $responseBody[0]->getData();
        // check that we have a message beening returned
        $this->assertTrue($acknowledgeMessage instanceof Messaging\AcknowledgeMessage);
        // Check the message body is the expected data to be returned
        $this->assertEquals("myMenuB", $acknowledgeMessage->body);
    }

    /**
     * test command message. THis is the first call the Flex
     * makes before any subsequent service calls.
     */
    public function testCommandMessagePingOperation()
    {
        $message = new Messaging\CommandMessage();
        $message->operation = 5;
        $message->messageId = $message->generateId();
        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null,"/1",$message);
        $request = new Request\StreamRequest();
        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);
        // let the server handle mock request
        $result = $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        $this->assertTrue(0 < count($responseBody), var_export($responseBody, 1));
        $this->assertTrue(array_key_exists(0, $responseBody), var_export($responseBody, 1));
        // Now check if the return data was properly set.
        $acknowledgeMessage = $responseBody[0]->getData();
        // check that we have a message beening returned
        $this->assertTrue($acknowledgeMessage instanceof Messaging\AcknowledgeMessage);
        // Check that the MessageID was not corrupeted when set to the correlationId
        $this->assertEquals($acknowledgeMessage->correlationId, $message->messageId);
    }

    public function testInvalidAmf0MessageShouldResultInErrorMessage()
    {
        // serialize the data to an AMF output stream
        $data[] = "12345";
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass');
        $newBody = new Value\MessageBody("bogus","/1",$data);
        $request = new Request\StreamRequest();
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x00);
        $result = $this->_server->handle($request);
        $bodies = $result->getAmfBodies();
        $found  = false;
        foreach ($bodies as $body) {
            $data = $body->getData();
            if (!is_array($data)) {
                continue;
            }
            if (!array_key_exists('description', $data)) {
                continue;
            }
            if (strstr($data['description'], 'does not exist')) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Invalid method did not raise error condition' . var_export($bodies, 1));
    }

    public function testInvalidCommandMessageShouldResultInErrorMessage()
    {
        $message = new Messaging\CommandMessage();
        $message->operation = 'pong';
        $message->messageId = $message->generateId();

        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null,"/1",$message);
        $request = new Request\StreamRequest();

        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);

        // let the server handle mock request
        $result = $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        $this->assertTrue(0 < count($responseBody), var_export($responseBody, 1));
        $this->assertTrue(array_key_exists(0, $responseBody), var_export($responseBody, 1));

        // Now check if the return data was properly set.
        $message = $responseBody[0]->getData();

        // check that we have a message beening returned
        $this->assertTrue($message instanceof Messaging\ErrorMessage);
    }

    /**
     * Add a class mapping and lookup the mapping to make sure
     * the mapping succeeds
     */
    public function testClassMap()
    {
        $this->_server->setClassMap('controller.test', 'ZendTest\\Amf\\TestAsset\\Server\\testclass');
        $className = Parser\TypeLoader::getMappedClassName('ZendTest\\Amf\\TestAsset\\Server\\testclass');
        $this->assertEquals('controller.test', $className);
    }

    public function testDispatchingMethodShouldReturnErrorMessageForInvalidMethod()
    {
        // serialize the data to an AMF output stream
        $data[] = "12345";
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass');

        // create a mock remoting message
        $message = new Messaging\RemotingMessage();
        $message->operation = 'bogus'; // INVALID method!
        $message->body      = $data;

        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null,"/1",$message);
        $request = new Request\StreamRequest();

        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);

        // let the server handle mock request
        $result = $this->_server->handle($request);
        $bodies = $result->getAmfBodies();
        $found  = false;
        foreach ($bodies as $body) {
            $data = $body->getData();
            if ($data instanceof Messaging\ErrorMessage) {
                if (strstr($data->faultString, 'does not exist')) {
                    $found = true;
                    break;
                }
            }
        }
        $this->assertTrue($found, 'Invalid method did not raise error condition: ' . var_export($bodies, 1));
    }

    public function testDispatchingMethodThatThrowsExceptionShouldReturnErrorMessageWhenProductionFlagOff()
    {
        // serialize the data to an AMF output stream
        $data = array();
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass');

        // create a mock remoting message
        $message = new Messaging\RemotingMessage();
        $message->operation = 'throwException';
        $message->source    = 'ZendTest\\Amf\\TestAsset\\Server\\testclass';
        $message->body      = $data;

        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null,"/1",$message);
        $request = new Request\StreamRequest();

        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);

        // let the server handle mock request
        $result = $this->_server->handle($request);
        $bodies = $result->getAmfBodies();
        $found  = false;
        foreach ($bodies as $body) {
            $data = $body->getData();
            if ($data instanceof Messaging\ErrorMessage) {
                if (strstr($data->faultString, 'should not be displayed')) {
                    $found = true;
                    break;
                }
            }
        }
        $this->assertTrue($found, 'Method raising exception should display error message when not in production');
    }

    public function testDispatchingMethodThatThrowsExceptionShouldNotReturnErrorMessageWhenProductionFlagOn()
    {
        // serialize the data to an AMF output stream
        $data = array();
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass')
                      ->setProduction(true);

        // create a mock remoting message
        $message = new Messaging\RemotingMessage();
        $message->operation = 'throwException';
        $message->source    = 'ZendTest\\Amf\\TestAsset\\Server\\testclass';
        $message->body      = $data;

        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null,"/1",$message);
        $request = new Request\StreamRequest();

        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);

        // let the server handle mock request
        $result = $this->_server->handle($request);
        $bodies = $result->getAmfBodies();
        $found  = false;
        foreach ($bodies as $body) {
            $data = $body->getData();
            if ($data instanceof Messaging\ErrorMessage) {
                if (strstr($data->faultString, 'should not be displayed')) {
                    $found = true;
                    break;
                }
            }
        }
        $this->assertFalse($found, 'Method raising exception should not display error message when in production');
    }

    public function testDispatchingMethodShouldPassInvocationArgumentsToMethod()
    {
        // serialize the data to an AMF output stream
        $data[] = "baz";
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass', '', 'foo', 'bar');

        // create a mock remoting message
        $message = new Messaging\RemotingMessage();
        $message->operation = 'checkArgv';
        $message->source    = 'ZendTest\\Amf\\TestAsset\\Server\\testclass';
        $message->body      = $data;

        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null, "/1" ,$message);
        $request = new Request\StreamRequest();

        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);

        // let the server handle mock request
        $result = $this->_server->handle($request);
        $bodies = $result->getAmfBodies();
        $found  = false;
        foreach ($bodies as $body) {
            $data  = $body->getData();
            if ('Zend\\Amf\\Value\\Messaging\\AcknowledgeMessage' == get_class($data)) {
                if ('baz:foo:bar' == $data->body) {
                    $found = true;
                    break;
                }
            }
        }
        $this->assertTrue($found, 'Valid response not found');
    }

    public function testServerShouldSeamlesslyInvokeStaticMethods()
    {
        // serialize the data to an AMF output stream
        $data[] = "testing";
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass');

        // create a mock remoting message
        $message = new Messaging\RemotingMessage();
        $message->operation = 'checkStaticUsage';
        $message->source    = 'ZendTest\\Amf\\TestAsset\\Server\\testclass';
        $message->body      = $data;

        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null, "/1" ,$message);
        $request = new Request\StreamRequest();

        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);

        // let the server handle mock request
        $result = $this->_server->handle($request);
        $bodies = $result->getAmfBodies();
        $found  = false;
        foreach ($bodies as $body) {
            $data  = $body->getData();
            if ('Zend\\Amf\\Value\\Messaging\\AcknowledgeMessage' == get_class($data)) {
                if ('testing' == $data->body) {
                    $found = true;
                    break;
                }
            }
        }
        $this->assertTrue($found, 'Valid response not found');
    }

    public function testServerShouldSeamlesslyInvokeFunctions()
    {
        // serialize the data to an AMF output stream
        $data[] = 'foo';
        $data[] = 'bar';
        $this->_server->addFunction('ZendTest\\Amf\\TestAsset\\Server\\testFunction');

        // create a mock remoting message
        $message = new Messaging\RemotingMessage();
        $message->operation = 'ZendTest\\Amf\\TestAsset\\Server\\testFunction';
        $message->source    = null;
        $message->body      = $data;

        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null, "/1" ,$message);
        $request = new Request\StreamRequest();

        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);

        // let the server handle mock request
        $result = $this->_server->handle($request);
        $bodies = $result->getAmfBodies();
        $found  = false;
        foreach ($bodies as $body) {
            $data  = $body->getData();
            if ('Zend\\Amf\\Value\\Messaging\\AcknowledgeMessage' == get_class($data)) {
                if ('bar: foo' == $data->body) {
                    $found = true;
                    break;
                }
            }
        }
        $this->assertTrue($found, 'Valid response not found');
    }

    public function testDispatchingMethodCorrespondingToClassWithPrivateConstructorShouldReturnErrorMessage()
    {
        // serialize the data to an AMF output stream
        $data[] = "baz";
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclassPrivate');

        // create a mock remoting message
        $message = new Messaging\RemotingMessage();
        $message->operation = 'test1';
        $message->source    = 'ZendTest\\Amf\\TestAsset\\Server\\testclassPrivate';
        $message->body      = $data;

        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null, "/1" ,$message);
        $request = new Request\StreamRequest();

        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);

        // let the server handle mock request
        $result = $this->_server->handle($request);
        $bodies = $result->getAmfBodies();
        $found  = false;
        foreach ($bodies as $body) {
            $data  = $body->getData();
            if ('Zend\\Amf\\Value\\Messaging\\ErrorMessage' == get_class($data)) {
                if (strstr($data->faultString, 'Error instantiating class')) {
                    $found = true;
                    break;
                }
            }
        }
        $this->assertTrue($found, 'Method succeeded?');
    }

    public function testNotPassingRequestToHandleShouldResultInServerCreatingRequest()
    {
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass');
        ob_start();
        $result  = $this->_server->handle();
        $content = ob_get_clean();
        $request = $this->_server->getRequest();
        $this->assertTrue($request instanceof Request\HttpRequest);
        $bodies  = $request->getAmfBodies();
        $this->assertEquals(0, count($bodies));
        $this->assertContains('Endpoint', $content);
    }

    public function testSetRequestShouldAllowValidStringClassNames()
    {
        $this->_server->setRequest('Zend\\Amf\\Request\\StreamRequest');
        $request = $this->_server->getRequest();
        $this->assertTrue($request instanceof Request\StreamRequest);
        $this->assertFalse($request instanceof Request\HttpRequest);
    }

    /**
     * @expectedException Zend\Amf\Exception\ExceptionInterface
     */
    public function testSetRequestShouldRaiseExceptionOnInvalidStringClassName()
    {
        @$this->_server->setRequest('ZendTest\\Amf\\ServerTest\\BogusRequest');
    }

    public function testSetRequestShouldAllowValidRequestObjects()
    {
        $request = new Request\StreamRequest;
        $this->_server->setRequest($request);
        $this->assertSame($request, $this->_server->getRequest());
    }

    /**
     * @expectedException Zend\Amf\Exception\ExceptionInterface
     */
    public function testSetRequestShouldRaiseExceptionOnInvalidRequestObjects()
    {
        $request = new \Zend\XmlRpc\Request;
        $this->_server->setRequest($request);
    }

    public function testSetResponseShouldAllowValidStringClassNames()
    {
        $this->_server->setResponse('Zend\\Amf\\Response\\StreamResponse');
        $response = $this->_server->getResponse();
        $this->assertTrue($response instanceof Response\StreamResponse);
        $this->assertFalse($response instanceof Response\HttpResponse);
    }

    /**
     * @expectedException Zend\Amf\Exception\ExceptionInterface
     */
    public function testSetResponseShouldRaiseExceptionOnInvalidStringClassName()
    {
        @$this->_server->setResponse('ZendTest\\Amf\\ServerTest\\BogusResponse');
    }

    public function testSetResponseShouldAllowValidResponseObjects()
    {
        $response = new Response\StreamResponse;
        $this->_server->setResponse($response);
        $this->assertSame($response, $this->_server->getResponse());
    }

    /**
     * @expectedException Zend\Amf\Exception\ExceptionInterface
     */
    public function testSetResponseShouldRaiseExceptionOnInvalidResponseObjects()
    {
        $response = new \Zend\XmlRpc\Response;
        $this->_server->setResponse($response);
    }

    public function testGetFunctionsShouldReturnArrayOfDispatchables()
    {
        $this->_server->addFunction('ZendTest\\Amf\\TestAsset\\Server\\testFunction', 'tf')
                      ->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass', 'tc')
                      ->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclassPrivate', 'tcp');
        $functions = $this->_server->getFunctions();
        $this->assertTrue(is_array($functions));
        $this->assertTrue(0 < count($functions));
        $namespaces = array('tf', 'tc', 'tcp');
        foreach ($functions as $key => $value) {
            $this->assertTrue(strstr($key, '.') ? true : false, $key);
            $ns = substr($key, 0, strpos($key, '.'));
            $this->assertContains($ns, $namespaces, $key);
            $this->assertTrue($value instanceof \Zend\Server\Reflection\AbstractFunction);
        }
    }

    public function testFaultShouldBeUnimplemented()
    {
        $this->assertNull($this->_server->fault());
    }

    public function testPersistenceShouldBeUnimplemented()
    {
        $this->assertNull($this->_server->setPersistence(true));
    }

    public function testLoadFunctionsShouldBeUnimplemented()
    {
        $this->assertNull($this->_server->loadFunctions(true));
    }

   /**
     * @group ZF-5388
     * Issue if only one parameter of type array is passed it is nested into another array.
     */
    public function testSingleArrayParamaterAMF3()
    {
            // serialize the data to an AMF output stream
        $data[] = array('item1', 'item2');
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass');
        // create a mock remoting message
        $message = new Messaging\RemotingMessage();
        $message->operation = 'testSingleArrayParamater';
        $message->source = 'ZendTest\\Amf\\TestAsset\\Server\\testclass';
        $message->body = $data;
        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null,"/1",$message);
        $request = new Request\StreamRequest();
        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);
        // let the server handle mock request
        $result = $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        $this->assertTrue(0 < count($responseBody), var_export($responseBody, 1));
        $this->assertTrue(array_key_exists(0, $responseBody), var_export($responseBody, 1));
        // Now check if the return data was properly set.
        $acknowledgeMessage = $responseBody[0]->getData();
        // check that we have a message beening returned
        $this->assertTrue($acknowledgeMessage instanceof Messaging\AcknowledgeMessage);
        // Check the message body is the expected data to be returned
        $this->assertTrue($acknowledgeMessage->body);
    }

     /**
     * @group ZF-5388
     * Issue if only one parameter of type array is passed it is nested into another array.
     */
    public function testSingleArrayParamaterAMF0()
    {
        $data[] = array('item1', 'item2');
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass');
        $newBody = new Value\MessageBody("ZendTest\\Amf\\TestAsset\\Server\\testclass.testSingleArrayParamater","/1",$data);
        $request = new Request\StreamRequest();
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x00);
        $result = $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        // Now check if the return data was properly set.
        $this->assertTrue(0 < count($responseBody), var_export($responseBody, 1));
        $this->assertTrue(array_key_exists(0, $responseBody), var_export($responseBody, 1));
        $this->assertTrue($responseBody[0]->getData(), var_export($responseBody, 1));
    }

    /**
     * @group ZF-5388
     * Issue if only one parameter of type array is passed it is nested into another array.
     */
    public function testMutiArrayParamaterAMF3()
    {
        // serialize the data to an AMF output stream
        $data[] = array('item1', 'item2');
        $data[] = array('item3', 'item4');
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass');
        // create a mock remoting message
        $message = new Messaging\RemotingMessage();
        $message->operation = 'testMultiArrayParamater';
        $message->source = 'ZendTest\\Amf\\TestAsset\\Server\\testclass';
        $message->body = $data;
        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null,"/1",$message);
        $request = new Request\StreamRequest();
        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);
        // let the server handle mock request
        $result = $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        $this->assertTrue(0 < count($responseBody), var_export($responseBody, 1));
        $this->assertTrue(array_key_exists(0, $responseBody), var_export($responseBody, 1));
        // Now check if the return data was properly set.
        $acknowledgeMessage = $responseBody[0]->getData();
        // check that we have a message beening returned
        $this->assertTrue($acknowledgeMessage instanceof Messaging\AcknowledgeMessage);
        // Check the message body is the expected data to be returned
        $this->assertEquals(4, count($acknowledgeMessage->body));
    }

     /**
     * @group ZF-5388
     * Issue if multipol parameters are sent and one is of type array is passed.
     */
    public function testMutiArrayParamaterAMF0()
    {
        $data[] = array('item1', 'item2');
        $data[] = array('item3', 'item4');
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass');
        $newBody = new Value\MessageBody("ZendTest\\Amf\\TestAsset\\Server\\testclass.testMultiArrayParamater","/1",$data);
        $request = new Request\StreamRequest();
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x00);
        $result = $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        // Now check if the return data was properly set.
        $this->assertTrue(0 < count($responseBody), var_export($responseBody, 1));
        $this->assertTrue(array_key_exists(0, $responseBody), var_export($responseBody, 1));
        $this->assertEquals(4, count($responseBody[0]->getData()), var_export($responseBody, 1));
    }

    /**
     * @group ZF-5346
     */
    public function testSingleObjectParamaterAMF3()
    {
        // serialize the data to an AMF output stream
        $data[] = array('item1', 'item2');
        $data[] = array('item3', 'item4');
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testclass');
        // create a mock remoting message
        $message = new Messaging\RemotingMessage();
        $message->operation = 'testMultiArrayParamater';
        $message->source = 'ZendTest\\Amf\\TestAsset\\Server\\testclass';
        $message->body = $data;
        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null,"/1",$message);
        $request = new Request\StreamRequest();
        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);
        // let the server handle mock request
        $result = $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        $this->assertTrue(0 < count($responseBody), var_export($responseBody, 1));
        $this->assertTrue(array_key_exists(0, $responseBody), var_export($responseBody, 1));
        // Now check if the return data was properly set.
        $acknowledgeMessage = $responseBody[0]->getData();
        // check that we have a message beening returned
        $this->assertTrue($acknowledgeMessage instanceof Messaging\AcknowledgeMessage);
        // Check the message body is the expected data to be returned
        $this->assertEquals(4, count($acknowledgeMessage->body));

    }

    /**
     * Check that when using server->setSession you get an amf header that has an append to gateway sessionID
     * @group ZF-5381
     */
    public function testSessionAmf3()
    {
        $this->session->start();
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testSession');
        $this->_server->setSession();

        // create a mock remoting message
        $message = new Messaging\RemotingMessage();
        $message->operation = 'getCount';
        $message->source = 'ZendTest\\Amf\\TestAsset\\Server\\testSession';
        $message->body = array();
        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null,"/1", $message);
        $request = new Request\StreamRequest();
        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);
        // let the server handle mock request
        $result = $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        // Now check if the return data was properly set.
        $acknowledgeMessage = $responseBody[0]->getData();
        // check that we have a message beening returned
        $this->assertEquals(1, $acknowledgeMessage->body);
        // check that a header is being returned for the session id
        $headerBody = $response->getAmfHeaders();
        $this->assertEquals('AppendToGatewayUrl',$headerBody[0]->name);

        // Do not stop session since it still can be used by other tests
        // Zend_Session::stop();
    }

    /* See ZF-7102 */
    public function testCtorExcection()
    {
        $this->_server->setClass('ZendTest\\Amf\\TestAsset\\Server\\testException');
        $this->_server->setProduction(false);
        $message = new Messaging\RemotingMessage();
        $message->operation = 'hello';
        $message->source = 'ZendTest\\Amf\\TestAsset\\Server\\testException';
        $message->body = array("123");
        // create a mock message body to place th remoting message inside
        $newBody = new Value\MessageBody(null,"/1", $message);
        $request = new Request\StreamRequest();
        // at the requested service to a request
        $request->addAmfBody($newBody);
        $request->setObjectEncoding(0x03);
        // let the server handle mock request
        $this->_server->handle($request);
        $response = $this->_server->getResponse()->getAMFBodies();
        $this->assertTrue($response[0]->getData() instanceof Messaging\ErrorMessage);
        $this->assertContains("Oops, exception!", $response[0]->getData()->faultString);
    }

    public function testAcceptsStringArgumentToSetBroker()
    {
        $this->_server->setBroker('Zend\View\HelperBroker');
        $this->assertInstanceOf('Zend\View\HelperBroker', $this->_server->getBroker());
    }

    public function testAcceptsBrokerObjectToSetBroker()
    {
        $broker = new \Zend\View\HelperBroker();
        $this->_server->setBroker($broker);
        $this->assertSame($broker, $this->_server->getBroker());
    }

    public function testRaisesExceptionOnNonClassStringBrokerArgument()
    {
        $this->setExpectedException('Zend\Amf\Exception\ExceptionInterface', 'could not resolve');
        $this->_server->setBroker('__foo__');
    }

    public function testRaisesExceptionOnNonBrokerObjectArgument()
    {
        $this->setExpectedException('Zend\Amf\Exception\ExceptionInterface', 'implement');
        $this->_server->setBroker($this);
    }
}
