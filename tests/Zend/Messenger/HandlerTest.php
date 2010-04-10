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

namespace ZendTest\Messenger;
use Zend\Messenger\Handler as Handler;

/**
 * @category   Phly
 * @package    Phly_PubSub
 * @subpackage Test
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 */
class HandlerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (isset($this->args)) {
            unset($this->args);
        }
    }

    public function testGetTopicShouldReturnTopic()
    {
        $handle = new Handler('foo', 'rand');
        $this->assertEquals('foo', $handle->getTopic());
    }

    public function testCallbackShouldBeStringIfNoHandlerPassedToConstructor()
    {
        $handle = new Handler('foo', 'rand');
        $this->assertSame('rand', $handle->getCallback());
    }

    public function testCallbackShouldBeArrayIfHandlerPassedToConstructor()
    {
        $handle = new Handler('foo', '\\ZendTest\\Messenger\\Handlers\\ObjectCallback', 'test');
        $this->assertSame(array('\\ZendTest\\Messenger\\Handlers\\ObjectCallback', 'test'), $handle->getCallback());
    }

    public function testCallShouldInvokeCallbackWithSuppliedArguments()
    {
        $handle = new Handler('foo', $this, 'handleCall');
        $args   = array('foo', 'bar', 'baz');
        $handle->call($args);
        $this->assertSame($args, $this->args);
    }

    /**
     * @expectedException \Zend\Messenger\InvalidCallbackException
     */
    public function testPassingInvalidCallbackShouldRaiseInvalidCallbackExceptionDuringCall()
    {
        $handle = new Handler('Invokable', 'boguscallback');
        $handle->call();
    }

    public function testCallShouldReturnTheReturnValueOfTheCallback()
    {
        $handle = new Handler('foo', '\\ZendTest\\Messenger\\Handlers\\ObjectCallback', 'test');
        if (!is_callable(array('\\ZendTest\\Messenger\\Handlers\\ObjectCallback', 'test'))) {
            echo "\nClass exists? " . var_export(class_exists('\\ZendTest\\Messenger\\Handlers\\ObjectCallback'), 1) . "\n";
            echo "Include path: " . get_include_path() . "\n";
        }
        $this->assertEquals('bar', $handle->call(array()));
    }

    public function testStringCallbackResolvingToClassNameShouldCallViaInvoke()
    {
        $handle = new Handler('foo', '\\ZendTest\\Messenger\\Handlers\\Invokable');
        $this->assertEquals('__invoke', $handle->call(), var_export($handle->getCallback(), 1));
    }

    /**
     * @expectedException \Zend\Messenger\InvalidCallbackException
     */
    public function testStringCallbackReferringToClassWithoutDefinedInvokeShouldRaiseException()
    {
        $handle = new Handler('foo', '\\ZendTest\\Messenger\\Handlers\\InstanceMethod');
        $handle->call();
    }

    public function testCallbackConsistingOfStringContextWithNonStaticMethodShouldInstantiateContext()
    {
        $handle = new Handler('foo', 'ZendTest\\Messenger\\Handlers\\InstanceMethod', 'callable');
        $this->assertEquals('callable', $handle->call());
    }

    public function testCallbackToClassImplementingOverloadingShouldSucceed()
    {
        $handle = new Handler('foo', '\\ZendTest\\Messenger\\Handlers\\Overloadable', 'foo');
        $this->assertEquals('foo', $handle->call());
    }

    public function handleCall()
    {
        $this->args = func_get_args();
    }
}
