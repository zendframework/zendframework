<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Reflection;

use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Annotation\Parser\GenericAnnotationParser;
use ZendTest\Code\Reflection\TestAsset\InjectablePropertyReflection;

/**
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Property
 */
class PropertyReflectionTest extends \PHPUnit_Framework_TestCase
{
    public function testDeclaringClassReturn()
    {
        $property = new \Zend\Code\Reflection\PropertyReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', '_prop1');
        $this->assertInstanceOf('Zend\Code\Reflection\ClassReflection', $property->getDeclaringClass());
        $this->assertEquals('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', $property->getDeclaringClass()->getName());
    }

    public function testAnnotationScanningIsPossible()
    {
        $manager = new AnnotationManager();
        $parser = new GenericAnnotationParser();
        $parser->registerAnnotation(new TestAsset\SampleAnnotation());
        $manager->attach($parser);

        $property = new \Zend\Code\Reflection\PropertyReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', '_prop2');
        $annotations = $property->getAnnotations($manager);
        $this->assertInstanceOf('Zend\Code\Annotation\AnnotationCollection', $annotations);
        $this->assertTrue($annotations->hasAnnotation('ZendTest\Code\Reflection\TestAsset\SampleAnnotation'));
        $found = false;
        foreach ($annotations as $key => $annotation) {
            if (!$annotation instanceof TestAsset\SampleAnnotation) {
                continue;
            }
            $this->assertEquals(get_class($annotation) . ': {"foo":"bar"}', $annotation->content);
            $found = true;
            break;
        }
        $this->assertTrue($found);
    }

    public function testGetAnnotationsWithNoNameInformations()
    {
        $reflectionProperty = new InjectablePropertyReflection(
            // TestSampleClass5 has the annotations required to get to the
            // right point in the getAnnotations method.
            'ZendTest\Code\Reflection\TestAsset\TestSampleClass2',
            '_prop2'
        );

        $annotationManager = new \Zend\Code\Annotation\AnnotationManager();

        $fileScanner = $this->getMockBuilder('Zend\Code\Scanner\CachingFileScanner')
                            ->disableOriginalConstructor()
                            ->getMock();

        $reflectionProperty->setFileScanner($fileScanner);

        $fileScanner->expects($this->any())
                    ->method('getClassNameInformation')
                    ->will($this->returnValue(false));

        $this->assertFalse($reflectionProperty->getAnnotations($annotationManager));
    }
}
