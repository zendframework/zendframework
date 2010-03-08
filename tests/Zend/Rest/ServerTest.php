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

// Call Zend_Rest_ServerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Rest_ServerTest::main");
}


/**
 * Zend_Rest_Server
 */

/**
 * Test cases for Zend_Rest_Server
 *
 * @category   Zend
 * @package    Zend_Rest
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Rest
 * @group      Zend_Rest_Server
 */
class Zend_Rest_ServerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Rest_ServerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (isset($this->request)) {
            $_REQUEST = $this->request;
        } else {
            $this->request = $_REQUEST;
        }
    }

    public function testAddFunctionSimple()
    {
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc');
        $funcs = $server->getFunctions();
        $this->assertTrue(isset($funcs['Zend_Rest_Server_TestFunc']), "Function not registered.");
    }

    public function testSetClass()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test');
        $funcs = $server->getFunctions();
        $this->assertTrue(isset($funcs['testFunc']), "Class Not Registered. testFunc not found");
        $this->assertTrue(isset($funcs['testFunc2']), "Class Not Registered. testFunc2 not found");
        $this->assertTrue(isset($funcs['testFunc3']), "Class Not Registered. testFunc3 not found");
    }

    public function testHandleNamedArgFunction()
    {
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc');
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc', 'who' => 'Davey'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_TestFunc generator="zend" version="1.0"><response>Hello Davey</response><status>success</status></Zend_Rest_Server_TestFunc>', $result, "Bad Result");
    }

    public function testHandleFunctionNoArgs()
    {
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc2');
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc2'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_TestFunc2 generator="zend" version="1.0"><response>Hello World</response><status>success</status></Zend_Rest_Server_TestFunc2>', $result, "Bad Result");
    }

    public function testHandleFunctionNoArgsRaisesFaultResponse()
    {
        $server = new Zend_Rest_Server();
        $server->returnResponse(true);
        $server->addFunction('Zend_Rest_Server_TestFunc');
        $result = $server->handle(array('method' => 'Zend_Rest_Server_TestFunc'));
        $this->assertContains('failed', $result);
    }

      public function testHandleFunctionNoArgsUsingRequest()
    {
        $_REQUEST = array(
            'method' => 'Zend_Rest_Server_TestFunc2'
        );
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc2');
        ob_start();
        $server->handle();
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_TestFunc2 generator="zend" version="1.0"><response>Hello World</response><status>success</status></Zend_Rest_Server_TestFunc2>', $result, "Bad Result");
    }

    public function testHandleAnonymousArgFunction()
    {
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc');
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc', 'arg1' => 'Davey'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_TestFunc generator="zend" version="1.0"><response>Hello Davey</response><status>success</status></Zend_Rest_Server_TestFunc>', $result, "Bad Result");
    }

    public function testHandleMultipleFunction()
    {

        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc2');
        $server->addFunction('Zend_Rest_Server_TestFunc');
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc2'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_TestFunc2 generator="zend" version="1.0"><response>Hello World</response><status>success</status></Zend_Rest_Server_TestFunc2>', $result, "Bad Result");
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc', 'arg1' => 'Davey'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_TestFunc generator="zend" version="1.0"><response>Hello Davey</response><status>success</status></Zend_Rest_Server_TestFunc>', $result, "Bad Result");
    }

    public function testHandleMethodNoArgs()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test');
        ob_start();
        $server->handle(array('method' => 'testFunc'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_Test generator="zend" version="1.0"><testFunc><response>Hello World</response><status>success</status></testFunc></Zend_Rest_Server_Test>', $result, 'Bad Result');
    }

    public function testHandleMethodOfClassWithConstructor()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test2', '', array('testing args'));
        ob_start();
        $server->handle(array('method' => 'test2Func1'));
        $result = ob_get_clean();
        $this->assertContains("testing args", $result, "Bad Result");
    }

    public function testHandleAnonymousArgMethod()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test');
        ob_start();
        $server->handle(array('method' => 'testFunc2', 'arg1' => "Davey"));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_Test generator="zend" version="1.0"><testFunc2><response>Hello Davey</response><status>success</status></testFunc2></Zend_Rest_Server_Test>', $result, 'Bad Result');
    }

    public function testHandleNamedArgMethod()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test');
        ob_start();
        $server->handle(array('method' => 'testFunc3', 'who' => "Davey", 'when' => 'today'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_Test generator="zend" version="1.0"><testFunc3><response>Hello Davey, How are you today</response><status>success</status></testFunc3></Zend_Rest_Server_Test>', $result, 'Bad Result');
    }

    public function testHandleStaticNoArgs()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test');
        ob_start();
        $server->handle(array('method' => 'testFunc4'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_Test generator="zend" version="1.0"><testFunc4><response>Hello World</response><status>success</status></testFunc4></Zend_Rest_Server_Test>', $result, var_export($result, 1));
    }

    public function testHandleAnonymousArgStatic()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test');
        ob_start();
        $server->handle(array('method' => 'testFunc5', 'arg1' => "Davey"));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_Test generator="zend" version="1.0"><testFunc5><response>Hello Davey</response><status>success</status></testFunc5></Zend_Rest_Server_Test>', $result, 'Bad Result');
    }

    public function testHandleNamedArgStatic()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test');
        ob_start();
        $server->handle(array('method' => 'testFunc6', 'who' => "Davey", 'when' => 'today'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_Test generator="zend" version="1.0"><testFunc6><response>Hello Davey, How are you today</response><status>success</status></testFunc6></Zend_Rest_Server_Test>', $result, 'Bad Result');
    }

    public function testHandleMultipleAnonymousArgs()
    {
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc9');
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc9', 'arg1' => "Hello", 'arg2' => "Davey"));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_TestFunc9 generator="zend" version="1.0"><response>Hello Davey</response><status>success</status></Zend_Rest_Server_TestFunc9>', $result, "Bad Result");
    }

    public function testHandleReturnFalse()
    {
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc3');
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc3'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_TestFunc3 generator="zend" version="1.0"><response>0</response><status>success</status></Zend_Rest_Server_TestFunc3>', $result, 'Bas Response');
    }

    public function testHandleReturnTrue()
    {
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc4');
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc4'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_TestFunc4 generator="zend" version="1.0"><response>1</response><status>success</status></Zend_Rest_Server_TestFunc4>', $result, 'Bas Response');
    }


    public function testHandleReturnInteger()
    {
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc5');
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc5'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_TestFunc5 generator="zend" version="1.0"><response>123</response><status>success</status></Zend_Rest_Server_TestFunc5>', $result, 'Bas Response');
    }

    public function testHandleReturnString()
    {
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc6');
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc6'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_TestFunc6 generator="zend" version="1.0"><response>string</response><status>success</status></Zend_Rest_Server_TestFunc6>', $result, 'Bas Response');
    }

    public function testHandleReturnArray()
    {
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc7');
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc7'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_TestFunc7 generator="zend" version="1.0"><foo>bar</foo><baz>1</baz><key_1>0</key_1><bat>123</bat><status>success</status></Zend_Rest_Server_TestFunc7>', $result, $result);
    }

    public function testHandleReturnNestedArray()
    {
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc12');
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc12'));
        $result = ob_get_clean();
        $this->assertContains('Zend_Rest_Server_TestFunc12', $result, $result);
        $this->assertContains('<foo><baz>1</baz>', $result, $result);
        $this->assertContains('<bat>123</bat></foo><bar>baz</bar>', $result, $result);
        $this->assertContains('</bar><status>success</status', $result, $result);
    }

    public function testHandleMethodReturnObject()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test2');
        ob_start();
        $server->handle(array('method' => 'test2Struct'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_Test2', $result, $result);
        $this->assertContains('<test2Struct', $result, $result);
        $this->assertContains('<foo><baz>1</baz>', $result, $result);
        $this->assertContains('<bat>123</bat></foo><bar>baz</bar>', $result, $result);
        $this->assertContains('</bar><status>success</status', $result, $result);
    }

    public function testHandleReturnObject()
    {
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc8');
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc8'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_TestFunc8 generator="zend" version="1.0"><foo>bar</foo><baz>1</baz><bat>123</bat><qux>0</qux><status>success</status></Zend_Rest_Server_TestFunc8>', $result, $result);
    }

    public function testHandleReturnSimpleXml()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test2');
        ob_start();
        $server->handle(array('method' => 'test2Xml'));
        $result = ob_get_clean();
        $this->assertContains("<foo>bar</foo>", $result, "Bad Result");
    }

    public function testHandleReturnDomDocument()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test2');
        ob_start();
        $server->handle(array('method' => 'test2DomDocument'));
        $result = ob_get_clean();
        $this->assertContains("<foo>bar</foo>", $result, "Bad Result");
    }

    public function testHandleReturnDomElement()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test2');
        ob_start();
        $server->handle(array('method' => 'test2DomElement'));
        $result = ob_get_clean();
        $this->assertContains("<foo>bar</foo>", $result, "Bad Result");
    }

    /**
     * @group ZF-3751
     */
    public function testHandleInvalidMethod()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test2');
        $server->returnResponse(true);
        $response = $server->handle(array('method' => 'test3DomElement'));
        $this->assertContains('<status>failed</status>', $response);
        $this->assertNotContains('<message>An unknown error occured. Please try again.</message>', $response);
    }

    public function testFault()
    {
        $e = new Exception('testing fault');
        $server = new Zend_Rest_Server();
        $fault = $server->fault($e);
        $this->assertTrue($fault instanceof DOMDocument);
        $sx = simplexml_import_dom($fault);
        $this->assertTrue(isset($sx->response));
        $this->assertTrue(isset($sx->response->message));
        $this->assertContains('testing fault', (string) $sx->response->message);
    }

    public function testFaultWithoutException()
    {
        $server = new Zend_Rest_Server();
        $fault = $server->fault('testing fault');
        $this->assertTrue($fault instanceof DOMDocument);
        $sx = simplexml_import_dom($fault);
        $this->assertTrue(isset($sx->response));
        $this->assertTrue(isset($sx->response->message));
        $this->assertContains('An unknown error occured. Please try again.', (string) $sx->response->message);
    }

    public function testHandleException()
    {
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc11');
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc11'));
        $result = ob_get_clean();
        $this->assertContains("<Zend_Rest_Server_TestFunc11", $result);
        $this->assertContains("<message>testing rest server faults</message>", $result);
    }

    public function testHandleClassMethodException()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test2');
        ob_start();
        $server->handle(array('method' => 'test2ThrowException'));
        $result = ob_get_clean();
        $this->assertContains("<Zend_Rest_Server_Test2", $result);
        $this->assertContains("<test2ThrowException>", $result);
        $this->assertContains("<message>testing class method exception</message>", $result);
    }

    public function testHandleVoid()
    {
        $server = new Zend_Rest_Server();
        $server->addFunction('Zend_Rest_Server_TestFunc10');
        ob_start();
        $server->handle(array('method' => 'Zend_Rest_Server_TestFunc10'));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_TestFunc10 generator="zend" version="1.0"><response/><status>success</status></Zend_Rest_Server_TestFunc10>', $result, $result);
    }

    public function testGetHeaders()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test2');
        ob_start();
        $server->handle(array('method' => 'test2ThrowException'));
        $result = ob_get_clean();
        $headers = $server->getHeaders();
        $this->assertContains('HTTP/1.0 400 Bad Request', $headers);
    }

    public function testReturnResponse()
    {
        $server = new Zend_Rest_Server();
        $this->assertFalse($server->returnResponse());
        $server->returnResponse(true);
        $this->assertTrue($server->returnResponse());
    }

    public function testReturnResponseForcesHandleToReturnResponse()
    {
        $server = new Zend_Rest_Server();
        $server->returnResponse(true);
        $server->setClass('Zend_Rest_Server_Test2');
        ob_start();
        $response = $server->handle(array('method' => 'test2Xml'));
        $result = ob_get_clean();
        $this->assertTrue(empty($result));
        $this->assertContains('<foo>bar</foo>', $response);
    }

    public function testGeneratedXmlEncodesScalarAmpersands()
    {
        $server = new Zend_Rest_Server();
        $server->returnResponse(true);
        $server->setClass('Zend_Rest_Server_Test');
        ob_start();
        $response = $server->handle(array('method' => 'testScalarEncoding'));
        $result = ob_get_clean();
        $this->assertTrue(empty($result));
        $this->assertContains('This string has chars &amp; ampersands', $response);
    }

    public function testGeneratedXmlEncodesStructAmpersands()
    {
        $server = new Zend_Rest_Server();
        $server->returnResponse(true);
        $server->setClass('Zend_Rest_Server_Test');
        ob_start();
        $response = $server->handle(array('method' => 'testStructEncoding'));
        $result = ob_get_clean();
        $this->assertTrue(empty($result));
        $this->assertContains('bar &amp; baz', $response);
    }

    public function testGeneratedXmlEncodesFaultAmpersands()
    {
        $server = new Zend_Rest_Server();
        $server->returnResponse(true);
        $server->setClass('Zend_Rest_Server_Test');
        ob_start();
        $response = $server->handle(array('method' => 'testExceptionsEncoding'));
        $result = ob_get_clean();
        $this->assertTrue(empty($result));
        $this->assertContains('testing class method exception &amp; encoding', $response);
    }

    /**
     * @see ZF-1992
     * @group ZF-1992
     */
    public function testDefaultEncodingShouldBeUtf8()
    {
        $server = new Zend_Rest_Server();
        $this->assertEquals('UTF-8', $server->getEncoding());
    }

    /**
     * @see ZF-1992
     * @group ZF-1992
     */
    public function testEncodingShouldBeMutableViaAccessors()
    {
        $server = new Zend_Rest_Server();
        $this->assertEquals('UTF-8', $server->getEncoding());
        $server->setEncoding('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $server->getEncoding());
    }

    /**
     * @see ZF-2279
     * @group ZF-2279
     */
    public function testNamesOfArgumentsShouldDetermineArgumentOrder()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test');
        ob_start();
        $server->handle(array('method' => 'testFunc6', 'arg2' => 'today', 'arg1' => "Davey"));
        $result = ob_get_clean();
        $this->assertContains('<Zend_Rest_Server_Test generator="zend" version="1.0"><testFunc6><response>Hello Davey, How are you today</response><status>success</status></testFunc6></Zend_Rest_Server_Test>', $result, var_export($result, 1));
    }

    /**
     * @see ZF-1949
     * @see ZF-7977
     * @group ZF-1949
     * @group ZF-7977
     */
    public function testMissingArgumentsShouldResultInFaultResponse()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test');
        ob_start();
        $server->handle(array('method' => 'testFunc6', 'arg1' => 'Davey'));
        $result = ob_get_clean();
        $this->assertRegexp('#<message>Invalid Method Call to(.*?)(Missing argument\(s\): ).*?(</message>)#', $result);
        $this->assertContains('<status>failed</status>', $result);
    }

    /**
     * @see ZF-1949
     * @group ZF-1949
     */
    public function testMissingArgumentsWithDefaultsShouldNotResultInFaultResponse()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test');
        ob_start();
        $server->handle(array('method' => 'testFunc7', 'arg1' => "Davey"));
        $result = ob_get_clean();
        $this->assertContains('<status>success</status>', $result, var_export($result, 1));
        $this->assertContains('<response>Hello today, How are you Davey</response>', $result, var_export($result, 1));
    }

    /**
     * @group ZF-3751
     */
    public function testCallingUnknownMethodDoesNotThrowUnknownButSpecificErrorExceptionMessage()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test2');
        $server->returnResponse(true);
        $response = $server->handle(array('method' => 'testCallingInvalidMethod'));
        $this->assertContains('<status>failed</status>', $response);
        $this->assertNotContains('<message>An unknown error occured. Please try again.</message>', $response);
    }

    /**
     * @group ZF-3751
     */
    public function testCallingNoMethodDoesNotThrowUnknownButSpecificErrorExceptionMessage()
    {
        $server = new Zend_Rest_Server();
        $server->setClass('Zend_Rest_Server_Test2');
        $server->returnResponse(true);
        $response = $server->handle();
        $this->assertContains('<status>failed</status>', $response);
        $this->assertNotContains('<message>An unknown error occured. Please try again.</message>', $response);
    }
}

