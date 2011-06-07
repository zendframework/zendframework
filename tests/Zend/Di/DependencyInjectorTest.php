<?php
namespace ZendTest\Di;

use Zend\Di\DependencyInjector,
    Zend\Di\Definition,
    Zend\Di\Reference;

use PHPUnit_Framework_TestCase as TestCase;

class DependencyInjectorTest extends TestCase
{
    public function setUp()
    {
        $this->di = new DependencyInjector;
    }

    public function testPassingInvalidDefinitionRaisesException()
    {
        $definitions = array('foo');
        $this->setExpectedException('PHPUnit_Framework_Error');
        $this->di->setDefinitions($definitions);
    }

    public function testGetRetrievesObjectWithMatchingClassDefinition()
    {
        $def = new Definition('ZendTest\Di\TestAsset\Struct');
        $def->setParam('param1', 'foo')
            ->setParam('param2', 'bar');
        $this->di->setDefinition($def);
        $test = $this->di->get('ZendTest\Di\TestAsset\Struct');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $test);
        $this->assertEquals('foo', $test->param1);
        $this->assertEquals('bar', $test->param2);
    }

    public function testGetRetrievesSameInstanceOnSubsequentCalls()
    {
        $def = new Definition('ZendTest\Di\TestAsset\Struct');
        $def->setParam('param1', 'foo')
            ->setParam('param2', 'bar');
        $this->di->setDefinition($def);
        $first  = $this->di->get('ZendTest\Di\TestAsset\Struct');
        $second = $this->di->get('ZendTest\Di\TestAsset\Struct');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $first);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $second);
        $this->assertSame($first, $second);
    }

    public function testGetCanRetrieveByProvidedServiceName()
    {
        $def = new Definition('ZendTest\Di\TestAsset\Struct');
        $def->setParam('param1', 'foo')
            ->setParam('param2', 'bar');
        $this->di->setDefinition($def, 'struct');
        $test = $this->di->get('struct');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $test);
        $this->assertEquals('foo', $test->param1);
        $this->assertEquals('bar', $test->param2);
    }

    public function testGetCanRetrieveByClassNameWhenServiceNameIsAlsoProvided()
    {
        $def = new Definition('ZendTest\Di\TestAsset\Struct');
        $def->setParam('param1', 'foo')
            ->setParam('param2', 'bar');
        $this->di->setDefinition($def, 'struct');
        $test = $this->di->get('ZendTest\Di\TestAsset\Struct');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $test);
        $this->assertEquals('foo', $test->param1);
        $this->assertEquals('bar', $test->param2);
    }

    public function testGetReturnsNewInstanceIfDefinitionSharedFlagIsFalse()
    {
        $def = new Definition('ZendTest\Di\TestAsset\Struct');
        $def->setParam('param1', 'foo')
            ->setParam('param2', 'bar')
            ->setShared(false);
        $this->di->setDefinition($def);
        $first  = $this->di->get('ZendTest\Di\TestAsset\Struct');
        $second = $this->di->get('ZendTest\Di\TestAsset\Struct');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $first);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $second);
        $this->assertNotSame($first, $second);
    }

    public function testNewInstanceForcesNewObjectInstanceEvenWhenSharedFlagIsTrue()
    {
        $def = new Definition('ZendTest\Di\TestAsset\Struct');
        $def->setParam('param1', 'foo')
            ->setParam('param2', 'bar')
            ->setShared(true);
        $this->di->setDefinition($def);
        $first  = $this->di->get('ZendTest\Di\TestAsset\Struct');
        $second = $this->di->newInstance('ZendTest\Di\TestAsset\Struct');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $first);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $second);
        $this->assertNotSame($first, $second);
    }

    public function testGetNewInstanceByServiceName()
    {
        $def = new Definition('ZendTest\Di\TestAsset\Struct');
        $def->setParam('param1', 'foo')
            ->setParam('param2', 'bar');
        $this->di->setDefinition($def, 'struct');
        $test = $this->di->newInstance('struct');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $test);
    }

    public function testGetNewInstanceByAlias()
    {
        $def = new Definition('ZendTest\Di\TestAsset\Struct');
        $def->setParam('param1', 'foo')
            ->setParam('param2', 'bar');
        $this->di->setDefinition($def);
        $this->di->setAlias('struct', 'ZendTest\Di\TestAsset\Struct');
        
        $test = $this->di->newInstance('struct');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $test);
    }

    public function testCanAliasToServiceName()
    {
        $def = new Definition('ZendTest\Di\TestAsset\Struct');
        $def->setParam('param1', 'foo')
            ->setParam('param2', 'bar');
        $this->di->setDefinition($def, 'struct');
        $this->di->setAlias('mystruct', 'struct');
        
        $test = $this->di->newInstance('mystruct');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $test);
    }

    public function testCanApplyMultipleAliasesPerDefinition()
    {
        $def = new Definition('ZendTest\Di\TestAsset\Struct');
        $def->setParam('param1', 'foo')
            ->setParam('param2', 'bar');
        $this->di->setDefinition($def);
        $this->di->setAlias('mystruct', 'ZendTest\Di\TestAsset\Struct');
        $this->di->setAlias('struct', 'ZendTest\Di\TestAsset\Struct');
        
        $test1 = $this->di->newInstance('struct');
        $test2 = $this->di->newInstance('mystruct');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $test1);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $test2);
        $this->assertSame($test1, $test2);
    }

    public function testGetReturnsNullIfNoMatchingClassOrDefinitionFound()
    {
        $classes = get_declared_classes();
        $class   = array_pop($classes) . uniqid();
        while (in_array($class, $classes)) {
            $class .= uniqid();
        }

        $this->assertNull($this->di->get($class));
    }

    public function testNewInstanceReturnsNullIfNoMatchingClassOrDefinitionFound()
    {
        $classes = get_declared_classes();
        $class   = array_pop($classes) . uniqid();
        while (in_array($class, $classes)) {
            $class .= uniqid();
        }

        $this->assertNull($this->di->newInstance($class));
    }

    public function testUnmatchedReferenceInDefinitionParametersResultsInNullInjection()
    {
        $struct   = new Definition('ZendTest\Di\TestAsset\Struct');
        $struct->setParam('param1', 'foo')
               ->setParam('param2', new Reference('voodoo'));
        $this->di->setDefinition($struct);
        $test = $this->di->get('ZendTest\Di\TestAsset\Struct');
        $this->assertNull($test->param2);
    }

    public function testReferenceInDefinitionParametersCausesInjection()
    {
        $composed = new Definition('ZendTest\Di\TestAsset\ComposedClass');
        $struct   = new Definition('ZendTest\Di\TestAsset\Struct');
        $struct->setParam('param1', 'foo')
               ->setParam('param2', new Reference('ZendTest\Di\TestAsset\ComposedClass'));
        $this->di->setDefinition($composed)
                 ->setDefinition($struct);

        $diStruct  = $this->di->get('ZendTest\Di\TestAsset\Struct');
        $diCompose = $this->di->get('ZendTest\Di\TestAsset\ComposedClass');
        $this->assertSame($diCompose, $diStruct->param2);
    }

    public function testReferenceToServiceNameInDefinitionParametersCausesInjection()
    {
        $composed = new Definition('ZendTest\Di\TestAsset\ComposedClass');
        $struct   = new Definition('ZendTest\Di\TestAsset\Struct');
        $struct->setParam('param1', 'foo')
               ->setParam('param2', new Reference('composed'));
        $this->di->setDefinition($composed, 'composed')
                 ->setDefinition($struct);

        $diStruct  = $this->di->get('ZendTest\Di\TestAsset\Struct');
        $diCompose = $this->di->get('ZendTest\Di\TestAsset\ComposedClass');
        $this->assertSame($diCompose, $diStruct->param2);
    }

    public function testCanInjectNestedItems()
    {
        $inspect  = new Definition('ZendTest\Di\TestAsset\InspectedClass');
        $inspect->setParam('foo', new Reference('composed'))
                ->setParam('baz', 'BAZ');
        $composed = new Definition('ZendTest\Di\TestAsset\ComposedClass');
        $struct   = new Definition('ZendTest\Di\TestAsset\Struct');
        $struct->setParam('param1', 'foo')
               ->setParam('param2', new Reference('inspect'));
        $this->di->setDefinition($composed, 'composed')
                 ->setDefinition($inspect, 'inspect')
                 ->setDefinition($struct, 'struct');

        $diStruct  = $this->di->get('struct');
        $diInspect = $this->di->get('inspect');
        $diCompose = $this->di->get('composed');
        $this->assertSame($diCompose, $diInspect->foo);
        $this->assertSame($diInspect, $diStruct->param2);
    }

    public function testLastDefinitionOfSameClassNameWins()
    {
        $struct1 = new Definition('ZendTest\Di\TestAsset\Struct');
        $struct1->setParam('param1', 'foo')
                ->setParam('param2', 'bar');
        $struct2 = new Definition('ZendTest\Di\TestAsset\Struct');
        $struct2->setParam('param1', 'FOO')
                ->setParam('param2', 'BAR');
        $this->di->setDefinition($struct1)
                 ->setDefinition($struct2);
        $test = $this->di->get('ZendTest\Di\TestAsset\Struct');
        $this->assertEquals('FOO', $test->param1);
        $this->assertEquals('BAR', $test->param2);
    }

    public function testLastDefinitionOfSameClassNameWinsEvenWhenAddedWithDifferentServiceNames()
    {
        $struct1 = new Definition('ZendTest\Di\TestAsset\Struct');
        $struct1->setParam('param1', 'foo')
                ->setParam('param2', 'bar');
        $struct2 = new Definition('ZendTest\Di\TestAsset\Struct');
        $struct2->setParam('param1', 'FOO')
                ->setParam('param2', 'BAR');
        $this->di->setDefinition($struct1, 'struct1')
                 ->setDefinition($struct2, 'struct2');
        $test = $this->di->get('struct1');
        $this->assertEquals('FOO', $test->param1);
        $this->assertEquals('BAR', $test->param2);
    }

    public function testCanInjectSpecificMethods()
    {
        $struct = new Definition('ZendTest\Di\TestAsset\Struct');
        $struct->setParam('param1', 'foo')
               ->setParam('param2', 'bar');
        $def = new Definition('ZendTest\Di\TestAsset\InjectedMethod');
        $def->addMethodCall('setObject', array(new Reference('struct')));
        $this->di->setDefinition($def)
                 ->setDefinition($struct, 'struct');

        $test = $this->di->get('ZendTest\Di\TestAsset\InjectedMethod');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\InjectedMethod', $test);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $test->object);
        $this->assertSame($test->object, $this->di->get('struct'));
    }

    /**
     * @dataProvider constructorCallbacks
     */
    public function testUsesConstructorCallbackIfDefinedInDefinition($callback)
    {
        $struct = new Definition('ZendTest\Di\TestAsset\Struct');
        $struct->setConstructorCallback($callback);
        $struct->setParam('params', array('foo' => 'bar'));
        $struct->setParamMap(array('params' => 0));
        $this->di->setDefinition($struct, 'struct');
        $test = $this->di->get('struct');
        $this->assertInstanceOf('stdClass', $test);
        $this->assertNotInstanceOf('ZendTest\Di\TestAsset\Struct', $test);
        $this->assertEquals('bar', $test->foo);
    }

    public function constructorCallbacks()
    {
        return array(
            array(__CLASS__ . '::structFactory'),
            array(array($this, 'structFactory')),
            array(function (array $params) {
                $o = (object) $params;
                return $o;
            }),
        );
    }

    public static function structFactory(array $params)
    {
        $o = (object) $params;
        return $o;
    }

    public function testRaisesExceptionForInvalidConstructorCallback()
    {
        $struct = new Definition('ZendTest\Di\TestAsset\Struct');
        $struct->setConstructorCallback(array('foo' => 'bar'));
        $struct->setParam('params', array('foo' => 'bar'));
        $struct->setParamMap(array('params' => 0));
        $this->di->setDefinition($struct, 'struct');

        $this->setExpectedException('Zend\Di\Exception\InvalidCallbackException');
        $test = $this->di->get('struct');
    }

    public function testConstructorCallbackAllowsPassingReferences()
    {
        $struct = new Definition('ZendTest\Di\TestAsset\Struct');
        $struct->setConstructorCallback(function ($params) {
            $o = new \stdClass;
            $o->params = $params;
            return $o;
        });
        $struct->setParam('params', new Reference('params'));
        $struct->setParamMap(array('params' => 0));
        $this->di->setDefinition($struct, 'struct');

        $params = new Definition('ZendTest\Di\TestAsset\DummyParams');
        $params->setParam('params', array('bar' => 'baz'))
               ->setParamMap(array('params' => 0));
        $this->di->setDefinition($params, 'params');

        $testStruct = $this->di->get('struct');
        $testParams = $this->di->get('params');
        $this->assertSame($testParams, $testStruct->params, sprintf('Params: %s; struct: %s', var_export($testParams, 1), var_export($testStruct, 1)));
    }

    /**
     * Test for Circular Dependencies (case 1)
     * 
     * A->B, B->A
     */
    public function testCircularDependencies1() 
    {
        $classA= new Definition('ZendTest\Di\TestAsset\ClassA');
        $classA->setParam('param', new Reference('ZendTest\Di\TestAsset\ClassB'));
        $this->di->setDefinition($classA, 'A');
        
        $classB= new Definition('ZendTest\Di\TestAsset\ClassB');
        $classB->setParam('param', new Reference('ZendTest\Di\TestAsset\ClassA'));
        $this->setExpectedException('Zend\Di\Exception\RuntimeException');
        $this->di->setDefinition($classB, 'B');
    }
    /**
     * Test for Circular Dependencies (case 2)
     * 
     * A->B, B->C, C->A
     */
    public function testCircularDependencies2() 
    {
        $classA= new Definition('ZendTest\Di\TestAsset\ClassA');
        $classA->setParam('param', new Reference('ZendTest\Di\TestAsset\ClassB'));
        $this->di->setDefinition($classA, 'A');
        
        $classB= new Definition('ZendTest\Di\TestAsset\ClassB');
        $classB->setParam('param', new Reference('ZendTest\Di\TestAsset\ClassC'));
        $this->di->setDefinition($classB, 'B');
        
        $classC= new Definition('ZendTest\Di\TestAsset\ClassC');
        $classC->setParam('param', new Reference('ZendTest\Di\TestAsset\ClassA'));
        $this->setExpectedException('Zend\Di\Exception\RuntimeException');
        $this->di->setDefinition($classC, 'C');
    }
    /**
     * Test for Circular Dependencies (case 3)
     * 
     * A->B, B->C, C->D, D->B
     */
    public function testCircularDependencies3() 
    {
        $classA= new Definition('ZendTest\Di\TestAsset\ClassA');
        $classA->setParam('param', new Reference('ZendTest\Di\TestAsset\ClassB'));
        $this->di->setDefinition($classA, 'A');
        
        $classB= new Definition('ZendTest\Di\TestAsset\ClassB');
        $classB->setParam('param', new Reference('ZendTest\Di\TestAsset\ClassC'));
        $this->di->setDefinition($classB, 'B');
        
        $classC= new Definition('ZendTest\Di\TestAsset\ClassC');
        $classC->setParam('param', new Reference('ZendTest\Di\TestAsset\ClassD'));
        $this->di->setDefinition($classC, 'C');
        
        $classD= new Definition('ZendTest\Di\TestAsset\ClassD');
        $classD->setParam('param', new Reference('ZendTest\Di\TestAsset\ClassB'));
        $this->setExpectedException('Zend\Di\Exception\RuntimeException');
        $this->di->setDefinition($classD, 'D');
    }
    /**
     * Test for NO Circular Dependencies
     */
    public function testNoCircularDependencies() 
    {
        $classA= new Definition('ZendTest\Di\TestAsset\ClassA');
        $this->di->setDefinition($classA, 'A');
        
        $classB= new Definition('ZendTest\Di\TestAsset\ClassB');
        $this->di->setDefinition($classB, 'B');

        $a= $this->di->get('A');
        $b= $this->di->get('B');
        
        $a->setParam($b);
        $b->setParam($a);
        $this->assertEquals($a->getParam(),$b);
        $this->assertEquals($b->getParam(),$a);
    }
    /**
     * @todo tests for recursive DI calls
     */
}
