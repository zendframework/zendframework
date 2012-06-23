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
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Soap\TestAsset;

/* Test Functions */

/**
 * Test Function
 *
 * @param string $arg
 * @return string
 */
function TestFunc($who)
{
    return "Hello $who";
}

/**
 * Test Function 2
 */
function TestFunc2()
{
    return "Hello World";
}

/**
 * Return false
 *
 * @return bool
 */
function TestFunc3()
{
    return false;
}

/**
 * Return true
 *
 * @return bool
 */
function TestFunc4()
{
    return true;
}

/**
 * Return integer
 *
 * @return int
 */
function TestFunc5()
{
    return 123;
}

/**
 * Return string
 *
 * @return string
 */
function TestFunc6()
{
    return "string";
}

/**
 * Return array
 *
 * @return array
 */
function TestFunc7()
{
    return array('foo' => 'bar', 'baz' => true, 1 => false, 'bat' => 123);
}

/**
 * Return Object
 *
 * @return StdClass
 */
function TestFunc8()
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
function TestFunc9($foo, $bar)
{
    return "$foo $bar";
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TestFixingMultiplePrototypes
{
    /**
     * Test function
     *
     * @param integer $a
     * @param integer $b
     * @param integer $d
     * @return integer
     */
    function testFunc($a=100, $b=200, $d=300)
    {

    }
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Test {
    /**
     * Test Function 1
     *
     * @return string
     */
    function testFunc1()
    {
        return "Hello World";
    }

    /**
     * Test Function 2
     *
     * @param string $who Some Arg
     * @return string
     */
    function testFunc2($who)
    {
        return "Hello $who!";
    }

    /**
     * Test Function 3
     *
     * @param string $who Some Arg
     * @param int $when Some
     * @return string
     */
    function testFunc3($who, $when)
    {
        return "Hello $who, How are you $when";
    }

    /**
     * Test Function 4
     *
     * @return string
     */
    static function testFunc4()
    {
        return "I'm Static!";
    }
}

class AutoDiscoverTestClass1
{
    /**
     * @var integer $var
     */
    public $var = 1;

    /**
     * @var string $param
     */
    public $param = "hello";
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AutoDiscoverTestClass2
{
    /**
     *
     * @param \ZendTest\Soap\TestAsset\AutoDiscoverTestClass1 $test
     * @return boolean
     */
    public function add(AutoDiscoverTestClass1 $test)
    {
        return true;
    }

    /**
     * @return \ZendTest\Soap\TestAsset\AutoDiscoverTestClass1[]
     */
    public function fetchAll()
    {
        return array(
            new AutoDiscoverTestClass1(),
            new AutoDiscoverTestClass1(),
        );
    }

    /**
     * @param \ZendTest\Soap\TestAsset\AutoDiscoverTestClass1[]
     */
    public function addMultiple($test)
    {

    }
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ComplexTypeB
{
    /**
     * @var string
     */
    public $bar;
    /**
     * @var string
     */
    public $foo;
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ComplexTypeA
{
    /**
     * @var \ZendTest\Soap\TestAsset\ComplexTypeB[]
     */
    public $baz = array();
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ComplexTest
{
    /**
     * @var int
     */
    public $var = 5;
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ComplexObjectStructure
{
    /**
     * @var boolean
     */
    public $boolean = true;

    /**
     * @var string
     */
    public $string = "Hello World";

    /**
     * @var int
     */
    public $int = 10;

    /**
     * @var array
     */
    public $array = array(1, 2, 3);
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ComplexObjectWithObjectStructure
{
    /**
     * @var \ZendTest\Soap\TestAsset\ComplexTest
     */
    public $object;
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MyService
{
    /**
     *    @param string $foo
     *    @return \ZendTest\Soap\TestAsset\MyResponse[]
     */
    public function foo($foo) {
    }
    /**
     *    @param string $bar
     *    @return \ZendTest\Soap\TestAsset\MyResponse[]
     */
    public function bar($bar) {
    }

    /**
     *    @param string $baz
     *    @return \ZendTest\Soap\TestAsset\MyResponse[]
     */
    public function baz($baz) {
    }
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MyServiceSequence
{
    /**
     *    @param string $foo
     *    @return string[]
     */
    public function foo($foo) {
    }
    /**
     *    @param string $bar
     *    @return string[]
     */
    public function bar($bar) {
    }

    /**
     *    @param string $baz
     *    @return string[]
     */
    public function baz($baz) {
    }

    /**
     *    @param string $baz
     *    @return string[][][]
     */
    public function bazNested($baz) {
    }
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MyResponse
{
    /**
     * @var string
     */
    public $p1;
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Recursion
{
    /**
     * @var \ZendTest\Soap\TestAsset\Recursion
     */
    public $recursion;

    /**
     * @return \ZendTest\Soap\TestAsset\Recursion
     */
    public function create() {}
}

/**
 * @param string $message
 */
function OneWay($message)
{

}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class NoReturnType
{
    /**
     *
     * @param string $message
     */
    public function pushOneWay($message)
    {

    }
}

/* Client test classes */
/** Test Class */
class TestClass {
    /**
     * Test Function 1
     *
     * @return string
     */
    function testFunc1()
    {
        return "Hello World";
    }

    /**
     * Test Function 2
     *
     * @param string $who Some Arg
     * @return string
     */
    function testFunc2($who)
    {
        return "Hello $who!";
    }

    /**
     * Test Function 3
     *
     * @param string $who Some Arg
     * @param int $when Some
     * @return string
     */
    function testFunc3($who, $when)
    {
        return "Hello $who, How are you $when";
    }

    /**
     * Test Function 4
     *
     * @return string
     */
    static function testFunc4()
    {
        return "I'm Static!";
    }
}

/** Test class 2 */
class TestData1 {
    /**
     * Property1
     *
     * @var string
     */
     public $property1;

    /**
     * Property2
     *
     * @var float
     */
     public $property2;
}

/** Test class 2 */
class TestData2 {
    /**
     * Property1
     *
     * @var integer
     */
     public $property1;

    /**
     * Property1
     *
     * @var float
     */
     public $property2;
}

class MockSoapServer {
    public $handle = null;
    public function handle()
    {
        $this->handle = func_get_args();
    }
    public function __call($name, $args) {}
}

class MockServer extends \Zend\Soap\Server {
    public $mockSoapServer = null;
    protected function _getSoap() {
        $this->mockSoapServer = new MockSoapServer();
        return $this->mockSoapServer;
    }
}


/** Server test classes */
class ServerTestClass
{
    /**
     * Test Function 1
     *
     * @return string
     */
    function testFunc1()
    {
        return "Hello World";
    }

    /**
     * Test Function 2
     *
     * @param string $who Some Arg
     * @return string
     */
    function testFunc2($who)
    {
        return "Hello $who!";
    }

    /**
     * Test Function 3
     *
     * @param string $who Some Arg
     * @param int $when Some
     * @return string
     */
    function testFunc3($who, $when)
    {
        return "Hello $who, How are you $when";
    }

    /**
     * Test Function 4
     *
     * @return string
     */
    static function testFunc4()
    {
        return "I'm Static!";
    }

    /**
     * Test Function 5 raises a user error
     *
     * @return void
     */
    function testFunc5()
    {
        trigger_error("Test Message", E_USER_ERROR);
    }
}

if (extension_loaded('soap')) {

/** Local SOAP client */
class TestLocalSoapClient extends \SoapClient
{
    /**
     * Server object
     *
     * @var Zend_Soap_Server
     */
    public $server;

    /**
     * Local client constructor
     *
     * @param Zend_Soap_Server $server
     * @param string $wsdl
     * @param array $options
     */
    function __construct(\Zend\Soap\Server $server, $wsdl, $options)
    {
        $this->server = $server;
        parent::__construct($wsdl, $options);
    }

    function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        ob_start();
        $this->server->handle($request);
        $response = ob_get_clean();

        return $response;
    }
}

}
