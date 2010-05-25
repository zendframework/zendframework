<?php
/**
 * Phly - PHp LibrarY
 * 
 * @category   Phly
 * @package    Phly_PubSub
 * @subpackage Test
 * @copyright  Copyright (C) 2008 - Present, Matthew Weier O'Phinney
 * @author     Matthew Weier O'Phinney <mweierophinney@gmail.com> 
 * @license    New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 */

namespace ZendTest\SignalSlot;
use Zend\SignalSlot\Slot as Slot;

/**
 * @category   Phly
 * @package    Phly_PubSub
 * @subpackage Test
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 */
class SlotTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (isset($this->args)) {
            unset($this->args);
        }
    }

    public function testGetSignalShouldReturnSignal()
    {
        $slot = new Slot('foo', 'rand');
        $this->assertEquals('foo', $slot->getSignal());
    }

    public function testCallbackShouldBeStringIfNoHandlerPassedToConstructor()
    {
        $slot = new Slot('foo', 'rand');
        $this->assertSame('rand', $slot->getCallback());
    }

    public function testCallbackShouldBeArrayIfHandlerPassedToConstructor()
    {
        $slot = new Slot('foo', '\\ZendTest\\SignalSlot\\Slots\\ObjectCallback', 'test');
        $this->assertSame(array('\\ZendTest\\SignalSlot\\Slots\\ObjectCallback', 'test'), $slot->getCallback());
    }

    public function testCallShouldInvokeCallbackWithSuppliedArguments()
    {
        $slot = new Slot('foo', $this, 'handleCall');
        $args   = array('foo', 'bar', 'baz');
        $slot->call($args);
        $this->assertSame($args, $this->args);
    }

    /**
     * @expectedException \Zend\SignalSlot\InvalidCallbackException
     */
    public function testPassingInvalidCallbackShouldRaiseInvalidCallbackExceptionDuringCall()
    {
        $slot = new Slot('Invokable', 'boguscallback');
        $slot->call();
    }

    public function testCallShouldReturnTheReturnValueOfTheCallback()
    {
        $slot = new Slot('foo', '\\ZendTest\\SignalSlot\\Slots\\ObjectCallback', 'test');
        if (!is_callable(array('\\ZendTest\\SignalSlot\\Slots\\ObjectCallback', 'test'))) {
            echo "\nClass exists? " . var_export(class_exists('\\ZendTest\\SignalSlot\\Slots\\ObjectCallback'), 1) . "\n";
            echo "Include path: " . get_include_path() . "\n";
        }
        $this->assertEquals('bar', $slot->call(array()));
    }

    public function testStringCallbackResolvingToClassNameShouldCallViaInvoke()
    {
        $slot = new Slot('foo', '\\ZendTest\\SignalSlot\\Slots\\Invokable');
        $this->assertEquals('__invoke', $slot->call(), var_export($slot->getCallback(), 1));
    }

    /**
     * @expectedException \Zend\SignalSlot\InvalidCallbackException
     */
    public function testStringCallbackReferringToClassWithoutDefinedInvokeShouldRaiseException()
    {
        $slot = new Slot('foo', '\\ZendTest\\SignalSlot\\Slots\\InstanceMethod');
        $slot->call();
    }

    public function testCallbackConsistingOfStringContextWithNonStaticMethodShouldInstantiateContext()
    {
        $slot = new Slot('foo', 'ZendTest\\SignalSlot\\Slots\\InstanceMethod', 'callable');
        $this->assertEquals('callable', $slot->call());
    }

    public function testCallbackToClassImplementingOverloadingShouldSucceed()
    {
        $slot = new Slot('foo', '\\ZendTest\\SignalSlot\\Slots\\Overloadable', 'foo');
        $this->assertEquals('foo', $slot->call());
    }

    public function testClosureCallbackShouldBeInvokedByCall()
    {
        $slot = new Slot(null, function () {
            return 'foo';
        });
        $this->assertEquals('foo', $slot->call());
    }

    public function handleCall()
    {
        $this->args = func_get_args();
    }
}
