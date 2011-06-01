<?php

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\ScannerFile;

final class ScannerClassTest extends \PHPUnit_Framework_TestCase
{
    
    public function testScannerClassHasClassInformation()
    {
        $file = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
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
        $methods = $class->getMethods();
        $this->assertInternalType('array', $methods);
        $this->assertContains('fooBarBaz', $methods);
    }
    
    public function testScannerClassHasConstant()
    {
        $file = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $this->assertInternalType('array', $class->getConstants());
        //
    }
    
    public function testScannerClassHasProperties()
    {
        $file = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $this->assertInternalType('array', $class->getProperties());
        $this->assertContains('bar', $class->getProperties());
    }
    
    public function testScannerClassHasMethods()
    {
        $file = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $this->assertContains('fooBarBaz', $class->getMethods());
    }
    
    public function testScannerClassReturnsMethodsWithScannerMethod()
    {
        $file = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $methods = $class->getMethods(true);
        $method = array_shift($methods);
        $this->assertInstanceOf('Zend\Code\Scanner\ScannerMethod', $method);
    }
    
}