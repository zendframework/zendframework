<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Reflection;

use Zend\Code\Reflection\ClassReflection;
use ZendTest\Code\Reflection\TestAsset\InjectableClassReflection;

/**
 *
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Class
 */
class ClassReflectionTest extends \PHPUnit_Framework_TestCase
{
    public function testMethodReturns()
    {
        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2');

        $methodByName = $reflectionClass->getMethod('getProp1');
        $this->assertEquals('Zend\Code\Reflection\MethodReflection', get_class($methodByName));

        $methodsAll = $reflectionClass->getMethods();
        $this->assertEquals(3, count($methodsAll));

        $firstMethod = array_shift($methodsAll);
        $this->assertEquals('getProp1', $firstMethod->getName());
    }

    public function testPropertyReturns()
    {
        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2');

        $propertyByName = $reflectionClass->getProperty('_prop1');
        $this->assertInstanceOf('Zend\Code\Reflection\PropertyReflection', $propertyByName);

        $propertiesAll = $reflectionClass->getProperties();
        $this->assertEquals(2, count($propertiesAll));

        $firstProperty = array_shift($propertiesAll);
        $this->assertEquals('_prop1', $firstProperty->getName());
    }

    public function testParentReturn()
    {
        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass');

        $parent = $reflectionClass->getParentClass();
        $this->assertEquals('Zend\Code\Reflection\ClassReflection', get_class($parent));
        $this->assertEquals('ArrayObject', $parent->getName());
    }

    public function testInterfaceReturn()
    {
        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass4');

        $interfaces = $reflectionClass->getInterfaces();
        $this->assertEquals(1, count($interfaces));

        $interface = array_shift($interfaces);
        $this->assertEquals('ZendTest\Code\Reflection\TestAsset\TestSampleClassInterface', $interface->getName());
    }

    public function testGetContentsReturnsContents()
    {
        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2');
        $target = <<<EOS
{
    protected \$_prop1 = null;

    /**
     * @Sample({"foo":"bar"})
     */
    protected \$_prop2 = null;

    public function getProp1()
    {
        return \$this->_prop1;
    }

    public function getProp2(\$param1, TestSampleClass \$param2)
    {
        return \$this->_prop2;
    }

    public function getIterator()
    {
        return array();
    }

}
EOS;
        $contents = $reflectionClass->getContents();
        $this->assertEquals(trim($target), trim($contents));
    }

    public function testGetContentsReturnsContentsWithImplementsOnSeparateLine()
    {
        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass9');
        $target = <<<EOS
{
    protected \$_prop1 = null;

    /**
     * @Sample({"foo":"bar"})
     */
    protected \$_prop2 = null;

    public function getProp1()
    {
        return \$this->_prop1;
    }

    public function getProp2(\$param1, TestSampleClass \$param2)
    {
        return \$this->_prop2;
    }

    public function getIterator()
    {
        return array();
    }

}
EOS;
        $contents = $reflectionClass->getContents();
        $this->assertEquals(trim($target), trim($contents));
    }

    public function testStartLine()
    {
        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $this->assertEquals(18, $reflectionClass->getStartLine());
        $this->assertEquals(5, $reflectionClass->getStartLine(true));
    }

    public function testGetDeclaringFileReturnsFilename()
    {
        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2');
        $this->assertContains('TestSampleClass2.php', $reflectionClass->getDeclaringFile()->getFileName());
    }

    public function testGetAnnotationsWithNoNameInformations()
    {
        $reflectionClass = new InjectableClassReflection(
            // TestSampleClass5 has the annotations required to get to the
            // right point in the getAnnotations method.
            'ZendTest\Code\Reflection\TestAsset\TestSampleClass5'
        );

        $annotationManager = new \Zend\Code\Annotation\AnnotationManager();

        $fileScanner = $this->getMockBuilder('Zend\Code\Scanner\FileScanner')
                            ->disableOriginalConstructor()
                            ->getMock();

        $reflectionClass->setFileScanner($fileScanner);

        $fileScanner->expects($this->any())
                    ->method('getClassNameInformation')
                    ->will($this->returnValue(false));

        $this->assertFalse($reflectionClass->getAnnotations($annotationManager));
    }

    public function testGetContentsReturnsEmptyContentsOnEvaldCode()
    {
        $className = uniqid('ClassReflectionTestGenerated');

        eval('name' . 'space ' . __NAMESPACE__ . '; cla' . 'ss ' . $className . '{}');

        $reflectionClass = new ClassReflection(__NAMESPACE__ . '\\' . $className);

        $this->assertSame('', $reflectionClass->getContents());
    }

    public function testGetContentsReturnsEmptyContentsOnInternalCode()
    {
        $reflectionClass = new ClassReflection('ReflectionClass');
        $this->assertSame('', $reflectionClass->getContents());
    }

    public function testGetTraits()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        // PHP documentations mentions that getTraits() return NULL in case of error. I don't know how to cause such
        // error so I test just normal behaviour.

        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestTraitClass4');
        $traitsArray = $reflectionClass->getTraits();
        $this->assertInternalType('array', $traitsArray);
        $this->assertCount(1, $traitsArray);
        $this->assertInstanceOf('Zend\Code\Reflection\ClassReflection', $traitsArray[0]);

        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass');
        $traitsArray = $reflectionClass->getTraits();
        $this->assertInternalType('array', $traitsArray);
        $this->assertCount(0, $traitsArray);
    }
}
