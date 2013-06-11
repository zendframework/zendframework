<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace ZendTest\Code\Scanner;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Code\Annotation;
use Zend\Code\Scanner\FileScanner;
use Zend\Stdlib\ErrorHandler;

class ClassScannerTest extends TestCase
{
    protected $manager;

    public function setUp()
    {
        $this->manager = new Annotation\AnnotationManager();

        $genericParser = new Annotation\Parser\GenericAnnotationParser();
        $genericParser->registerAnnotation('ZendTest\Code\Annotation\TestAsset\Foo');
        $genericParser->registerAnnotation('ZendTest\Code\Annotation\TestAsset\Bar');

        $this->manager->attach($genericParser);
    }

    public function testClassScannerHasClassInformation()
    {
        $file  = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $this->assertEquals('ZendTest\Code\TestAsset\FooClass', $class->getName());
        $this->assertEquals('FooClass', $class->getShortName());
        $this->assertFalse($class->isFinal());
        $this->assertTrue($class->isAbstract());
        $this->assertFalse($class->isInterface());
        $interfaces = $class->getInterfaces();
        $this->assertContains('ArrayAccess', $interfaces);
        $this->assertContains('A\B\C\D\Blarg', $interfaces);
        $this->assertContains('ZendTest\Code\TestAsset\Local\SubClass', $interfaces);
        $methods = $class->getMethodNames();
        $this->assertInternalType('array', $methods);
        $this->assertContains('fooBarBaz', $methods);
    }

    public function testClassScannerHasConstant()
    {
        $file  = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $this->assertInternalType('array', $class->getConstantNames());
        $this->assertContains('FOO', $class->getConstantNames());
    }

    public function testClassScannerHasProperties()
    {
        $file  = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $this->assertContains('bar', $class->getPropertyNames());
    }

    public function testClassScannerHasMethods()
    {
        $file  = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $this->assertContains('fooBarBaz', $class->getMethodNames());
    }

    /**
     * @todo Remove error handling once we remove deprecation warning from getConstants method
     */
    public function testGetConstantsReturnsConstantNames()
    {
        $file      = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class     = $file->getClass('ZendTest\Code\TestAsset\FooClass');

        ErrorHandler::start(E_USER_DEPRECATED);
        $constants = $class->getConstants();
        $error = ErrorHandler::stop();

        $this->assertInstanceOf('ErrorException', $error);
        $this->assertContains('FOO', $constants);
    }

    public function testGetConstantsReturnsInstancesOfConstantScanner()
    {
        $file    = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class   = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $constants = $class->getConstants(false);
        foreach ($constants as $constant) {
            $this->assertInstanceOf('Zend\Code\Scanner\ConstantScanner', $constant);
        }
    }

    public function testHasConstant()
    {
        $file    = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class   = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $this->assertTrue($class->hasConstant('FOO'));
        $this->assertFalse($class->hasConstant('foo'));
    }

    public function testHasProperty()
    {
        $file    = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class   = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $this->assertTrue($class->hasProperty('foo'));
        $this->assertFalse($class->hasProperty('FOO'));
        $this->assertTrue($class->hasProperty('bar'));
    }

    public function testHasMethod()
    {
        $file    = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class   = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $this->assertTrue($class->hasMethod('fooBarBaz'));
        $this->assertFalse($class->hasMethod('FooBarBaz'));
        $this->assertFalse($class->hasMethod('bar'));
    }

    public function testClassScannerReturnsMethodsWithMethodScanners()
    {
        $file    = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class   = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $methods = $class->getMethods();
        foreach ($methods as $method) {
            $this->assertInstanceOf('Zend\Code\Scanner\MethodScanner', $method);
        }
    }

    public function testClassScannerReturnsPropertiesWithPropertyScanners()
    {
        $file    = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class   = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $properties = $class->getProperties();
        foreach ($properties as $property) {
            $this->assertInstanceOf('Zend\Code\Scanner\PropertyScanner', $property);
        }
    }

    public function testClassScannerCanScanInterface()
    {
        $file  = new FileScanner(__DIR__ . '/../TestAsset/FooInterface.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\FooInterface');
        $this->assertEquals('ZendTest\Code\TestAsset\FooInterface', $class->getName());
    }

    public function testClassScannerCanReturnLineNumbers()
    {
        $file    = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class   = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $this->assertEquals(11, $class->getLineStart());
        $this->assertEquals(36, $class->getLineEnd());

        $file    = new FileScanner(__DIR__ . '/../TestAsset/BarClass.php');
        $class   = $file->getClass('ZendTest\Code\TestAsset\BarClass');
        $this->assertEquals(10, $class->getLineStart());
        $this->assertEquals(33, $class->getLineEnd());
    }

    public function testClassScannerCanScanAnnotations()
    {
        $file    = new FileScanner(__DIR__ . '/../Annotation/TestAsset/EntityWithAnnotations.php');
        $class   = $file->getClass('ZendTest\Code\Annotation\TestAsset\EntityWithAnnotations');
        $annotations = $class->getAnnotations($this->manager);

        $this->assertTrue($annotations->hasAnnotation('ZendTest\Code\Annotation\TestAsset\Foo'));
        $this->assertTrue($annotations->hasAnnotation('ZendTest\Code\Annotation\TestAsset\Bar'));

        $this->assertEquals('first',  $annotations[0]->content);
        $this->assertEquals('second', $annotations[1]->content);
        $this->assertEquals('third',  $annotations[2]->content);
    }
}
