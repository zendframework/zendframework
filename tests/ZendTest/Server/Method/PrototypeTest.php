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
 * Test class for \Zend\Server\Method\Prototype
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @group      Zend_Server
 */
class PrototypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->prototype = new Method\Prototype();
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
        $parameter = new Method\Parameter(array(
            'type' => 'string',
            'name' => 'foo',
        ));
        $this->prototype->addParameter($parameter);
        $this->assertSame($parameter, $this->prototype->getParameter('foo'));
    }

    public function testPrototypeShouldAllowFetchingParameterByNameOrIndex()
    {
        $parameter = new Method\Parameter(array(
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
            $this->assertTrue($parameter instanceof Method\Parameter);
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
        $prototype = new Method\Prototype($options);
        $test = $prototype->toArray();
        $this->assertSame($options, $test);
    }
}
