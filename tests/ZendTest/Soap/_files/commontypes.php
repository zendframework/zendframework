<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Soap
 */

/* Test Functions */

/**
 * Test Function
 *
 * @param string $arg
 * @return string
 */
function Zend_Soap_AutoDiscover_TestFunc($who)
{
    return "Hello $who";
}

/**
 * Test Function 2
 */
function Zend_Soap_AutoDiscover_TestFunc2()
{
    return "Hello World";
}

/**
 * Return false
 *
 * @return bool
 */
function Zend_Soap_AutoDiscover_TestFunc3()
{
    return false;
}

/**
 * Return true
 *
 * @return bool
 */
function Zend_Soap_AutoDiscover_TestFunc4()
{
    return true;
}

/**
 * Return integer
 *
 * @return int
 */
function Zend_Soap_AutoDiscover_TestFunc5()
{
    return 123;
}

/**
 * Return string
 *
 * @return string
 */
function Zend_Soap_AutoDiscover_TestFunc6()
{
    return "string";
}

/**
 * Return array
 *
 * @return array
 */
function Zend_Soap_AutoDiscover_TestFunc7()
{
    return array('foo' => 'bar', 'baz' => true, 1 => false, 'bat' => 123);
}

/**
 * Return Object
 *
 * @return stdClass
 */
function Zend_Soap_AutoDiscover_TestFunc8()
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
function Zend_Soap_AutoDiscover_TestFunc9($foo, $bar)
{
    return "$foo $bar";
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 */
class Zend_Soap_AutoDiscover_TestFixingMultiplePrototypes
{
    /**
     * Test function
     *
     * @param integer $a
     * @param integer $b
     * @param integer $d
     * @return integer
     */
    public function testFunc($a=100, $b=200, $d=300)
    {

    }
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 */
class Zend_Soap_AutoDiscover_Test
{
    /**
     * Test Function 1
     *
     * @return string
     */
    public function testFunc1()
    {
        return "Hello World";
    }

    /**
     * Test Function 2
     *
     * @param string $who Some Arg
     * @return string
     */
    public function testFunc2($who)
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
    public function testFunc3($who, $when)
    {
        return "Hello $who, How are you $when";
    }

    /**
     * Test Function 4
     *
     * @return string
     */
    public static function testFunc4()
    {
        return "I'm Static!";
    }
}

class Zend_Soap_AutoDiscoverTestClass1
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
 */
class Zend_Soap_AutoDiscoverTestClass2
{
    /**
     *
     * @param Zend_Soap_AutoDiscoverTestClass1 $test
     * @return bool
     */
    public function add(Zend_Soap_AutoDiscoverTestClass1 $test)
    {
        return true;
    }

    /**
     * @return Zend_Soap_AutoDiscoverTestClass1[]
     */
    public function fetchAll()
    {
        return array(
            new Zend_Soap_AutoDiscoverTestClass1(),
            new Zend_Soap_AutoDiscoverTestClass1(),
        );
    }

    /**
     * @param Zend_Soap_AutoDiscoverTestClass1[]
     */
    public function addMultiple($test)
    {

    }
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 */
class Zend_Soap_Wsdl_ComplexTypeB
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
 */
class Zend_Soap_Wsdl_ComplexTypeA
{
    /**
     * @var Zend_Soap_Wsdl_ComplexTypeB[]
     */
    public $baz = array();
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 */
class Zend_Soap_Wsdl_ComplexTest
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
 */
class Zend_Soap_Wsdl_ComplexObjectStructure
{
    /**
     * @var bool
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
 */
class Zend_Soap_Wsdl_ComplexObjectWithObjectStructure
{
    /**
     * @var Zend_Soap_Wsdl_ComplexTest
     */
    public $object;
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 */
class Zend_Soap_AutoDiscover_MyService
{
    /**
     *    @param string $foo
     *    @return Zend_Soap_AutoDiscover_MyResponse[]
     */
    public function foo($foo)
    {
    }
    /**
     *    @param string $bar
     *    @return Zend_Soap_AutoDiscover_MyResponse[]
     */
    public function bar($bar)
    {
    }

    /**
     *    @param string $baz
     *    @return Zend_Soap_AutoDiscover_MyResponse[]
     */
    public function baz($baz)
    {
    }
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 */
class Zend_Soap_AutoDiscover_MyServiceSequence
{
    /**
     *    @param string $foo
     *    @return string[]
     */
    public function foo($foo)
    {
    }
    /**
     *    @param string $bar
     *    @return string[]
     */
    public function bar($bar)
    {
    }

    /**
     *    @param string $baz
     *    @return string[]
     */
    public function baz($baz)
    {
    }

    /**
     *    @param string $baz
     *    @return string[][][]
     */
    public function bazNested($baz)
    {
    }
}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 */
class Zend_Soap_AutoDiscover_MyResponse
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
 */
class Zend_Soap_AutoDiscover_Recursion
{
    /**
     * @var Zend_Soap_AutoDiscover_Recursion
     */
    public $recursion;

    /**
     * @return Zend_Soap_AutoDiscover_Recursion
     */
    public function create() {}
}

/**
 * @param string $message
 */
function Zend_Soap_AutoDiscover_OneWay($message)
{

}

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 */
class Zend_Soap_AutoDiscover_NoReturnType
{
    /**
     *
     * @param string $message
     */
    public function pushOneWay($message)
    {

    }
}
