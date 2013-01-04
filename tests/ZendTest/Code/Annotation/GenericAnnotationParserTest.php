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
use Zend\EventManager\Event;

class GenericAnnotationParserTest extends TestCase
{
    public function setUp()
    {
        $this->parser = new Annotation\Parser\GenericAnnotationParser();
    }

    public function getFooEvent()
    {
        $event = new Event();
        $event->setParams(array(
            'class' => __NAMESPACE__ . '\TestAsset\Foo',
            'content' => '(test content)',
            'raw' => '@' . __NAMESPACE__ . '\TestAsset\Foo(test content)',
        ));
        return $event;
    }

    public function testParserKeepsTrackOfAllowedAnnotations()
    {
        $this->parser->registerAnnotation(new TestAsset\Foo());
        $this->parser->registerAnnotation(new TestAsset\Bar());

        $this->assertTrue($this->parser->hasAnnotation(__NAMESPACE__ . '\TestAsset\Foo'));
        $this->assertTrue($this->parser->hasAnnotation(__NAMESPACE__ . '\TestAsset\Bar'));
        $this->assertFalse($this->parser->hasAnnotation(__NAMESPACE__ . '\TestAsset\Bogus'));
    }

    public function testParserCreatesNewAnnotationInstances()
    {
        $foo = new TestAsset\Foo();
        $this->parser->registerAnnotation($foo);

        $event = $this->getFooEvent();
        $test = $this->parser->onCreateAnnotation($event);
        $this->assertInstanceOf(__NAMESPACE__ . '\TestAsset\Foo', $test);
        $this->assertNotSame($foo, $test);
        $this->assertEquals('test content', $test->content);
    }

    public function testReturnsFalseDuringCreationIfAnnotationIsNotRegistered()
    {
        $event = $this->getFooEvent();
        $this->assertFalse($this->parser->onCreateAnnotation($event));
    }

    public function testParserAllowsPassingArrayOfAnnotationInstances()
    {
        $this->parser->registerAnnotations(array(
            new TestAsset\Foo(),
            new TestAsset\Bar(),
        ));
        $this->assertTrue($this->parser->hasAnnotation(__NAMESPACE__ . '\TestAsset\Foo'));
        $this->assertTrue($this->parser->hasAnnotation(__NAMESPACE__ . '\TestAsset\Bar'));
    }

    public function testAllowsSpecifyingAliases()
    {
        $bar = new TestAsset\Bar();
        $this->parser->registerAnnotation($bar);
        $this->parser->setAlias(__NAMESPACE__ . '\TestAsset\Foo', get_class($bar));

        $event = $this->getFooEvent();
        $test  = $this->parser->onCreateAnnotation($event);
        $this->assertInstanceOf(__NAMESPACE__ . '\TestAsset\Bar', $test);
        $this->assertNotSame($bar, $test);
        $this->assertEquals('test content', $test->content);
    }
}
