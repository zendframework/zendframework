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

class AnnotationManagerTest extends TestCase
{
    public function setUp()
    {
        $this->manager = new Annotation\AnnotationManager();
    }

    public function testManagerKeepsTrackOfAllowedAnnotations()
    {
        $this->manager->registerAnnotation(new TestAsset\Foo());
        $this->manager->registerAnnotation(new TestAsset\Bar());

        $this->assertTrue($this->manager->hasAnnotation(__NAMESPACE__ . '\TestAsset\Foo'));
        $this->assertTrue($this->manager->hasAnnotation(__NAMESPACE__ . '\TestAsset\Bar'));
        $this->assertFalse($this->manager->hasAnnotation(__NAMESPACE__ . '\TestAsset\Bogus'));
    }

    public function testManagerCreatesNewAnnotationInstances()
    {
        $foo = new TestAsset\Foo();
        $this->manager->registerAnnotation($foo);

        $test = $this->manager->createAnnotation(__NAMESPACE__ . '\TestAsset\Foo', 'test content');
        $this->assertInstanceOf(__NAMESPACE__ . '\TestAsset\Foo', $test);
        $this->assertNotSame($foo, $test);
        $this->assertEquals('test content', $test->content);
    }

    public function testManagerRaisesAnExceptionDuringCreationIfAnnotationIsNotRegistered()
    {
        $this->setExpectedException('Zend\Code\Exception\RuntimeException', 'annotation');
        $this->manager->createAnnotation(__NAMESPACE__ . '\TestAsset\Foo', 'test content');
    }

    public function testManagerAllowsPassingArrayOfAnnotationInstancesToConstructor()
    {
        $manager = new Annotation\AnnotationManager(array(
            new TestAsset\Foo(),
            new TestAsset\Bar(),
        ));
        $this->assertTrue($manager->hasAnnotation(__NAMESPACE__ . '\TestAsset\Foo'));
        $this->assertTrue($manager->hasAnnotation(__NAMESPACE__ . '\TestAsset\Bar'));
    }

    public function testAllowsSpecifyingAliases()
    {
        $bar = new TestAsset\Bar();
        $this->manager->registerAnnotation($bar);
        $this->manager->setAlias(__NAMESPACE__ . '\TestAsset\Foo', get_class($bar));

        $test = $this->manager->createAnnotation(__NAMESPACE__ . '\TestAsset\Foo', 'test content');
        $this->assertInstanceOf(__NAMESPACE__ . '\TestAsset\Bar', $test);
        $this->assertNotSame($bar, $test);
        $this->assertEquals('test content', $test->content);
    }
}
