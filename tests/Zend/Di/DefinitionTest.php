<?php
namespace ZendTest\Di;

use Zend\Di\Definition,
    Zend\Di\InjectibleMethod,
    Zend\Di\Method;

use PHPUnit_Framework_TestCase as TestCase;

class DefinitionTest extends TestCase
{
    public function setUp()
    {
        $this->definition = new Definition(__CLASS__);
    }

    /**
     * Stub for testing
     */
    public function foo()
    {
    }

    /**
     * Stub for testing
     */
    public function bar($message)
    {
    }

    public function testCanRetrieveConfiguredClassName()
    {
        $this->assertEquals(__CLASS__, $this->definition->getClass());
    }

    public function testClassNameIsMutable()
    {
        $this->definition->setClass('foo');
        $this->assertEquals('foo', $this->definition->getClass());
    }

    public function testParamsAreEmptyByDefault()
    {
        foreach ($this->definition->getParams() as $param) {
            $this->assertNull($param);
        }
    }

    public function testParamsAreReturnedInConstructorOrderWhenNoParamMapProvided()
    {
        $def = new Definition('ZendTest\Di\TestAsset\InspectedClass');
        $def->setParam('baz', 'BAZ');
        $def->setParam('foo', 'FOO');
        $expected = array(
            'FOO',
            'BAZ',
        );
        $this->assertEquals($expected, $def->getParams());
    }

    public function testParamsAreReturnedInParamMapOrderIfSpecified()
    {
        $def = new Definition('ZendTest\Di\TestAsset\InspectedClass');
        $def->setParam('baz', 'BAZ');
        $def->setParam('foo', 'FOO');
        $def->setParamMap(array(
            'baz' => 0,
            'foo' => 1,
        ));
        $expected = array(
            'BAZ',
            'FOO',
        );
        $this->assertEquals($expected, $def->getParams());
    }

    public function testSpecifyingAParamMultipleTimesOverwrites()
    {
        $def = new Definition('ZendTest\Di\TestAsset\InspectedClass');
        $def->setParam('baz', 'BAZ');
        $def->setParam('foo', 'FOO');
        $def->setParam('baz', 'baz');
        $def->setParam('foo', 'foo');
        $def->setParamMap(array(
            'baz' => 0,
            'foo' => 1,
        ));
        $expected = array(
            'baz',
            'foo',
        );
        $this->assertEquals($expected, $def->getParams());
    }

    public function testCanSpecifyManyParamsAtOnce()
    {
        $params = array(
            'foo' => 'FOO',
            'bar' => 'BAR',
        );
        $map = array('foo' => 0, 'bar' => 1);
        $this->definition->setParams($params)
                         ->setParamMap($map);
        $this->assertEquals(array_values($params), $this->definition->getParams());
    }

    public function testSettingParamMapWithNonNumericPositionsRaisesException()
    {
        $this->setExpectedException('Zend\Di\Exception\InvalidPositionException');
        $this->definition->setParamMap(array(
            'foo' => 0,
            'bar' => 'bar',
            'baz' => 2,
        ));
    }

    public function testSettingParamMapWithNonStringNameRaisesException()
    {
        $this->setExpectedException('Zend\Di\Exception\InvalidParamNameException');
        $this->definition->setParamMap(array(
            'foo' => 0,
            1     => 1,
            'baz' => 2,
        ));
    }

    public function testSettingParamMapWithInvalidPositionsRaisesException()
    {
        $this->setExpectedException('Zend\Di\Exception\InvalidPositionException', 'non-sequential');
        $this->definition->setParamMap(array(
            'foo' => 0,
            'bar' => 3,
            'baz' => 2,
        ));
    }

    public function testSharedByDefault()
    {
        $this->assertTrue($this->definition->isShared());
    }

    public function testCanOverrideSharedFlag()
    {
        $this->definition->setShared(false);
        $this->assertFalse($this->definition->isShared());
    }

    /**
     * @group fml
     */
    public function testAddingMethodCallsAggregates()
    {
        $this->definition->addMethodCall('foo', array());
        $this->definition->addMethodCall('bar', array('bar'));
        $methods = $this->definition->getMethodCalls();
        $this->assertInstanceOf('Zend\Di\InjectibleMethods', $methods);
        foreach ($methods as $name => $method) {
            switch ($name) {
                case 'foo':
                    $this->assertSame(array(), $method->getParams());
                    break;
                case 'bar':
                    $this->assertSame(array('bar'), $method->getParams());
                    break;
                default:
                    $this->fail('Unexpected method encountered');
            }
        }
    }

    public function testCanPassInjectibleMethodObjectToAddMethodCall()
    {
        $definition = new Definition('Foo');
        $method     = new Method('bar', array());
        $definition->addMethodCall($method);
        $this->assertEquals('Foo', $method->getClass());
    }

    public function testCanPassParameterMapWhenCallingAddMethodCallWithStringMethod()
    {
        $definition = new Definition('Foo');
        $definition->addMethodCall('bar', array('message' => 'BAR'), array('message' => 0));
        foreach ($definition->getMethodCalls() as $method) {
            $this->assertEquals(array('message' => 0), $method->getParamMap());
        }
    }

    public function testCanPassParametersAndParameterMapWhenCallingAddMethodCallWithInjectibleMethod()
    {
        $definition = new Definition('Foo');
        $method     = new Method('bar', array());
        $definition->addMethodCall($method, array('message' => 'BAR'), array('message' => 0));
        $this->assertEquals(array('message' => 0), $method->getParamMap());
        $this->assertEquals(array('BAR'), $method->getParams());
    }

    public function testCanSerializeDefinitionToArray()
    {
        $definition = new Definition('Foo');
        $definition->setParams(array(
                        'name'   => 'foo',
                        'class'  => 'Foo',
                        'object' => array('__reference' => 'Baz'),
                   ))
                   ->setParamMap(array(
                       'name'   => 1,
                       'class'  => 0,
                       'object' => 2,
                   ))
                   ->addMethodCall('bar', array('one', 'two'))
                   ->addMethodCall('baz', array(array('__reference' => 'Bar')));
        $expected = array(
            'class'   => 'Foo',
            'methods' => array(
                array('name' => 'bar', 'params' => array('one', 'two')),
                array('name' => 'baz', 'params' => array(array('__reference' => 'Bar'))),
            ),
            'param_map' => array(
                'name'   => 1,
                'class'  => 0,
                'object' => 2,
            ),
            'params'    => array(
                'Foo',
                'foo',
                array('__reference' => 'Baz'),
            ),
        );
        $this->assertEquals($expected, $definition->toArray());
    }

    public function testNoConstructorCallbackByDefault()
    {
        $this->assertFalse($this->definition->hasConstructorCallback());
    }

    public function testReturnsTrueForHasConstructorCallbackWhenOneProvided()
    {
        $callback = function () {};
        $this->definition->setConstructorCallback($callback);
        $this->assertTrue($this->definition->hasConstructorCallback());
    }

    public function testCanSetConstructorCallback()
    {
        $callback = function () {};
        $this->definition->setConstructorCallback($callback);
        $this->assertSame($callback, $this->definition->getConstructorCallback());
    }
}
