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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Server_DefinitionTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Server_DefinitionTest::main");
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

/** Zend_Server_Definition */
require_once 'Zend/Server/Definition.php';

/** Zend_Server_Method_Definition */
require_once 'Zend/Server/Method/Definition.php';

/** Zend_Server_Method_Callback */
require_once 'Zend/Server/Method/Callback.php';

/** Zend_Server_Method_Prototype */
require_once 'Zend/Server/Method/Prototype.php';

/**
 * Test class for Zend_Server_Definition
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Server
 */
class Zend_Server_DefinitionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Server_DefinitionTest");
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
        $this->definition = new Zend_Server_Definition();
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
        $method = new Zend_Server_Method_Definition(array('name' => 'foo'));
        $this->definition->addMethod($method);
        $methods = $this->definition->getMethods();
        $this->assertEquals(1, count($methods));
        $this->assertSame($method, $methods['foo']);
        $this->assertSame($method, $this->definition->getMethod('foo'));
    }

    public function testDefinitionShouldAllowAddingMultipleMethods()
    {
        $method1 = new Zend_Server_Method_Definition(array('name' => 'foo'));
        $method2 = new Zend_Server_Method_Definition(array('name' => 'bar'));
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
        $method1 = new Zend_Server_Method_Definition(array('name' => 'foo'));
        $method2 = new Zend_Server_Method_Definition(array('name' => 'bar'));
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
        $definition = new Zend_Server_Definition();
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
        $definition = new Zend_Server_Definition($options);
        $test = $definition->toArray();
        $this->assertEquals(1, count($test));
        $test = array_shift($test);
        $this->assertEquals($method['name'], $test['name']);
        $this->assertEquals($method['methodHelp'], $test['methodHelp']);
        $this->assertEquals($method['invokeArguments'], $test['invokeArguments']);
        $this->assertEquals($method['prototypes'][0]['returnType'], $test['prototypes'][0]['returnType']);
    }
}

// Call Zend_Server_DefinitionTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Server_DefinitionTest::main") {
    Zend_Server_DefinitionTest::main();
}
