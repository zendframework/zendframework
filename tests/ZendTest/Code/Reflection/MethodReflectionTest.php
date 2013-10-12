<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace ZendTest\Code\Reflection;

use Zend\Code\Reflection\MethodReflection;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Method
 */
class MethodReflectionTest extends \PHPUnit_Framework_TestCase
{
   public function testDeclaringClassReturn()
    {
        $method = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', 'getProp1');
        $this->assertInstanceOf('Zend\Code\Reflection\ClassReflection', $method->getDeclaringClass());
    }

    public function testParemeterReturn()
    {
        $method = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', 'getProp2');
        $parameters = $method->getParameters();
        $this->assertEquals(2, count($parameters));
        $this->assertInstanceOf('Zend\Code\Reflection\ParameterReflection', array_shift($parameters));
    }

    public function testStartLine()
    {
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5', 'doSomething');

        $this->assertEquals(37, $reflectionMethod->getStartLine());
        $this->assertEquals(21, $reflectionMethod->getStartLine(true));
    }

    public function testGetBodyReturnsCorrectBody()
    {
        $body = '        //we need a multi-line method body.
        $assigned = 1;
        $alsoAssigined = 2;
        return \'mixedValue\';';
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass6', 'doSomething');
        $this->assertEquals($body, $reflectionMethod->getBody());
    }

    public function testGetContentsReturnsCorrectContent()
    {
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5', 'doSomething');
        $this->assertEquals("    {\n\n        return 'mixedValue';\n\n    }\n", $reflectionMethod->getContents(false));
    }

    public function testGetPrototypeMethod()
    {
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass10', 'doSomethingElse');
        $prototype = array(
            'namespace' => 'ZendTest\Code\Reflection\TestAsset',
            'class' => 'TestSampleClass10',
            'name' => 'doSomethingElse',
            'return' => 'int',
            'arguments' => array(
                'one' => array(
                    'type'     => 'int',
                    'required' => true,
                    'by_ref'   => false,
                    'default'  => null,
                ),
                'two' => array(
                    'type'     => 'int',
                    'required' => false,
                    'by_ref'   => false,
                    'default'  => 2,
                ),
                'three' => array(
                    'type'     => 'string',
                    'required' => false,
                    'by_ref'   => false,
                    'default'  => 'three',
                ),
            ),
        );
        $this->assertEquals($prototype, $reflectionMethod->getPrototype());
        
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', 'getProp2');
        $prototype = array(
            'namespace' => 'ZendTest\Code\Reflection\TestAsset',
            'class' => 'TestSampleClass2',
            'name' => 'getProp2',
            'return' => '',
            'arguments' => array(
                'param1' => array(
                    'type'     => '',
                    'required' => true,
                    'by_ref'   => false,
                    'default'  => null,
                ),
                'param2' => array(
                    'type'     => 'ZendTest\Code\Reflection\TestAsset\TestSampleClass',
                    'required' => true,
                    'by_ref'   => false,
                    'default'  => null,
                ),
            ),
        );
        $this->assertEquals($prototype, $reflectionMethod->getPrototype());
    }
}
