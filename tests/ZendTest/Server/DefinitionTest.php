<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Server
 */

namespace ZendTest\Server;

use Zend\Server;
use Zend\Server\Method;

/**
 * Test class for Zend\Server\Definition
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
        $this->definition = new Server\Definition();
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

    public function testMethodsShouldBeEmptyArrayByDefault()
    {
        $methods = $this->definition->getMethods();
        $this->assertTrue(is_array($methods));
        $this->assertTrue(empty($methods));
    }

    public function testDefinitionShouldAllowAddingSingleMethods()
    {
        $method = new Method\Definition(array('name' => 'foo'));
        $this->definition->addMethod($method);
        $methods = $this->definition->getMethods();
        $this->assertEquals(1, count($methods));
        $this->assertSame($method, $methods['foo']);
        $this->assertSame($method, $this->definition->getMethod('foo'));
    }

    public function testDefinitionShouldAllowAddingMultipleMethods()
    {
        $method1 = new Method\Definition(array('name' => 'foo'));
        $method2 = new Method\Definition(array('name' => 'bar'));
        $this->definition->addMethods(array($method1, $method2));
        $methods = $this->definition->getMethods();
        $this->assertEquals(2, count($methods));
        $this->assertSame($method1, $methods['foo']);
        $this->assertSame($method1, $this->definition->getMethod('foo'));
        $this->assertSame($method2, $methods['bar']);
        $this->assertSame($method2, $this->definition->getMethod('bar'));
    }

    public function testSetMethodsShouldOverwriteExistingMethods()
    {
        $this->testDefinitionShouldAllowAddingMultipleMethods();
        $method1 = new Method\Definition(array('name' => 'foo'));
        $method2 = new Method\Definition(array('name' => 'bar'));
        $methods = array($method1, $method2);
        $this->assertNotEquals($methods, $this->definition->getMethods());
        $this->definition->setMethods($methods);
        $test = $this->definition->getMethods();
        $this->assertEquals(array_values($methods), array_values($test));
    }

    public function testHasMethodShouldReturnFalseWhenMethodNotRegisteredWithDefinition()
    {
        $this->assertFalse($this->definition->hasMethod('foo'));
    }

    public function testHasMethodShouldReturnTrueWhenMethodRegisteredWithDefinition()
    {
        $this->testDefinitionShouldAllowAddingMultipleMethods();
        $this->assertTrue($this->definition->hasMethod('foo'));
    }

    public function testDefinitionShouldAllowRemovingIndividualMethods()
    {
        $this->testDefinitionShouldAllowAddingMultipleMethods();
        $this->assertTrue($this->definition->hasMethod('foo'));
        $this->definition->removeMethod('foo');
        $this->assertFalse($this->definition->hasMethod('foo'));
    }

    public function testDefinitionShouldAllowClearingAllMethods()
    {
        $this->testDefinitionShouldAllowAddingMultipleMethods();
        $this->definition->clearMethods();
        $test = $this->definition->getMethods();
        $this->assertTrue(empty($test));
    }

    public function testDefinitionShouldSerializeToArray()
    {
        $method = array(
            'name' => 'foo.bar',
            'callback' => array(
                'type'     => 'function',
                'function' => 'bar',
            ),
            'prototypes' => array(
                array(
                    'returnType' => 'string',
                    'parameters' => array('string'),
                ),
            ),
            'methodHelp' => 'Foo Bar!',
            'invokeArguments' => array('foo'),
        );
        $definition = new Server\Definition();
        $definition->addMethod($method);
        $test = $definition->toArray();
        $this->assertEquals(1, count($test));
        $test = array_shift($test);
        $this->assertEquals($method['name'], $test['name']);
        $this->assertEquals($method['methodHelp'], $test['methodHelp']);
        $this->assertEquals($method['invokeArguments'], $test['invokeArguments']);
        $this->assertEquals($method['prototypes'][0]['returnType'], $test['prototypes'][0]['returnType']);
    }

    public function testPassingOptionsToConstructorShouldSetObjectState()
    {
        $method = array(
            'name' => 'foo.bar',
            'callback' => array(
                'type'     => 'function',
                'function' => 'bar',
            ),
            'prototypes' => array(
                array(
                    'returnType' => 'string',
                    'parameters' => array('string'),
                ),
            ),
            'methodHelp' => 'Foo Bar!',
            'invokeArguments' => array('foo'),
        );
        $options = array($method);
        $definition = new Server\Definition($options);
        $test = $definition->toArray();
        $this->assertEquals(1, count($test));
        $test = array_shift($test);
        $this->assertEquals($method['name'], $test['name']);
        $this->assertEquals($method['methodHelp'], $test['methodHelp']);
        $this->assertEquals($method['invokeArguments'], $test['invokeArguments']);
        $this->assertEquals($method['prototypes'][0]['returnType'], $test['prototypes'][0]['returnType']);
    }
}
