<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace ZendTest\Code\Annotation;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Code\Annotation;
use Zend\Code\Reflection;

class AnnotationManagerTest extends TestCase
{
    public function setUp()
    {
        if (!defined('TESTS_ZEND_CODE_ANNOTATION_DOCTRINE_SUPPORT')
            || !constant('TESTS_ZEND_CODE_ANNOTATION_DOCTRINE_SUPPORT')
        ) {
            $this->markTestSkipped('Enable TESTS_ZEND_CODE_ANNOTATION_DOCTRINE_SUPPORT to test doctrine annotation parsing');
        }

        $this->manager = new Annotation\AnnotationManager();
    }

    public function testAllowsMultipleParsingStrategies()
    {
        $genericParser = new Annotation\Parser\GenericAnnotationParser();
        $genericParser->registerAnnotation(__NAMESPACE__ . '\TestAsset\Foo');

        $doctrineParser = new Annotation\Parser\DoctrineAnnotationParser();
        $doctrineParser->registerAnnotation(__NAMESPACE__ . '\TestAsset\DoctrineAnnotation');

        $this->manager->attach($genericParser);
        $this->manager->attach($doctrineParser);

        $reflection = new Reflection\ClassReflection(__NAMESPACE__ . '\TestAsset\EntityWithMixedAnnotations');
        $prop = $reflection->getProperty('test');
        $annotations = $prop->getAnnotations($this->manager);

        $this->assertTrue($annotations->hasAnnotation(__NAMESPACE__ . '\TestAsset\Foo'));
        $this->assertTrue($annotations->hasAnnotation(__NAMESPACE__ . '\TestAsset\DoctrineAnnotation'));
        $this->assertFalse($annotations->hasAnnotation(__NAMESPACE__ . '\TestAsset\Bar'));

        foreach ($annotations as $annotation) {
            switch (get_class($annotation)) {
                case __NAMESPACE__ . '\TestAsset\Foo':
                    $this->assertEquals('first', $annotation->content);
                    break;
                case __NAMESPACE__ . '\TestAsset\DoctrineAnnotation':
                    $this->assertEquals(array('foo' => 'bar', 'bar' => 'baz'), $annotation->value);
                    break;
                default:
                    $this->fail('Received unexpected annotation "' . get_class($annotation) . '"');
            }
        }
    }
}
