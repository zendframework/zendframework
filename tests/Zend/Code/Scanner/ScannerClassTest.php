<?php

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\ScannerFile;

final class ScannerClassTest extends \PHPUnit_Framework_TestCase
{
    
    public function testScannerClassHasClassInformation()
    {
        $fileScanner = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $fileScanner->scan();
        $class = $fileScanner->getClass('ZendTest\Code\TestAsset\FooClass');
        $class->scan();
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
        $fileScanner = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $fileScanner->scan();
        $class = $fileScanner->getClass('ZendTest\Code\TestAsset\FooClass');
        $class->scan();
        $this->assertInternalType('array', $class->getConstants());
        //
    }
    
    public function testScannerClassHasProperties()
    {
        $fileScanner = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $fileScanner->scan();
        $class = $fileScanner->getClass('ZendTest\Code\TestAsset\FooClass');
        $class->scan();
        $this->assertInternalType('array', $class->getProperties());
        $this->assertContains('bar', $class->getProperties());
    }
    
    public function testScannerClassHasMethods()
    {
        $fileScanner = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $fileScanner->scan();
        $class = $fileScanner->getClass('ZendTest\Code\TestAsset\FooClass');
        $class->scan();
        $this->assertContains('fooBarBaz', $class->getMethods());
    }
    
}