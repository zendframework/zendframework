<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Soap
 */

require_once "Zend/Soap/AutoDiscover.php";
require_once "Zend/Soap/Server.php";
require_once "Zend/Soap/Wsdl/Strategy/ArrayOfTypeComplex.php";

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 */
class Zend_Soap_Service_Server1
{
    /**
     * @param  Zend_Soap_Wsdl_ComplexTypeB
     * @return Zend_Soap_Wsdl_ComplexTypeA[]
     */
    public function request($request)
    {
        $a = new Zend_Soap_Wsdl_ComplexTypeA();

        $b1 = new Zend_Soap_Wsdl_ComplexTypeB();
        $b1->bar = "bar";
        $b1->foo = "bar";
        $a->baz[] = $b1;

        $b2 = new Zend_Soap_Wsdl_ComplexTypeB();
        $b2->bar = "foo";
        $b2->foo = "foo";
        $a->baz[] = $b2;

        $a->baz[] = $request;

        return array($a);
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

if (isset($_GET['wsdl'])) {
    $server = new Zend_Soap_AutoDiscover(new Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex());
} else {
    $uri = "http://".$_SERVER['HTTP_HOST']."/".$_SERVER['PHP_SELF']."?wsdl";
    $server = new Zend_Soap_Server($uri);
}
$server->setClass('Zend_Soap_Service_Server1');
$server->handle();
