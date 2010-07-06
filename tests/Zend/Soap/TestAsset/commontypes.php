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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
// namespace ZendTest\Soap\TestAsset;

/* Test Functions */

/**
 * Test Function
 *
 * @param string $arg
 * @return string
 */
function ZendTest_Soap_TestAsset_TestFunc($who)
{
    return "Hello $who";
}

/**
 * Test Function 2
 */
function ZendTest_Soap_TestAsset_TestFunc2()
{
    return "Hello World";
}

/**
 * Return false
 *
 * @return bool
 */
function ZendTest_Soap_TestAsset_TestFunc3()
{
    return false;
}

/**
 * Return true
 *
 * @return bool
 */
function ZendTest_Soap_TestAsset_TestFunc4()
{
    return true;
}

/**
 * Return integer
 *
 * @return int
 */
function ZendTest_Soap_TestAsset_TestFunc5()
{
    return 123;
}

/**
 * Return string
 *
 * @return string
 */
function ZendTest_Soap_TestAsset_TestFunc6()
{
    return "string";
}

/**
 * Return array
 *
 * @return array
 */
function ZendTest_Soap_TestAsset_TestFunc7()
{
    return array('foo' => 'bar', 'baz' => true, 1 => false, 'bat' => 123);
}

/**
 * Return Object
 *
 * @return StdClass
 */
function ZendTest_Soap_TestAsset_TestFunc8()
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
function ZendTest_Soap_TestAsset_TestFunc9($foo, $bar)
{
    return "$foo $bar";
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendTest_Soap_TestAsset_TestFixingMultiplePrototypes
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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendTest_Soap_TestAsset_Test {
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

class ZendTest_Soap_TestAsset_AutoDiscoverTestClass1
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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendTest_Soap_TestAsset_AutoDiscoverTestClass2
{
    /**
     *
     * @param ZendTest_Soap_TestAsset_AutoDiscoverTestClass1 $test
     * @return boolean
     */
    public function add(AutoDiscoverTestClass1 $test)
    {
        return true;
    }

    /**
     * @return ZendTest_Soap_TestAsset_AutoDiscoverTestClass1[]
     */
    public function fetchAll()
    {
        return array(
            new AutoDiscoverTestClass1(),
            new AutoDiscoverTestClass1(),
        );
    }

    /**
     * @param ZendTest_Soap_TestAsset_AutoDiscoverTestClass1[]
     */
    public function addMultiple($test)
    {

    }
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendTest_Soap_TestAsset_ComplexTypeB
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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendTest_Soap_TestAsset_ComplexTypeA
{
    /**
     * @var ZendTest_Soap_TestAsset_ComplexTypeB[]
     */
    public $baz = array();
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendTest_Soap_TestAsset_ComplexTest
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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendTest_Soap_TestAsset_ComplexObjectStructure
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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendTest_Soap_TestAsset_ComplexObjectWithObjectStructure
{
    /**
     * @var ZendTest_Soap_TestAsset_ComplexTest
     */
    public $object;
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendTest_Soap_TestAsset_MyService
{
    /**
     *    @param string $foo
     *    @return ZendTest_Soap_TestAsset_MyResponse[]
     */
    public function foo($foo) {
    }
    /**
     *    @param string $bar
     *    @return ZendTest_Soap_TestAsset_MyResponse[]
     */
    public function bar($bar) {
    }

    /**
     *    @param string $baz
     *    @return ZendTest_Soap_TestAsset_MyResponse[]
     */
    public function baz($baz) {
    }
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendTest_Soap_TestAsset_MyServiceSequence
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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendTest_Soap_TestAsset_MyResponse
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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendTest_Soap_TestAsset_Recursion
{
    /**
     * @var ZendTest_Soap_TestAsset_Recursion
     */
    public $recursion;
}

/**
 * @param string $message
 */
function ZendTest_Soap_TestAsset_OneWay($message)
{

}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendTest_Soap_TestAsset_NoReturnType
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
class ZendTest_Soap_TestAsset_TestClass {
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
class ZendTest_Soap_TestAsset_TestData1 {
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
class ZendTest_Soap_TestAsset_TestData2 {
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

/** Server test classes */
class ZendTest_Soap_TestAsset_ServerTestClass 
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
class ZendTest_Soap_TestAsset_TestLocalSoapClient extends \SoapClient
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
