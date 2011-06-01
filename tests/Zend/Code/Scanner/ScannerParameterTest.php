<?php

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\ScannerFile;

class ScannerParameterTest extends \PHPUnit_Framework_TestCase
{
    
    public function testScannerParamterHasParameterInformation()
    {
        $file = new ScannerFile(__DIR__ . '/../TestAsset/BarClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\BarClass');
        $method = $class->getMethod('three');
        $parameter = $method->getParameter('t');
        $this->assertEquals('ZendTest\Code\TestAsset\BarClass', $parameter->getDeclaringClass());
        $this->assertEquals('three', $parameter->getDeclaringFunction());
        $this->assertEquals('t', $parameter->getName());
        $this->assertEquals(2, $parameter->getPosition());
        $this->assertEquals('2', $parameter->getDefaultValue());
        $this->assertFalse($parameter->isArray());
        $this->assertTrue($parameter->isDefaultValueAvailable());
        $this->assertTrue($parameter->isOptional());
        $this->assertTrue($parameter->isPassedByReference());
        
    }
}