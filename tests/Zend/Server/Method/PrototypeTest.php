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

// Call Zend_Server_Method_PrototypeTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Server_Method_PrototypeTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Server_Method_Prototype */
require_once 'Zend/Server/Method/Prototype.php';

/**
 * Test class for Zend_Server_Method_Prototype
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Server
 */
class Zend_Server_Method_PrototypeTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Server_Method_PrototypeTest");
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
        $this->prototype = new Zend_Server_Method_Prototype();
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

    public function testReturnTypeShouldBeVoidByDefault()
    {
        $this->assertEquals('void', $this->prototype->getReturnType());
    }

    public function testReturnTypeShouldBeMutable()
    {
        $this->assertEquals('void', $this->prototype->getReturnType());
        $this->prototype->setReturnType('string');
        $this->assertEquals('string', $this->prototype->getReturnType());
    }

    public function testParametersShouldBeEmptyArrayByDefault()
    {
        $params = $this->prototype->getParameters();
        $this->assertTrue(is_array($params));
        $this->assertTrue(empty($params));
    }

    public function testPrototypeShouldAllowAddingSingleParameters()
    {
        $this->testParametersShouldBeEmptyArrayByDefault();
        $this->prototype->addParameter('string');
        $params = $this->prototype->getParameters();
        $this->assertTrue(is_array($params));
        $this->assertEquals(1, count($params));
        $this->assertEquals('string', $params[0]);

        $this->prototype->addParameter('array');
        $params = $this->prototype->getParameters();
        $this->assertEquals(2, count($params));
        $this->assertEquals('string', $params[0]);
        $this->assertEquals('array', $params[1]);
    }

    public function testPrototypeShouldAllowAddingParameterObjects()
    {
        $parameter = new Zend_Server_Method_Parameter(array(
            'type' => 'string',
            'name' => 'foo',
        ));
        $this->prototype->addParameter($parameter);
        $this->assertSame($parameter, $this->prototype->getParameter('foo'));
    }

    public function testPrototypeShouldAllowFetchingParameterByNameOrIndex()
    {
        $parameter = new Zend_Server_Method_Parameter(array(
            'type' => 'string',
            'name' => 'foo',
        ));
        $this->prototype->addParameter($parameter);
        $test1 = $this->prototype->getParameter('foo');
        $test2 = $this->prototype->getParameter(0);
        $this->assertSame($test1, $test2);
        $this->assertSame($parameter, $test1);
        $this->assertSame($parameter, $test2);
    }

    public function testPrototypeShouldAllowRetrievingParameterObjects()
    {
        $this->prototype->addParameters(array('string', 'array'));
        $parameters = $this->prototype->getParameterObjects();
        foreach ($parameters as $parameter) {
            $this->assertTrue($parameter instanceof Zend_Server_Method_Parameter);
        }
    }

    public function testPrototypeShouldAllowAddingMultipleParameters()
    {
        $this->testParametersShouldBeEmptyArrayByDefault();
        $params = array(
            'string',
            'array',
        );
        $this->prototype->addParameters($params);
        $test = $this->prototype->getParameters();
        $this->assertSame($params, $test);
    }

    public function testSetParametersShouldOverwriteParameters()
    {
        $this->testPrototypeShouldAllowAddingMultipleParameters();
        $params = array(
            'bool',
            'base64',
            'struct',
        );
        $this->prototype->setParameters($params);
        $test = $this->prototype->getParameters();
        $this->assertSame($params, $test);
    }

    public function testPrototypeShouldSerializeToArray()
    {
        $return = 'string';
        $params = array(
            'bool',
            'base64',
            'struct',
        );
        $this->prototype->setReturnType($return)
                        ->setParameters($params);
        $test = $this->prototype->toArray();
        $this->assertEquals($return, $test['returnType']);
        $this->assertEquals($params, $test['parameters']);
    }

    public function testConstructorShouldSetObjectStateFromOptions()
    {
        $options = array(
            'returnType' => 'string',
            'parameters' => array(
                'bool',
                'base64',
                'struct',
            ),
        );
        $prototype = new Zend_Server_Method_Prototype($options);
        $test = $prototype->toArray();
        $this->assertSame($options, $test);
    }
}

// Call Zend_Server_Method_PrototypeTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Server_Method_PrototypeTest::main") {
    Zend_Server_Method_PrototypeTest::main();
}
