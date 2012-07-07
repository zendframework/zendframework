<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Code
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Code\Annotation;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Code\Annotation;
use Zend\EventManager\Event;

class DoctrineAnnotationParserTest extends TestCase
{
    public function setUp()
    {
        if (!defined('TESTS_ZEND_CODE_ANNOTATION_DOCTRINE_SUPPORT')
            || !constant('TESTS_ZEND_CODE_ANNOTATION_DOCTRINE_SUPPORT')
        ) {
            $this->markTestSkipped('Enable TESTS_ZEND_CODE_ANNOTATION_DOCTRINE_SUPPORT to test doctrine annotation parsing');
        }

        $this->parser = new Annotation\Parser\DoctrineAnnotationParser();
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
}

