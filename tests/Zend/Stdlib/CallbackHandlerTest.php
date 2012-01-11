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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\CallbackHandler;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @group      Zend_Stdlib
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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

    public function testCallbackShouldStoreMetadata()
    {
        $handler = new CallbackHandler('rand', array('event' => 'foo'));
        $this->assertEquals('foo', $handler->getMetadatum('event'));
        $this->assertEquals(array('event' => 'foo'), $handler->getMetadata());
    }

    public function testCallbackShouldBeStringIfNoHandlerPassedToConstructor()
    {
        $handler = new CallbackHandler('rand');
        $this->assertSame('rand', $handler->getCallback());
    }

    public function testCallbackShouldBeArrayIfHandlerPassedToConstructor()
    {
        $handler = new CallbackHandler(array('ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback', 'test'));
        $this->assertSame(array('ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback', 'test'), $handler->getCallback());
    }

    public function testCallShouldInvokeCallbackWithSuppliedArguments()
    {
        $handler = new CallbackHandler(array( $this, 'handleCall' ));
        $args   = array('foo', 'bar', 'baz');
        $handler->call($args);
        $this->assertSame($args, $this->args);
    }

    public function testPassingInvalidCallbackShouldRaiseInvalidCallbackExceptionDuringInstantiation()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidCallbackException');
        $handler = new CallbackHandler('boguscallback');
    }

    public function testCallShouldReturnTheReturnValueOfTheCallback()
    {
        $handler = new CallbackHandler(array('ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback', 'test'));
        if (!is_callable(array('ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback', 'test'))) {
            echo "\nClass exists? " . var_export(class_exists('ZendTest\\Stdlib\\SignalHandlers\\ObjectCallback'), 1) . "\n";
            echo "Include path: " . get_include_path() . "\n";
        }
        $this->assertEquals('bar', $handler->call(array()));
    }

    public function testStringCallbackResolvingToClassDefiningInvokeNameShouldRaiseException()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidCallbackException');
        $handler = new CallbackHandler('ZendTest\\Stdlib\\SignalHandlers\\Invokable');
    }

    public function testStringCallbackReferringToClassWithoutDefinedInvokeShouldRaiseException()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidCallbackException');
        $handler = new CallbackHandler('ZendTest\\Stdlib\\SignalHandlers\\InstanceMethod');
    }

    public function testCallbackConsistingOfStringContextWithNonStaticMethodShouldRaiseException()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidCallbackException');
        $handler = new CallbackHandler(array('ZendTest\\Stdlib\\SignalHandlers\\InstanceMethod', 'handler'));
        $handler->call();
    }

    public function testStringCallbackConsistingOfNonStaticMethodShouldRaiseException()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidCallbackException');
        $handler = new CallbackHandler('ZendTest\\Stdlib\\SignalHandlers\\InstanceMethod::handler');
        $handler->call();
    }

    public function testCallbackToClassImplementingOverloadingButNotInvocableShouldRaiseException()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidCallbackException');
        $handler = new CallbackHandler('foo', array( 'ZendTest\\Stdlib\\SignalHandlers\\Overloadable', 'foo' ));
    }

    public function testClosureCallbackShouldBeInvokedByCall()
    {
        $handler = new CallbackHandler(function () {
            return 'foo';
        });
        $this->assertEquals('foo', $handler->call());
    }

    public function handleCall()
    {
        $this->args = func_get_args();
    }
}
