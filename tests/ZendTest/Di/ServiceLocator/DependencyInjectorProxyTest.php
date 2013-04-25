<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

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
