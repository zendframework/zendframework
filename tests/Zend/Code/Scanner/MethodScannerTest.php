<?php

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\FileScanner,
    PHPUnit_Framework_TestCase as TestCase;

class MethodScannerTest extends TestCase
{
    public function testMethodScannerHasMethodInformation()
    {
        $file   = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class  = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $method = $class->getMethod('fooBarBaz');
        $this->assertEquals('fooBarBaz', $method->getName());
        $this->assertFalse($method->isAbstract());
        $this->assertTrue($method->isFinal());
        $this->assertTrue($method->isPublic());
        $this->assertFalse($method->isProtected());
        $this->assertFalse($method->isPrivate());
        $this->assertFalse($method->isStatic());
    }
    
    public function testMethodScannerReturnsParameters()
    {
        $file       = new FileScanner(__DIR__ . '/../TestAsset/BarClass.php');
        $class      = $file->getClass('ZendTest\Code\TestAsset\BarClass');
        $method     = $class->getMethod('three');
        $parameters = $method->getParameters();
        $this->assertInternalType('array', $parameters);
    }

    public function testMethodScannerReturnsParameterScanner()
    {
        $file   = new FileScanner(__DIR__ . '/../TestAsset/BarClass.php');
        $class  = $file->getClass('ZendTest\Code\TestAsset\BarClass');
        $method = $class->getMethod('three');
        $this->assertEquals(array('o', 't', 'bbf'), $method->getParameters());
        $parameter = $method->getParameter('t');
        $this->assertInstanceOf('Zend\Code\Scanner\ParameterScanner', $parameter);
        $this->assertEquals('t', $parameter->getName());
    }
}
