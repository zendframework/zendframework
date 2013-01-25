<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Soap
 */

namespace ZendTest\Soap\TestAsset\fulltests;

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 */
class Server1
{
    /**
     * @param  \ZendTest\Soap\TestAsset\fulltests\ComplexTypeB
     * @return \ZendTest\Soap\TestAsset\fulltests\ComplexTypeA[]
     */
    public function request($request)
    {
        $a = new ComplexTypeA();

        $b1 = new ComplexTypeB();
        $b1->bar = "bar";
        $b1->foo = "bar";
        $a->baz[] = $b1;

        $b2 = new ComplexTypeB();
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
 */
class ComplexTypeA
{
    /**
     * @var \ZendTest\Soap\TestAsset\fulltests\ComplexTypeB[]
     */
    public $baz = array();
}

if (isset($_GET['wsdl'])) {
    $server = new \Zend\Soap\AutoDiscover(new \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex());
} else {
    $uri = "http://".$_SERVER['HTTP_HOST']."/".$_SERVER['PHP_SELF']."?wsdl";
    $server = new \Zend\Soap\Server($uri);
}
$server->setClass('\ZendTest\Soap\TestAsset\fulltests\Server1');
$server->handle();
