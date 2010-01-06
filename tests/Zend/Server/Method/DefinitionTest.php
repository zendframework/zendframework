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
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Server_Method_DefinitionTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Server_Method_DefinitionTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Server_Method_Definition */
require_once 'Zend/Server/Method/Definition.php';

/** Zend_Server_Method_Callback */
require_once 'Zend/Server/Method/Callback.php';

/** Zend_Server_Method_Prototype */
require_once 'Zend/Server/Method/Prototype.php';

/**
 * Test class for Zend_Server_Method_Definition
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Server
 */
class Zend_Server_Method_DefinitionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Server_Method_DefinitionTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->definition = new Zend_Server_Method_Definition();
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
        $callback = new Zend_Server_Method_Callback();
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
        $object = new stdClass;
        $this->definition->setObject($object);
        $this->assertEquals($object, $this->definition->getObject());
    }

    /**
     * @expectedException Zend_Server_Exception
     */
    public function testSettingObjectToNonObjectShouldThrowException()
    {
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
        $args = array('foo', array('bar', 'baz'), new stdClass);
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
        $prototype1 = new Zend_Server_Method_Prototype;
        $this->definition->addPrototype($prototype1);
        $test = $this->definition->getPrototypes();
        $this->assertSame($prototype1, $test[0]);

        $prototype2 = new Zend_Server_Method_Prototype;
        $this->definition->addPrototype($prototype2);
        $test = $this->definition->getPrototypes();
        $this->assertSame($prototype1, $test[0]);
        $this->assertSame($prototype2, $test[1]);
    }

    public function testDefinitionShouldAllowAddingMultiplePrototypes()
    {
        $prototype1 = new Zend_Server_Method_Prototype;
        $prototype2 = new Zend_Server_Method_Prototype;
        $prototypes = array($prototype1, $prototype2);
        $this->definition->addPrototypes($prototypes);
        $this->assertSame($prototypes, $this->definition->getPrototypes());
    }

    public function testSetPrototypesShouldOverwriteExistingPrototypes()
    {
        $this->testDefinitionShouldAllowAddingMultiplePrototypes();

        $prototype1 = new Zend_Server_Method_Prototype;
        $prototype2 = new Zend_Server_Method_Prototype;
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
        $object     = new stdClass;
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
            'object'          => new stdClass,
            'invokeArguments' => array('foo', array('bar', 'baz')),
        );
        $definition = new Zend_Server_Method_Definition($options);
        $test = $definition->toArray();
        $this->assertEquals($options['name'], $test['name']);
        $this->assertEquals($options['callback'], $test['callback']);
        $this->assertEquals($options['prototypes'], $test['prototypes']);
        $this->assertEquals($options['methodHelp'], $test['methodHelp']);
        $this->assertEquals($options['object'], $test['object']);
        $this->assertEquals($options['invokeArguments'], $test['invokeArguments']);
    }
}

// Call Zend_Server_Method_DefinitionTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Server_Method_DefinitionTest::main") {
    Zend_Server_Method_DefinitionTest::main();
}