/* Test Functions */

/**
 * Test Function
 *
 * @param string $arg
 * @return string
 */
function Zend_Rest_Server_TestFunc($who)
{
    return "Hello $who";
}

/**
 * Test Function 2
 */
function Zend_Rest_Server_TestFunc2()
{
    return "Hello World";
}

/**
 * Return false
 *
 * @return bool
 */
function Zend_Rest_Server_TestFunc3()
{
    return false;
}

/**
 * Return true
 *
 * @return bool
 */
function Zend_Rest_Server_TestFunc4()
{
    return true;
}

/**
 * Return integer
 *
 * @return int
 */
function Zend_Rest_Server_TestFunc5()
{
    return 123;
}

/**
 * Return string
 *
 * @return string
 */
function Zend_Rest_Server_TestFunc6()
{
    return "string";
}

/**
 * Return array
 *
 * @return array
 */
function Zend_Rest_Server_TestFunc7()
{
    return array('foo' => 'bar', 'baz' => true, 1 => false, 'bat' => 123);
}

/**
 * Return Object
 *
 * @return StdClass
 */
function Zend_Rest_Server_TestFunc8()
{
    $return = (object) array('foo' => 'bar', 'baz' => true, 'bat' => 123, 'qux' => false);
    return $return;
}

/**
 * Multiple Args
 *
 * @param string $foo
 * @param string $bar
 * @return string
 */
