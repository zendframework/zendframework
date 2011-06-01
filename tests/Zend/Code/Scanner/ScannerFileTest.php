<?php

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\ScannerFile;

class ScannerFileTest extends \PHPUnit_Framework_TestCase
{
    
    public function testScannerFileReturnsNamespacesWithoutScannerClass()
    {
        $fileScanner = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $namespaces = $fileScanner->getNamespaces();
        $this->assertInternalType('array', $namespaces);
        $this->assertContains('ZendTest\Code\TestAsset', $namespaces);
    }
    
    public function testScannerFileReturnsClassesWithoutScannerClass()
    {
        $fileScanner = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $classes = $fileScanner->getClasses();
        $this->assertInternalType('array', $classes);
        $this->assertContains('ZendTest\Code\TestAsset\FooClass', $classes);
    }
    
    public function testScannerFileReturnsFunctionsWithoutScannerClass()
    {
        $fileScanner = new ScannerFile(__DIR__ . '/../TestAsset/functions.php');
        $functions = $fileScanner->getFunctions();
        $this->assertInternalType('array', $functions);
        $this->assertContains('ZendTest\Code\TestAsset\foo_bar', $functions);
    }
    
    public function testScannerFileReturnsClassesWithScannerClass()
    {
        $fileScanner = new ScannerFile(__DIR__ . '/../TestAsset/FooClass.php');
        $classes = $fileScanner->getClasses(true);
        $this->assertInternalType('array', $classes);
        $class = array_shift($classes);
        $this->assertInstanceOf('Zend\Code\Scanner\ScannerClass', $class);
    }
    
}

