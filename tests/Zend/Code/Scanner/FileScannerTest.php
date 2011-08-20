<?php

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\FileScanner,
    PHPUnit_Framework_TestCase as TestCase;

class FileScannerTest extends TestCase
{
    public function testFileScannerReturnsNamespacesWithoutClassScanner()
    {
        $fileScanner = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $namespaces  = $fileScanner->getNamespaces();
        $this->assertInternalType('array', $namespaces);
        $this->assertContains('ZendTest\Code\TestAsset', $namespaces);
    }
    
    public function testFileScannerReturnsClassesWithoutClassScanner()
    {
        $fileScanner = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $classes     = $fileScanner->getClasses();
        $this->assertInternalType('array', $classes);
        $this->assertContains('ZendTest\Code\TestAsset\FooClass', $classes);
    }
    
    public function testFileScannerReturnsFunctionsWithoutClassScanner()
    {
        $fileScanner = new FileScanner(__DIR__ . '/../TestAsset/functions.php');
        $functions   = $fileScanner->getFunctions();
        $this->assertInternalType('array', $functions);
        $this->assertContains('ZendTest\Code\TestAsset\foo_bar', $functions);
    }
    
    public function testFileScannerReturnsClassesWithClassScanner()
    {
        $fileScanner = new FileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $classes     = $fileScanner->getClasses(true);
        $this->assertInternalType('array', $classes);
        foreach ($classes as $class) {
            $this->assertInstanceOf('Zend\Code\Scanner\ClassScanner', $class);
        }
    }
}