function Zend_Rest_Server_TestFunc9($foo, $bar)
{
    return "$foo $bar";
}

/**
 * Void arguments
 *
 * @return void
 */
function Zend_Rest_Server_TestFunc10()
{
    // returns nothing
}

/**
 * throws exception
 *
 * @return void
 * @throws Exception
 */
function Zend_Rest_Server_TestFunc11()
{
    throw new Exception('testing rest server faults');
}

/**
 * Return nested array
 *
 * @return struct
 */
function Zend_Rest_Server_TestFunc12()
{
    return array('foo' => array('baz' => true, 1 => false, 'bat' => 123), 'bar' => 'baz');
}


/**
 * Test Class
 */
class Zend_Rest_Server_Test
{
    /**
     * Test Function
     */
    public function testFunc()
    {
        return "Hello World";
    }

    /**
     * Test Function 2
     *
     * @param string $who Some Arg
     */
    public function testFunc2($who)
    {
        return "Hello $who";
    }

    /**
     * Test Function 3
     *
     * @param string $who Some Arg
     * @param int $when Some Arg2
     */
    public function testFunc3($who, $when)
    {
        return "Hello $who, How are you $when";
    }

    /**
     * Test Function 4
     */
    public static function testFunc4()
    {
        return "Hello World";
    }

