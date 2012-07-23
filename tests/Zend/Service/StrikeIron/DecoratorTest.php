<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\StrikeIron;

use Zend\Service\StrikeIron;

/**
 * @category   Zend
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_StrikeIron
 */
class DecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testNoNoticesWhenDecoratedObjectIsNotAnObject()
    {
        $decorator = new StrikeIron\Decorator(3.1415);
        $this->assertSame(null, $decorator->foo);
    }

    public function testDecoratorReturnsNullWhenPropertyIsMissing()
    {
        $object = new \stdclass();
        $decorator = new StrikeIron\Decorator($object);
        $this->assertSame(null, $decorator->foo);
    }

    public function testDecoratorReturnsPropertyByItsName()
    {
        $object = (object)array('Foo' => 'bar',
                                'Baz' => 'qux');
        $decorator = new StrikeIron\Decorator($object);
        $this->assertEquals('qux', $decorator->Baz);
    }

    public function testDecoratorReturnsPropertyByInflectedName()
    {
        $object = (object)array('Foo' => 'bar',
                                'Baz' => 'qux');
        $decorator = new StrikeIron\Decorator($object);
        $this->assertEquals('qux', $decorator->baz);
    }

    public function testDecoratorTriesActualPropertyNameBeforeInflecting()
    {
        $object = (object)array('foo' => 'bar',
                                'Foo' => 'qux');
        $decorator = new StrikeIron\Decorator($object);
        $this->assertEquals('bar', $decorator->foo);
    }

    public function testDecoratorReturnsAnotherDecoratorWhenValueIsAnObject()
    {
        $object = (object)array('Foo' => new \stdclass);
        $decorator = new StrikeIron\Decorator($object);
        $this->assertInstanceOf(get_class($decorator), $decorator->Foo);
    }

    public function testDecoratorProxiesMethodCalls()
    {
        $decorator = new StrikeIron\Decorator($this);
        $this->assertEquals('bar', $decorator->foo());
    }

    public function foo()
    {
        return 'bar';
    }

    public function testGettingTheDecoratedObject()
    {
        $decorator = new StrikeIron\Decorator($this);
        $this->assertSame($this, $decorator->getDecoratedObject());
    }

    public function testGettingDecoratedObjectName()
    {
        $decorator = new StrikeIron\Decorator($this, 'foo');
        $this->assertSame('foo', $decorator->getDecoratedObjectName());
    }
}
