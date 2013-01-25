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
class Server2
{
    /**
     * @param  string $foo
     * @param  string $bar
     * @return \ZendTest\Soap\TestAsset\fulltests\ComplexTypeB
     */
    public function request($foo, $bar)
    {
        $b = new ComplexTypeB();
        $b->bar = $bar;
        $b->foo = $foo;
        return $b;
    }
}

if (isset($_GET['wsdl'])) {
    $server = new \Zend\Soap\AutoDiscover(new \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex());
} else {
    $uri = "http://".$_SERVER['HTTP_HOST']."/".$_SERVER['PHP_SELF']."?wsdl";
    $server = new \Zend\Soap\Server($uri);
}
$server->setClass('ZendTest\Soap\TestAsset\fulltests\Server2');
$server->handle();
