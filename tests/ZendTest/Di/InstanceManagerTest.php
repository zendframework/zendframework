<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Di
 */

namespace ZendTest\Di;

use Zend\Di\InstanceManager;
use PHPUnit_Framework_TestCase as TestCase;

class InstanceManagerTest extends TestCase
{

    public function testInstanceManagerCanPersistInstances()
    {
        $im = new InstanceManager();
        $obj = new TestAsset\BasicClass();
        $im->addSharedInstance($obj, 'ZendTest\Di\TestAsset\BasicClass');
        $this->assertTrue($im->hasSharedInstance('ZendTest\Di\TestAsset\BasicClass'));
        $this->assertSame($obj, $im->getSharedInstance('ZendTest\Di\TestAsset\BasicClass'));
    }

    public function testInstanceManagerCanPersistInstancesWithParameters()
    {
        $im = new InstanceManager();
        $obj1 = new TestAsset\BasicClass();
        $obj2 = new TestAsset\BasicClass();
        $obj3 = new TestAsset\BasicClass();

        $im->addSharedInstance($obj1, 'foo');
        $im->addSharedInstanceWithParameters($obj2, 'foo', array('foo' => 'bar'));
        $im->addSharedInstanceWithParameters($obj3, 'foo', array('foo' => 'baz'));

        $this->assertSame($obj1, $im->getSharedInstance('foo'));
        $this->assertSame($obj2, $im->getSharedInstanceWithParameters('foo', array('foo' => 'bar')));
        $this->assertSame($obj3, $im->getSharedInstanceWithParameters('foo', array('foo' => 'baz')));
    }

    /**
     * @group AliasAlias
     */
    public function testInstanceManagerCanResolveRecursiveAliases()
    {
        $im = new InstanceManager;
        $im->addAlias('bar-alias', 'Some\Class');
        $im->addAlias('foo-alias', 'bar-alias');
        $class = $im->getClassFromAlias('foo-alias');
        $this->assertEquals('Some\Class', $class);
    }

    /**
     * @group AliasAlias
     */
    public function testInstanceManagerThrowsExceptionForRecursiveAliases()
    {
        $im = new InstanceManager;
        $im->addAlias('bar-alias', 'foo-alias');
        $im->addAlias('foo-alias', 'bar-alias');

        $this->setExpectedException('Zend\Di\Exception\RuntimeException', 'recursion');
        $im->getClassFromAlias('foo-alias');
    }

    /**
     * @group AliasAlias
     */
    public function testInstanceManagerResolvesRecursiveAliasesForConfig()
    {
        $config = array('parameters' => array('username' => 'my-username'));

        $im = new InstanceManager;
        $im->addAlias('bar-alias', 'Some\Class');
        $im->addAlias('foo-alias', 'bar-alias');
        $im->setConfig('bar-alias', $config);

        $config['injections'] = array();
        $config['shared'] = true;

        $this->assertEquals($config, $im->getConfig('foo-alias'));
    }

}
