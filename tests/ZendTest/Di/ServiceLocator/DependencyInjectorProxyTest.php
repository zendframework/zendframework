<?php
namespace ZendTest\Di\ServiceLocator;

use Zend\Di\Di;
use Zend\Di\ServiceLocator\DependencyInjectorProxy;
use ZendTest\Di\TestAsset\SetterInjection\A;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Tests used to verify DependencyInjectorProxy functionality
 */
class DependencyInjectorProxyTest extends TestCase
{
    public function testWillDiscoverInjectedMethodParameters()
    {
        $di = new Di();
        $a = new A();
        $di->instanceManager()->setParameters(
            'ZendTest\Di\TestAsset\SetterInjection\B',
            array('a' => $a)
        );
        $proxy = new DependencyInjectorProxy($di);
        $b = $proxy->get('ZendTest\Di\TestAsset\SetterInjection\B');
        $methods = $b->getMethods();
        $this->assertSame('setA', $methods[0]['method']);
        $this->assertSame($a, $methods[0]['params'][0]);
    }
}
