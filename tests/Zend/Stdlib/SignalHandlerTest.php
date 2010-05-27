<?php
/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage Test
 * @copyright  Copyright (c) 2010 - Present Zend Technologies USA Inc. (http://www.zend.com)
 * @license    New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 */

namespace ZendTest\SignalSlot;

use Zend\Stdlib\SignalHandler;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage Test
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 */
class SignalHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (isset($this->args)) {
            unset($this->args);
        }
    }

    public function testGetSignalShouldReturnSignal()
    {
        $handler = new SignalHandler('foo', 'rand');
        $this->assertEquals('foo', $handler->getSignal());
    }

    public function testCallbackShouldBeStringIfNoHandlerPassedToConstructor()
    {
        $handler = new SignalHandler('foo', 'rand');
        $this->assertSame('rand', $handler->getCallback());
    }

    public function testCallbackShouldBeArrayIfHandlerPassedToConstructor()
    {
        $handler = new SignalHandler('foo', '\\ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback', 'test');
        $this->assertSame(array('\\ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback', 'test'), $handler->getCallback());
    }

    public function testCallShouldInvokeCallbackWithSuppliedArguments()
    {
        $handler = new SignalHandler('foo', $this, 'handleCall');
        $args   = array('foo', 'bar', 'baz');
        $handler->call($args);
        $this->assertSame($args, $this->args);
    }

    /**
     * @expectedException \Zend\Stdlib\InvalidCallbackException
     */
    public function testPassingInvalidCallbackShouldRaiseInvalidCallbackExceptionDuringCall()
    {
        $handler = new SignalHandler('Invokable', 'boguscallback');
        $handler->call();
    }

    public function testCallShouldReturnTheReturnValueOfTheCallback()
    {
        $handler = new SignalHandler('foo', '\\ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback', 'test');
        if (!is_callable(array('\\ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback', 'test'))) {
            echo "\nClass exists? " . var_export(class_exists('\\ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback'), 1) . "\n";
            echo "Include path: " . get_include_path() . "\n";
        }
        $this->assertEquals('bar', $handler->call(array()));
    }

    public function testStringCallbackResolvingToClassNameShouldCallViaInvoke()
    {
        $handler = new SignalHandler('foo', '\\ZendTest\\Stdlib\\SignalHandlers\\Invokable');
        $this->assertEquals('__invoke', $handler->call(), var_export($handler->getCallback(), 1));
    }

    /**
     * @expectedException \Zend\Stdlib\InvalidCallbackException
     */
    public function testStringCallbackReferringToClassWithoutDefinedInvokeShouldRaiseException()
    {
        $handler = new SignalHandler('foo', '\\ZendTest\\Stdlib\\SignalHandlers\\InstanceMethod');
        $handler->call();
    }

    public function testCallbackConsistingOfStringContextWithNonStaticMethodShouldInstantiateContext()
    {
        $handler = new SignalHandler('foo', 'ZendTest\\Stdlib\\SignalHandlers\\InstanceMethod', 'callable');
        $this->assertEquals('callable', $handler->call());
    }

    public function testCallbackToClassImplementingOverloadingShouldSucceed()
    {
        $handler = new SignalHandler('foo', '\\ZendTest\\Stdlib\\SignalHandlers\\Overloadable', 'foo');
        $this->assertEquals('foo', $handler->call());
    }

    public function testClosureCallbackShouldBeInvokedByCall()
    {
        $handler = new SignalHandler(null, function () {
            return 'foo';
        });
        $this->assertEquals('foo', $handler->call());
    }

    public function handleCall()
    {
        $this->args = func_get_args();
    }
}
