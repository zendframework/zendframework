<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Server
 */

namespace ZendTest\Server\Method;

use Zend\Server\Method;

/**
 * Test class for \Zend\Server\Method\Definition
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @group      Zend_Server
 */
class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->definition = new Method\Definition();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testCallbackShouldBeNullByDefault()
    {
        $this->assertNull($this->definition->getCallback());
    }

    public function testSetCallbackShouldAcceptMethodCallback()
    {
        $callback = new Method\Callback();
        $this->definition->setCallback($callback);
        $test = $this->definition->getCallback();
        $this->assertSame($callback, $test);
    }

    public function testSetCallbackShouldAcceptArray()
    {
        $callback = array(
            'type'     => 'function',
            'function' => 'foo',
        );
        $this->definition->setCallback($callback);
        $test = $this->definition->getCallback()->toArray();
        $this->assertSame($callback, $test);
    }

    public function testMethodHelpShouldBeEmptyStringByDefault()
    {
        $this->assertEquals('', $this->definition->getMethodHelp());
    }

    public function testMethodHelpShouldBeMutable()
    {
        $this->assertEquals('', $this->definition->getMethodHelp());
        $this->definition->setMethodHelp('foo bar');
        $this->assertEquals('foo bar', $this->definition->getMethodHelp());
    }

    public function testNameShouldBeNullByDefault()
    {
        $this->assertNull($this->definition->getName());
    }

    public function testNameShouldBeMutable()
    {
        $this->assertNull($this->definition->getName());
        $this->definition->setName('foo.bar');
        $this->assertEquals('foo.bar', $this->definition->getName());
    }

    public function testObjectShouldBeNullByDefault()
    {
        $this->assertNull($this->definition->getObject());
    }

    public function testObjectShouldBeMutable()
    {
        $this->assertNull($this->definition->getObject());
        $object = new \stdClass;
        $this->definition->setObject($object);
        $this->assertEquals($object, $this->definition->getObject());
    }

    public function testSettingObjectToNonObjectShouldThrowException()
    {
        $this->setExpectedException('Zend\Server\Exception\InvalidArgumentException', 'Invalid object passed to');
        $this->definition->setObject('foo');
    }

    public function testInvokeArgumentsShouldBeEmptyArrayByDefault()
    {
        $args = $this->definition->getInvokeArguments();
        $this->assertTrue(is_array($args));
        $this->assertTrue(empty($args));
    }

    public function testInvokeArgumentsShouldBeMutable()
    {
        $this->testInvokeArgumentsShouldBeEmptyArrayByDefault();
        $args = array('foo', array('bar', 'baz'), new \stdClass);
        $this->definition->setInvokeArguments($args);
        $this->assertSame($args, $this->definition->getInvokeArguments());
    }

    public function testPrototypesShouldBeEmptyArrayByDefault()
    {
        $prototypes = $this->definition->getPrototypes();
        $this->assertTrue(is_array($prototypes));
        $this->assertTrue(empty($prototypes));
    }

    public function testDefinitionShouldAllowAddingSinglePrototypes()
    {
        $this->testPrototypesShouldBeEmptyArrayByDefault();
        $prototype1 = new Method\Prototype;
        $this->definition->addPrototype($prototype1);
        $test = $this->definition->getPrototypes();
        $this->assertSame($prototype1, $test[0]);

        $prototype2 = new Method\Prototype;
        $this->definition->addPrototype($prototype2);
        $test = $this->definition->getPrototypes();
        $this->assertSame($prototype1, $test[0]);
        $this->assertSame($prototype2, $test[1]);
    }

    public function testDefinitionShouldAllowAddingMultiplePrototypes()
    {
        $prototype1 = new Method\Prototype;
        $prototype2 = new Method\Prototype;
        $prototypes = array($prototype1, $prototype2);
        $this->definition->addPrototypes($prototypes);
        $this->assertSame($prototypes, $this->definition->getPrototypes());
    }

    public function testSetPrototypesShouldOverwriteExistingPrototypes()
    {
        $this->testDefinitionShouldAllowAddingMultiplePrototypes();

        $prototype1 = new Method\Prototype;
        $prototype2 = new Method\Prototype;
        $prototypes = array($prototype1, $prototype2);
        $this->assertNotSame($prototypes, $this->definition->getPrototypes());
        $this->definition->setPrototypes($prototypes);
        $this->assertSame($prototypes, $this->definition->getPrototypes());
    }

    public function testDefintionShouldSerializeToArray()
    {
        $name       = 'foo.bar';
        $callback   = array('function' => 'foo', 'type' => 'function');
        $prototypes = array(array('returnType' => 'struct', 'parameters' => array('string', 'array')));
        $methodHelp = 'foo bar';
        $object     = new \stdClass;
        $invokeArgs = array('foo', array('bar', 'baz'));
        $this->definition->setName($name)
                         ->setCallback($callback)
                         ->setPrototypes($prototypes)
                         ->setMethodHelp($methodHelp)
                         ->setObject($object)
                         ->setInvokeArguments($invokeArgs);
        $test = $this->definition->toArray();
        $this->assertEquals($name, $test['name']);
        $this->assertEquals($callback, $test['callback']);
        $this->assertEquals($prototypes, $test['prototypes']);
        $this->assertEquals($methodHelp, $test['methodHelp']);
        $this->assertEquals($object, $test['object']);
        $this->assertEquals($invokeArgs, $test['invokeArguments']);
    }

    public function testPassingOptionsToConstructorShouldSetObjectState()
    {
        $options = array(
            'name'            => 'foo.bar',
            'callback'        => array('function' => 'foo', 'type' => 'function'),
            'prototypes'      => array(array('returnType' => 'struct', 'parameters' => array('string', 'array'))),
            'methodHelp'      => 'foo bar',
            'object'          => new \stdClass,
            'invokeArguments' => array('foo', array('bar', 'baz')),
        );
        $definition = new Method\Definition($options);
        $test = $definition->toArray();
        $this->assertEquals($options['name'], $test['name']);
        $this->assertEquals($options['callback'], $test['callback']);
        $this->assertEquals($options['prototypes'], $test['prototypes']);
        $this->assertEquals($options['methodHelp'], $test['methodHelp']);
        $this->assertEquals($options['object'], $test['object']);
        $this->assertEquals($options['invokeArguments'], $test['invokeArguments']);
    }
}
