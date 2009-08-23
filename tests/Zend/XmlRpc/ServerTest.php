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
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version $Id$
 */

// Call Zend_XmlRpc_ServerTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_XmlRpc_ServerTest::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Zend/XmlRpc/Server.php';
require_once 'Zend/XmlRpc/Request.php';
require_once 'Zend/XmlRpc/Response.php';

/**
 * Test case for Zend_XmlRpc_Server
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_XmlRpc
 */
class Zend_XmlRpc_ServerTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Zend_XmlRpc_Server object
     * @var Zend_XmlRpc_Server
     */
    protected $_server;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_XmlRpc_ServerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Setup environment
     */
    public function setUp() 
    {
        $this->_server = new Zend_XmlRpc_Server();
    }

    /**
     * Teardown environment
     */
    public function tearDown() 
    {
        unset($this->_server);
    }

    /**
     * __construct() test
     *
     * Call as method call 
     *
     * Returns: void 
     */
    public function test__construct()
    {
        $this->assertTrue($this->_server instanceof Zend_XmlRpc_Server);
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
        try {
            $this->_server->addFunction('Zend_XmlRpc_Server_testFunction', 'zsr');
        } catch (Zend_XmlRpc_Exception $e) {
            $this->fail('Attachment should have worked');
        }

        $methods = $this->_server->listMethods();
        $this->assertTrue(in_array('zsr.Zend_XmlRpc_Server_testFunction', $methods));

        try {
            $this->_server->addFunction('nosuchfunction');
            $this->fail('nosuchfunction() should not exist and should throw an exception');
        } catch (Zend_XmlRpc_Exception $e) {
            // do nothing
        }

        $server = new Zend_XmlRpc_Server();
        try {
            $server->addFunction(
                array(
                    'Zend_XmlRpc_Server_testFunction',
                    'Zend_XmlRpc_Server_testFunction2',
                ),
                'zsr'
            );
        } catch (Zend_XmlRpc_Exception $e) {
            $this->fail('Error attaching array of functions: ' . $e->getMessage());
        }
        $methods = $server->listMethods();
        $this->assertTrue(in_array('zsr.Zend_XmlRpc_Server_testFunction', $methods));
        $this->assertTrue(in_array('zsr.Zend_XmlRpc_Server_testFunction2', $methods));
    }

    /**
     * get/loadFunctions() test
     */
    public function testFunctions()
    {
        try {
            $this->_server->addFunction(
                array(
                    'Zend_XmlRpc_Server_testFunction',
                    'Zend_XmlRpc_Server_testFunction2',
                ),
                'zsr'
            );
        } catch (Zend_XmlRpc_Exception $e) {
            $this->fail('Error attaching functions: ' . $e->getMessage());
        }

        $expected = $this->_server->listMethods();

        $functions = $this->_server->getFunctions();
        $server = new Zend_XmlRpc_Server();
        $server->loadFunctions($functions);
        $actual = $server->listMethods();

        $this->assertSame($expected, $actual);
    }

    /**
     * setClass() test
     */
    public function testSetClass()
    {
        $this->_server->setClass('Zend_XmlRpc_Server_testClass', 'test');
        $methods = $this->_server->listMethods();
        $this->assertTrue(in_array('test.test1', $methods));
        $this->assertTrue(in_array('test.test2', $methods));
        $this->assertFalse(in_array('test._test3', $methods));
        $this->assertFalse(in_array('test.__construct', $methods));
    }

    public function testSettingClassWithArguments()
    {
        $this->_server->setClass('Zend_XmlRpc_Server_testClass', 'test', 'argv-argument');
        $request = new Zend_XmlRpc_Request();
        $request->setMethod('test.test4');
        $response = $this->_server->handle($request);
        $this->assertNotType('Zend_XmlRpc_Fault', $response);
        $this->assertSame(
            array('test1' => 'argv-argument',
                'test2' => null,
                'arg' => array('argv-argument')),
            $response->getReturnValue());
    }

    /**
     * fault() test
     */
    public function testFault()
    {
        $fault = $this->_server->fault('This is a fault', 411);
        $this->assertTrue($fault instanceof Zend_XmlRpc_Server_Fault);
        $this->assertEquals(411, $fault->getCode());
        $this->assertEquals('This is a fault', $fault->getMessage());

        $fault = $this->_server->fault(new Zend_XmlRpc_Server_Exception('Exception fault', 511));
        $this->assertTrue($fault instanceof Zend_XmlRpc_Server_Fault);
        $this->assertEquals(511, $fault->getCode());
        $this->assertEquals('Exception fault', $fault->getMessage());
    }

    /**
     * handle() test
     *
     * Call as method call 
     *
     * Expects:
     * - request: Optional; 
     * 
     * Returns: Zend_XmlRpc_Response|Zend_XmlRpc_Fault 
     */
    public function testHandle()
    {
        $request = new Zend_XmlRpc_Request();
        $request->setMethod('system.listMethods');
        $response = $this->_server->handle($request);

        $this->assertTrue($response instanceof Zend_XmlRpc_Response);
        $return = $response->getReturnValue();
        $this->assertTrue(is_array($return));
        $this->assertTrue(in_array('system.multicall', $return));
    }

    /**
     * Test that only calling methods using a valid parameter signature works
     */
    public function testHandle2()
    {
        $request = new Zend_XmlRpc_Request();
        $request->setMethod('system.methodHelp');
        $response = $this->_server->handle($request);

        $this->assertTrue($response instanceof Zend_XmlRpc_Fault);
        $this->assertEquals(623, $response->getCode());
    }

    /**
     * setResponseClass() test
     *
     * Call as method call 
     *
     * Expects:
     * - class: 
     * 
     * Returns: boolean 
     */
    public function testSetResponseClass()
    {
        $this->_server->setResponseClass('Zend_XmlRpc_Server_testResponse');
        $request = new Zend_XmlRpc_Request();
        $request->setMethod('system.listMethods');
        $response = $this->_server->handle($request);

        $this->assertTrue($response instanceof Zend_XmlRpc_Response);
        $this->assertTrue($response instanceof Zend_XmlRpc_Server_testResponse);
    }

    /**
     * listMethods() test
     *
     * Call as method call 
     *
     * Returns: array 
     */
    public function testListMethods()
    {
        $methods = $this->_server->listMethods();
        $this->assertTrue(is_array($methods));
        $this->assertTrue(in_array('system.listMethods', $methods));
        $this->assertTrue(in_array('system.methodHelp', $methods));
        $this->assertTrue(in_array('system.methodSignature', $methods));
        $this->assertTrue(in_array('system.multicall', $methods));
    }

    /**
     * methodHelp() test
     *
     * Call as method call 
     *
     * Expects:
     * - method: 
     * 
     * Returns: string 
     */
    public function testMethodHelp()
    {
        $help = $this->_server->methodHelp('system.listMethods');
        $this->assertContains('all available XMLRPC methods', $help);
    }

    /**
     * methodSignature() test
     *
     * Call as method call 
     *
     * Expects:
     * - method: 
     * 
     * Returns: array 
     */
    public function testMethodSignature()
    {
        $sig = $this->_server->methodSignature('system.methodSignature');
        $this->assertTrue(is_array($sig));
        $this->assertEquals(1, count($sig), var_export($sig, 1));
    }

    /**
     * multicall() test
     *
     * Call as method call 
     *
     * Expects:
     * - methods: 
     * 
     * Returns: array 
     */
    public function testMulticall()
    {
        $struct = array(
            array(
                'methodName' => 'system.listMethods',
                'params' => array()
            ),
            array(
                'methodName' => 'system.methodHelp',
                'params' => array('system.multicall')
            )
        );
        $request = new Zend_XmlRpc_Request();
        $request->setMethod('system.multicall');
        $request->addParam($struct);
        $response = $this->_server->handle($request);

        $this->assertTrue($response instanceof Zend_XmlRpc_Response, $response->__toString() . "\n\n" . $request->__toString());
        $returns = $response->getReturnValue();
        $this->assertTrue(is_array($returns));
        $this->assertEquals(2, count($returns), var_export($returns, 1));
        $this->assertTrue(is_array($returns[0]), var_export($returns[0], 1));
        $this->assertTrue(is_string($returns[1]), var_export($returns[1], 1));
    }

    /**
     * Test get/setEncoding()
     */
    public function testGetSetEncoding()
    {
        $this->assertEquals('UTF-8', $this->_server->getEncoding());
        $this->_server->setEncoding('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $this->_server->getEncoding());
    }

    /**
     * Test request/response encoding 
     */
    public function testRequestResponseEncoding()
    {
        $response = $this->_server->handle();
        $request  = $this->_server->getRequest();

        $this->assertEquals('UTF-8', $request->getEncoding());
        $this->assertEquals('UTF-8', $response->getEncoding());
    }

    /**
     * Test request/response encoding (alternate encoding)
     */
    public function testRequestResponseEncoding2()
    {
        $this->_server->setEncoding('ISO-8859-1');
        $response = $this->_server->handle();
        $request  = $this->_server->getRequest();

        $this->assertEquals('ISO-8859-1', $request->getEncoding());
        $this->assertEquals('ISO-8859-1', $response->getEncoding());
    }

    public function testAddFunctionWithExtraArgs()
    {
        $this->_server->addFunction('Zend_XmlRpc_Server_testFunction', 'test', 'arg1');
        $methods = $this->_server->listMethods();
        $this->assertContains('test.Zend_XmlRpc_Server_testFunction', $methods);
    }

    public function testAddFunctionThrowsExceptionWithBadData()
    {
        $o = new stdClass();
        try {
            $this->_server->addFunction($o);
            $this->fail('addFunction() should not accept objects');
        } catch (Zend_XmlRpc_Exception $e) {
            // success
        }
    }

    public function testLoadFunctionsThrowsExceptionWithBadData()
    {
        $o = new stdClass();
        try {
            $this->_server->loadFunctions($o);
            $this->fail('loadFunctions() should not accept objects');
        } catch (Zend_XmlRpc_Exception $e) {
            // success
        }

        $o = array($o);
        try {
            $this->_server->loadFunctions($o);
            $this->fail('loadFunctions() should not allow non-reflection objects in an array');
        } catch (Zend_Server_Exception $e) {
            $this->assertSame('Invalid method provided', $e->getMessage());
        }
    }

    public function testSetClassThrowsExceptionWithInvalidClass()
    {
        try {
            $this->_server->setClass('mybogusclass');
            $this->fail('setClass() should not allow invalid classes');
        } catch (Zend_XmlRpc_Exception $e) {
        }
    }

    public function testSetRequestUsingString()
    {
        $this->_server->setRequest('Zend_XmlRpc_Server_testRequest');
        $req = $this->_server->getRequest();
        $this->assertTrue($req instanceof Zend_XmlRpc_Server_testRequest);
    }

    public function testSetRequestThrowsExceptionOnBadClass()
    {
        try {
            $this->_server->setRequest('Zend_XmlRpc_Server_testRequest2');
            $this->fail('Invalid request class should throw exception');
        } catch (Zend_XmlRpc_Exception $e) {
            // success
        }

        try {
            $this->_server->setRequest($this);
            $this->fail('Invalid request object should throw exception');
        } catch (Zend_XmlRpc_Exception $e) {
            // success
        }
    }

    public function testHandleObjectMethod()
    {
        $this->_server->setClass('Zend_XmlRpc_Server_testClass');
        $request = new Zend_XmlRpc_Request();
        $request->setMethod('test1');
        $request->addParam('value');
        $response = $this->_server->handle($request);
        $this->assertFalse($response instanceof Zend_XmlRpc_Fault);
        $this->assertEquals('String: value', $response->getReturnValue());
    }

    public function testHandleClassStaticMethod()
    {
        $this->_server->setClass('Zend_XmlRpc_Server_testClass');
        $request = new Zend_XmlRpc_Request();
        $request->setMethod('test2');
        $request->addParam(array('value1', 'value2'));
        $response = $this->_server->handle($request);
        $this->assertFalse($response instanceof Zend_XmlRpc_Fault);
        $this->assertEquals('value1; value2', $response->getReturnValue());
    }

    public function testHandleFunction()
    {
        $this->_server->addFunction('Zend_XmlRpc_Server_testFunction');
        $request = new Zend_XmlRpc_Request();
        $request->setMethod('Zend_XmlRpc_Server_testFunction');
        $request->setParams(array(array('value1'), 'key'));
        $response = $this->_server->handle($request);
        $this->assertFalse($response instanceof Zend_XmlRpc_Fault);
        $this->assertEquals('key: value1', $response->getReturnValue());
    }

    public function testMulticallReturnsFaultsWithBadData()
    {
        // bad method array
        $try = array(
            'system.listMethods',
            array(
                'name' => 'system.listMethods'
            ),
            array(
                'methodName' => 'system.listMethods'
            ),
            array(
                'methodName' => 'system.listMethods',
                'params'     => ''
            ),
            array(
                'methodName' => 'system.multicall',
                'params'     => array()
            )
        );
        $returned = $this->_server->multicall($try);
        $this->assertTrue(is_array($returned));
        $this->assertEquals(5, count($returned));

        $response = $returned[0];
        $this->assertTrue(is_array($response));
        $this->assertTrue(isset($response['faultCode']));
        $this->assertEquals(601, $response['faultCode']);

        $response = $returned[1];
        $this->assertTrue(is_array($response));
        $this->assertTrue(isset($response['faultCode']));
        $this->assertEquals(602, $response['faultCode']);

        $response = $returned[2];
        $this->assertTrue(is_array($response));
        $this->assertTrue(isset($response['faultCode']));
        $this->assertEquals(603, $response['faultCode']);

        $response = $returned[3];
        $this->assertTrue(is_array($response));
        $this->assertTrue(isset($response['faultCode']));
        $this->assertEquals(604, $response['faultCode']);

        $response = $returned[4];
        $this->assertTrue(is_array($response));
        $this->assertTrue(isset($response['faultCode']));
        $this->assertEquals(605, $response['faultCode']);
    }

    /**
     * @see ZF-2872
     */
    public function testCanMarshalBase64Requests()
    {
        $this->_server->setClass('Zend_XmlRpc_Server_testClass', 'test');
        $data    = base64_encode('this is the payload');
        $param   = array('type' => 'base64', 'value' => $data);
        $request = new Zend_XmlRpc_Request('test.base64', array($param));

        $response = $this->_server->handle($request);
        $this->assertFalse($response instanceof Zend_XmlRpc_Fault);
        $this->assertEquals($data, $response->getReturnValue());
    }

    /**
     * @group ZF-6034
     */
    public function testPrototypeReturnValueMustReflectDocBlock()
    {
        $server = new Zend_XmlRpc_Server();
        $server->setClass('Zend_XmlRpc_Server_testClass');
        $table = $server->getDispatchTable();
        $method = $table->getMethod('test1');
        foreach ($method->getPrototypes() as $prototype) {
            $this->assertNotEquals('void', $prototype->getReturnType(), var_export($prototype, 1));
        }
    }

    public function testCallingUnregisteredMethod()
    {
        $this->setExpectedException('Zend_XmlRpc_Server_Exception',
            'Unknown instance method called on server: foobarbaz');
        $this->_server->foobarbaz();
    }
}

