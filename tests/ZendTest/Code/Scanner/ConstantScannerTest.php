<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\FileScanner;
use PHPUnit_Framework_TestCase as TestCase;

class ConstantScannerTest extends TestCase
{
    public function testConstantScannerHasConstantInformation()
    {
        $file = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $class = $file->getClass('ZendTest\Code\TestAsset\FooClass');

        $constant = $class->getConstant('BAR');
        $this->assertEquals('BAR', $constant->getName());
        $this->assertEquals(5, $constant->getValue());

        $constant = $class->getConstant('FOO');
        $this->assertEquals('FOO', $constant->getName());
        $this->assertEquals(5, $constant->getValue());

        $constant = $class->getConstant('BAZ');
        $this->assertEquals('BAZ', $constant->getName());
        $this->assertEquals('baz', $constant->getValue());
        $this->assertNotNull('Some comment', $constant->getDocComment());
    }
}
