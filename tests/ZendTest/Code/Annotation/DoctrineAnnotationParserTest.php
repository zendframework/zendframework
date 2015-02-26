<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Annotation;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Code\Annotation;
use Zend\Code\Annotation\Parser\DoctrineAnnotationParser;
use Zend\EventManager\Event;

class DoctrineAnnotationParserTest extends TestCase
{
    /**
     * @var DoctrineAnnotationParser
     */
    private $parser;

    public function setUp()
    {
        if (!defined('TESTS_ZEND_CODE_ANNOTATION_DOCTRINE_SUPPORT')
            || !constant('TESTS_ZEND_CODE_ANNOTATION_DOCTRINE_SUPPORT')
        ) {
            $this->markTestSkipped('Enable TESTS_ZEND_CODE_ANNOTATION_DOCTRINE_SUPPORT to test doctrine annotation parsing');
        }

        $this->parser = new DoctrineAnnotationParser();
    }

    public function getEvent()
    {
        $event = new Event();
        $event->setParams(array(
            'class'   => __NAMESPACE__ . '\TestAsset\DoctrineAnnotation',
            'content' => '(foo="bar")',
            'raw'     => '@' . __NAMESPACE__ . '\TestAsset\DoctrineAnnotation(foo="bar")',
        ));
        return $event;
    }

    public function testParserCreatesNewAnnotationInstances()
    {
        $this->parser->registerAnnotation(__NAMESPACE__ . '\TestAsset\DoctrineAnnotation');

        $event = $this->getEvent();
        $test  = $this->parser->onCreateAnnotation($event);
        $this->assertInstanceOf(__NAMESPACE__ . '\TestAsset\DoctrineAnnotation', $test);
        $this->assertEquals(array('foo' => 'bar'), $test->value);
    }

    public function testReturnsFalseDuringCreationIfAnnotationIsNotRegistered()
    {
        $event = $this->getEvent();
        $this->assertFalse($this->parser->onCreateAnnotation($event));
    }

    public function testReturnsFalseClassNotSet()
    {
        $this->assertFalse($this->parser->onCreateAnnotation(new Event()));
    }

    public function testReturnsFalseRawNotSet()
    {
        $this->parser->registerAnnotation(__NAMESPACE__ . '\TestAsset\DoctrineAnnotation');
        $event = $this->getEvent();
        $event->setParam('raw', false);

        $this->assertFalse($this->parser->onCreateAnnotation($event));
    }

    public function testReturnsFalseEmptyAnnotations()
    {
        $this->parser->registerAnnotation(__NAMESPACE__ . '\TestAsset\DoctrineAnnotation');
        $event = $this->getEvent();
        $event->setParam('raw', 'foo');
        $this->assertFalse($this->parser->onCreateAnnotation($event));
    }

    /**
     * @expectedException \Zend\Code\Exception\InvalidArgumentException
     */
    public function testRegisterAnnotationsThrowsException()
    {
        $this->parser->registerAnnotations('some string');
    }

    public function testRegisterAnnotations()
    {
        $this->parser->registerAnnotations(array(__NAMESPACE__ . '\TestAsset\DoctrineAnnotation'));
        $event = $this->getEvent();
        $test  = $this->parser->onCreateAnnotation($event);
        $this->assertInstanceOf(__NAMESPACE__ . '\TestAsset\DoctrineAnnotation', $test);
        $this->assertEquals(array('foo' => 'bar'), $test->value);
    }
}
