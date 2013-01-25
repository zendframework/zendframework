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

use Zend\Code\Scanner\FileScanner;
use PHPUnit_Framework_TestCase as TestCase;

class ParameterScannerTest extends TestCase
{
    public function testParameterScannerHasParameterInformation()
    {
        $file      = new FileScanner(__DIR__ . '/../TestAsset/BarClass.php');
        $class     = $file->getClass('ZendTest\Code\TestAsset\BarClass');
        $method    = $class->getMethod('three');
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