    /**
     * Test Function 5
     *
     * @param string $who Some Arg
     */
    public static function testFunc5($who)
    {
        return "Hello $who";
    }

    /**
     * Test Function 6
     *
     * @param string $who Some Arg
     * @param int $when Some Arg2
     */
    public static function testFunc6($who, $when)
    {
        return "Hello $who, How are you $when";
    }

    /**
     * Test Function 7
     *
     * @param string $who Some Arg
     * @param int $when Some Arg2
     */
    public static function testFunc7($who, $when = 'today')
    {
        return "Hello $who, How are you $when";
    }

    /**
     * Test scalar encoding
     *
     * @return string
     */
    public function testScalarEncoding()
    {
        return 'This string has chars & ampersands';
    }

    /**
     * Test structs encode correctly
     *
     * @return struct
     */
    public function testStructEncoding()
    {
        return array(
            'foo' => 'bar & baz'
        );
    }

    /**
     * Test exceptions encode correctly
     *
     * @return void
     */
    public function testExceptionsEncoding()
    {
        throw new Exception('testing class method exception & encoding');
    }
}

class Zend_Rest_Server_Test2
{
    public function __construct($arg1 = 'unset')
    {
        $this->arg1 = $arg1;
    }

    public function test2Func1()
    {
        return $this->arg1;
    }

    public function test2Xml()
    {
        $sx = new SimpleXMLElement('<root><foo>bar</foo></root>');
        return $sx;
    }

    public function test2DomDocument()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement('root');
        $dom->appendChild($root);

        $foo = $dom->createElement('foo', 'bar');
        $root->appendChild($foo);

        return $dom;
    }

    public function test2DomElement()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement('root');
        $dom->appendChild($root);

        $foo = $dom->createElement('foo', 'bar');
        $root->appendChild($foo);

        return $foo;
    }

    public function test2ThrowException()
    {
        throw new Exception('testing class method exception');
    }

    public function test2Struct()
    {
        $o = new stdClass();
        $o->foo = array('baz' => true, 1 => false, 'bat' => 123);
        $o->bar = 'baz';

        return $o;
    }
}

class Zend_Rest_TestException extends Exception { }

// Call Zend_Rest_ServerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Rest_ServerTest::main") {
    Zend_Rest_ServerTest::main();
}
