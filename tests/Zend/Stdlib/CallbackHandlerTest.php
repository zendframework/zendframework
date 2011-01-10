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
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

namespace ZendTest\SignalSlot;

use Zend\Stdlib\CallbackHandler;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @group      Zend_Stdlib
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class CallbackHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (isset($this->args)) {
            unset($this->args);
        }
    }

    public function testGetSignalShouldReturnSignal()
    {
        $handler = new CallbackHandler('foo', 'rand');
        $this->assertEquals('foo', $handler->getSignal());
    }

    public function testCallbackShouldBeStringIfNoHandlerPassedToConstructor()
    {
        $handler = new CallbackHandler('foo', 'rand');
        $this->assertSame('rand', $handler->getCallback());
    }

    public function testCallbackShouldBeArrayIfHandlerPassedToConstructor()
    {
        $handler = new CallbackHandler('foo', '\\ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback', 'test');
        $this->assertSame(array('\\ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback', 'test'), $handler->getCallback());
    }

    public function testCallShouldInvokeCallbackWithSuppliedArguments()
    {
        $handler = new CallbackHandler('foo', $this, 'handleCall');
        $args   = array('foo', 'bar', 'baz');
        $handler->call($args);
        $this->assertSame($args, $this->args);
    }

    /**
     * @expectedException \Zend\Stdlib\InvalidCallbackException
     */
    public function testPassingInvalidCallbackShouldRaiseInvalidCallbackExceptionDuringCall()
    {
        $handler = new CallbackHandler('Invokable', 'boguscallback');
        $handler->call();
    }

    public function testCallShouldReturnTheReturnValueOfTheCallback()
    {
        $handler = new CallbackHandler('foo', '\\ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback', 'test');
        if (!is_callable(array('\\ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback', 'test'))) {
            echo "\nClass exists? " . var_export(class_exists('\\ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback'), 1) . "\n";
            echo "Include path: " . get_include_path() . "\n";
        }
        $this->assertEquals('bar', $handler->call(array()));
    }

    public function testStringCallbackResolvingToClassNameShouldCallViaInvoke()
    {
        $handler = new CallbackHandler('foo', '\\ZendTest\\Stdlib\\SignalHandlers\\Invokable');
        $this->assertEquals('__invoke', $handler->call(), var_export($handler->getCallback(), 1));
    }

    /**
     * @expectedException \Zend\Stdlib\InvalidCallbackException
     */
    public function testStringCallbackReferringToClassWithoutDefinedInvokeShouldRaiseException()
    {
        $handler = new CallbackHandler('foo', '\\ZendTest\\Stdlib\\SignalHandlers\\InstanceMethod');
        $handler->call();
    }

    public function testCallbackConsistingOfStringContextWithNonStaticMethodShouldInstantiateContext()
    {
        $handler = new CallbackHandler('foo', 'ZendTest\\Stdlib\\SignalHandlers\\InstanceMethod', 'callable');
        $this->assertEquals('callable', $handler->call());
    }

    public function testCallbackToClassImplementingOverloadingShouldSucceed()
    {
        $handler = new CallbackHandler('foo', '\\ZendTest\\Stdlib\\SignalHandlers\\Overloadable', 'foo');
        $this->assertEquals('foo', $handler->call());
    }

    public function testClosureCallbackShouldBeInvokedByCall()
    {
        $handler = new CallbackHandler(null, function () {
            return 'foo';
        });
        $this->assertEquals('foo', $handler->call());
    }

    public function handleCall()
    {
        $this->args = func_get_args();
    }
}
