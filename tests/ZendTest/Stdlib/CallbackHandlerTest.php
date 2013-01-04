<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\CallbackHandler;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @group      Zend_Stdlib
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
        $class   = new SignalHandlers\InstanceMethod();
        $handler = new CallbackHandler($class);
    }

    public function testCallbackConsistingOfStringContextWithNonStaticMethodShouldNotRaiseExceptionButWillRaiseEStrict()
    {
        $handler = new CallbackHandler(array('ZendTest\\Stdlib\\SignalHandlers\\InstanceMethod', 'handler'));
        $error   = false;
        set_error_handler(function ($errno, $errstr) use (&$error) {
            $error = true;
        }, E_STRICT);
        $handler->call();
        restore_error_handler();
        $this->assertTrue($error);
    }

    public function testStringCallbackConsistingOfNonStaticMethodShouldRaiseException()
    {
        $handler = new CallbackHandler('ZendTest\\Stdlib\\SignalHandlers\\InstanceMethod::handler');

        if (version_compare(PHP_VERSION, '5.4.0rc1', '>=')) {
            $this->setExpectedException('Zend\Stdlib\Exception\InvalidCallbackException');
            $handler->call();
        } else {
            $error   = false;
            set_error_handler(function ($errno, $errstr) use (&$error) {
                $error = true;
            }, E_STRICT);
            $handler->call();
            restore_error_handler();
            $this->assertTrue($error);
        }
    }

    public function testStringStaticCallbackForPhp54()
    {
        if (version_compare(PHP_VERSION, '5.4.0rc1', '<=')) {
            $this->markTestSkipped('Requires PHP 5.4');
        }

        $handler = new CallbackHandler('ZendTest\\Stdlib\\SignalHandlers\\InstanceMethod::staticHandler');
        $error   = false;
        set_error_handler(function ($errno, $errstr) use (&$error) {
            $error = true;
        }, E_STRICT);
        $result = $handler->call();
        restore_error_handler();
        $this->assertFalse($error);
        $this->assertSame('staticHandler', $result);
    }

    public function testStringStaticCallbackForPhp54WithMoreThan3Args()
    {
        if (version_compare(PHP_VERSION, '5.4.0rc1', '<=')) {
            $this->markTestSkipped('Requires PHP 5.4');
        }

        $handler = new CallbackHandler('ZendTest\\Stdlib\\SignalHandlers\\InstanceMethod::staticHandler');
        $error   = false;
        set_error_handler(function ($errno, $errstr) use (&$error) {
            $error = true;
        }, E_STRICT);
        $result = $handler->call(array(1, 2, 3, 4));
        restore_error_handler();
        $this->assertFalse($error);
        $this->assertSame('staticHandler', $result);
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

    public function testHandlerShouldBeInvocable()
    {
        $handler = new CallbackHandler(array($this, 'handleCall'));
        $handler('foo', 'bar');
        $this->assertEquals(array('foo', 'bar'), $this->args);
    }

    public function handleCall()
    {
        $this->args = func_get_args();
    }
}