/**
 * Zend_XmlRpc_Server_testFunction 
 *
 * Function for use with xmlrpc server unit tests
 * 
 * @param array $var1 
 * @param string $var2 
 * @return string
 */
function Zend_XmlRpc_Server_testFunction($var1, $var2 = 'optional')
{
    return $var2 . ': ' . implode(',', (array) $var1);
}

/**
 * Zend_XmlRpc_Server_testFunction2
 *
 * Function for use with xmlrpc server unit tests
 * 
 * @return string
 */
function Zend_XmlRpc_Server_testFunction2()
{
    return 'function2';
}


class Zend_XmlRpc_Server_testClass
{
    private $_value1;
    private $_value2;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct($value1 = null, $value2 = null)
    {
        $this->_value1 = $value1;
        $this->_value2 = $value2;
    }

    /**
     * Test1 
     *
     * Returns 'String: ' . $string
     * 
     * @param string $string 
     * @return string
     */
    public function test1($string)
    {
        return 'String: ' . (string) $string;
    }

    /**
     * Test2 
     *
     * Returns imploded array
     * 
     * @param array $array 
     * @return string
     */
    public static function test2($array)
    {
        return implode('; ', (array) $array);
    }

    /**
     * Test3 
     *
     * Should not be available...
     * 
     * @return void
     */
    protected function _test3()
    {
    }

    /**
     * @param string $arg
     * @return struct
     */
    public function test4($arg)
    {
        return array('test1' => $this->_value1, 'test2' => $this->_value2, 'arg' => func_get_args());
    }

    /**
     * Test base64 encoding in request and response
     * 
     * @param  base64 $data 
     * @return base64
     */
    public function base64($data)
    {
        return $data;
    }
}

class Zend_XmlRpc_Server_testResponse extends Zend_XmlRpc_Response
{
}

class Zend_XmlRpc_Server_testRequest extends Zend_XmlRpc_Request
{
}

// Call Zend_XmlRpc_ServerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_XmlRpc_ServerTest::main") {
    Zend_XmlRpc_ServerTest::main();
}
