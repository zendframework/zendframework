<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Scanner;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Code\Annotation;
use Zend\Code\Scanner\FileScanner;
use Zend\Stdlib\ErrorHandler;
use ZendTest\Code\TestAsset\TraitWithSameMethods;
use ZendTest\Code\TestAsset\TestClassWithTraitAliases;

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
        $this->assertEquals(37, $class->getLineEnd());
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

    /**
     * @group trait1
     */
    public function testClassScannerCanScanTraits()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('Skipping; PHP 5.4 or greater is needed');
        }

        $file  = new FileScanner(__DIR__ . '/../TestAsset/BarTrait.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\BarTrait');

        $this->assertTrue($class->isTrait());
        $this->assertTrue($class->hasMethod('bar'));
    }

    /**
     * @group trait2
     */
    public function testClassScannerCanScanClassThatUsesTraits()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('Skipping; PHP 5.4 or greater is needed');
        }

        $file  = new FileScanner(__DIR__ . '/../TestAsset/TestClassUsesTraitSimple.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\TestClassUsesTraitSimple');

        $this->assertFalse($class->isTrait());
        $traitNames = $class->getTraitNames();
        $class->getTraitAliases();
        $this->assertContains('ZendTest\Code\TestAsset\BarTrait', $traitNames);
        $this->assertContains('ZendTest\Code\TestAsset\FooTrait', $traitNames);
    }

    /**
     * @group trait3
     */
    public function testClassScannerCanScanClassAndGetTraitsAliases()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('Skipping; PHP 5.4 or greater is needed');
        }

        $file  = new FileScanner(__DIR__ . '/../TestAsset/TestClassWithTraitAliases.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\TestClassWithTraitAliases');

        $this->assertFalse($class->isTrait());

        $aliases = $class->getTraitAliases();

        $this->assertEquals(count($aliases), 1);

        $this->assertEquals(key($aliases), 'test');
        $this->assertEquals(current($aliases), 'ZendTest\Code\TestAsset\TraitWithSameMethods::foo');
    }

    /**
     * @group trait4
     */
    public function testClassScannerCanGetTraitMethodsInGetMethods()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('Skipping; PHP 5.4 or greater is needed');
        }

        //load files or test may fail due to autoload issues
        require_once(__DIR__ . '/../TestAsset/TraitWithSameMethods.php');
        require_once(__DIR__ . '/../TestAsset/BarTrait.php');

        $file  = new FileScanner(__DIR__ . '/../TestAsset/TestClassWithTraitAliases.php');

        $class = $file->getClass('ZendTest\Code\TestAsset\TestClassWithTraitAliases');

        $this->assertFalse($class->isTrait());

        $testMethods = array(
            'fooBarBaz' => 'isPublic',
            'foo' => 'isPublic',
            'bar' => 'isPublic',
            'test' => 'isPrivate',
            'bazFooBar' => 'isPublic',
        );

        $this->assertEquals($class->getMethodNames(), array_keys($testMethods));

        foreach ($testMethods as $methodName => $testMethod) {
            $this->assertTrue($class->hasMethod($methodName), "Cannot find method $methodName");

            $method = $class->getMethod($methodName);
            $this->assertInstanceOf('Zend\Code\Scanner\MethodScanner', $method, $methodName . ' not found.');

            $this->assertTrue($method->$testMethod());

            // test that we got the right ::bar method based on declaration
            if ($testMethod === "bar") {
                $this->assertEquals(trim($method->getBody), 'echo "foo";');
            }
        }
    }

    /**
     * @group trait5
     */
    public function testGetMethodsThrowsExceptionOnDuplicateMethods()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('Skipping; PHP 5.4 or greater is needed');
        }

        $this->setExpectedException('Zend\Code\Exception\RuntimeException');

        $file  = new FileScanner(__DIR__ . '/TestAsset/TestClassWithAliasException.php');
        $class = $file->getClass('ZendTest\Code\Scanner\TestAsset\TestClassWithAliasException');

        $class->getMethods();
    }
}
