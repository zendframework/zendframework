<?php

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\FileScanner,
    PHPUnit_Framework_TestCase as TestCase;

class ClassScannerTest extends TestCase
{
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
        $this->assertInternalType('array', $class->getConstants());
    }

    public function testClassScannerHasProperties()
    {
        $file  = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $this->assertInternalType('array', $class->getPropertyNames());
        $this->assertContains('bar', $class->getPropertyNames());
    }

    public function testClassScannerHasMethods()
    {
        $file  = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $this->assertContains('fooBarBaz', $class->getMethodNames());
    }

    public function testClassScannerReturnsMethodsWithMethodScanners()
    {
        $file    = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class   = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $methods = $class->getMethods(true);
        foreach ($methods as $method) {
            $this->assertInstanceOf('Zend\Code\Scanner\MethodScanner', $method);
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
        $this->assertEquals(23, $class->getLineEnd());

        $file    = new FileScanner(__DIR__ . '/../TestAsset/BarClass.php');
        $class   = $file->getClass('ZendTest\Code\TestAsset\BarClass');
        $this->assertEquals(11, $class->getLineStart());
        $this->assertEquals(34, $class->getLineEnd());
    }

}
