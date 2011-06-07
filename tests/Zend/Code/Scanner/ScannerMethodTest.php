<?php

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\ScannerFile;

class ScannerMethodTest extends \PHPUnit_Framework_TestCase
{
    
    public function testScannerMethodHasMethodInformation()
    {
        $file = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\FooClass');
        $method = $class->getMethod('fooBarBaz');
        $this->assertEquals('fooBarBaz', $method->getName());
        $this->assertFalse($method->isAbstract());
        $this->assertTrue($method->isFinal());
        $this->assertTrue($method->isPublic());
        $this->assertFalse($method->isProtected());
        $this->assertFalse($method->isPrivate());
        $this->assertFalse($method->isStatic());
    }
    
    public function testScannerMethodReturnsParameters()
    {
        $file = new ScannerFile(__DIR__ . '/../TestAsset/BarClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\BarClass');
        $method = $class->getMethod('three');
        $parameters = $method->getParameters();
        $this->assertInternalType('array', $parameters);
    }

    public function testScannerMethodReturnsParameterScanner()
    {
        $file = new ScannerFile(__DIR__ . '/../TestAsset/BarClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\BarClass');
        $method = $class->getMethod('three');
        $this->assertEquals(array('o', 't', 'bbf'), $method->getParameters());
        $parameter = $method->getParameter('t');
        $this->assertInstanceOf('Zend\Code\Scanner\ScannerParameter', $parameter);
        $this->assertEquals('t', $parameter->getName());
    }

}
