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
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\StrikeIron;
use Zend\Service\StrikeIron;

/**
 * Test helper
 */

/**
 * @see \Zend\Service\StrikeIron\Decorator
 */


/**
 * @category   Zend
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
