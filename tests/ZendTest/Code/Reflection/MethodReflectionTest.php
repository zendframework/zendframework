<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Reflection;

use Zend\Code\Reflection\MethodReflection;
use ZendTest\Code\Reflection\TestAsset\InjectableMethodReflection;

/**
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

    public function testGetAnnotationsWithNoNameInformations()
    {
        $reflectionMethod = new InjectableMethodReflection(
            // TestSampleClass5 has the annotations required to get to the
            // right point in the getAnnotations method.
            'ZendTest\Code\Reflection\TestAsset\TestSampleClass5',
            'doSomething'
        );

        $annotationManager = new \Zend\Code\Annotation\AnnotationManager();

        $fileScanner = $this->getMockBuilder('Zend\Code\Scanner\CachingFileScanner')
                            ->disableOriginalConstructor()
                            ->getMock();

        $reflectionMethod->setFileScanner($fileScanner);

        $fileScanner->expects($this->any())
                    ->method('getClassNameInformation')
                    ->will($this->returnValue(false));

        $this->assertFalse($reflectionMethod->getAnnotations($annotationManager));
    }

    /**
     * @group 5062
     */
    public function testGetContentsWithCoreClass()
    {
        $reflectionMethod = new MethodReflection('DateTime', 'format');
        $this->assertEquals("", $reflectionMethod->getContents(false));
    }

    public function testGetContentsReturnsEmptyContentsOnEvaldCode()
    {
        $className = uniqid('MethodReflectionTestGenerated');

        eval('name' . 'space ' . __NAMESPACE__ . '; cla' . 'ss ' . $className . '{fun' . 'ction foo(){}}');

        $reflectionMethod = new MethodReflection(__NAMESPACE__ . '\\' . $className, 'foo');

        $this->assertSame('', $reflectionMethod->getContents());
        $this->assertSame('', $reflectionMethod->getBody());
    }

    public function testGetContentsReturnsEmptyContentsOnInternalCode()
    {
        $reflectionMethod = new MethodReflection('ReflectionClass', 'getName');

        $this->assertSame('', $reflectionMethod->getContents());
    }
}
