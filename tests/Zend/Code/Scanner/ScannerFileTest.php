<?php

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\ScannerFile;

class ScannerFileTest extends \PHPUnit_Framework_TestCase
{
    
    public function testFileScannerReturnsNamespacesWithoutScannerClass()
    {
        $fileScanner = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $fileScanner->scan();
        $namespaces = $fileScanner->getNamespaces();
        $this->assertInternalType('array', $namespaces);
        $this->assertContains('ZendTest\Code\TestAsset', $namespaces);
    }
    
    public function testFileScannerReturnsClassesWithoutScannerClass()
    {
        $fileScanner = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $fileScanner->scan();
        $classes = $fileScanner->getClasses();
        $this->assertInternalType('array', $classes);
        $this->assertContains('ZendTest\Code\TestAsset\FooClass', $classes);
    }
    
    public function testFileScannerReturnsFunctionsWithoutScannerClass()
    {
        $fileScanner = new ScannerFile(__DIR__ . '/../TestAsset/functions.php');
        $fileScanner->scan();
        $functions = $fileScanner->getFunctions();
        $this->assertInternalType('array', $functions);
        $this->assertContains('ZendTest\Code\TestAsset\foo_bar', $functions);
    }
    
    public function testFileScannerReturnsClassesWithScannerClass()
    {
        $fileScanner = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $fileScanner->scan();
        $classes = $fileScanner->getClasses(true);
        $this->assertInternalType('array', $classes);
        $class = array_shift($classes);
        $this->assertInstanceOf('Zend\Code\Scanner\ScannerClass', $class);
    }
    
}

