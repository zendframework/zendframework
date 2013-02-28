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

use Zend\Code\Scanner\TokenArrayScanner;
use PHPUnit_Framework_TestCase as TestCase;

class TokenArrayScannerTest extends TestCase
{

    public function testScannerReturnsNamespaces()
    {
        $tokenScanner = new TokenArrayScanner(token_get_all(file_get_contents((__DIR__ . '/../TestAsset/FooClass.php'))));
        $this->assertTrue($tokenScanner->hasNamespace('ZendTest\Code\TestAsset'));
        $namespaces = $tokenScanner->getNamespaces();
        $this->assertInternalType('array', $namespaces);
        $this->assertContains('ZendTest\Code\TestAsset', $namespaces);
    }

    public function testScannerReturnsNamespacesInNotNamespacedClasses()
    {
        $tokenScanner = new TokenArrayScanner(token_get_all(file_get_contents((__DIR__ . '/../TestAsset/FooBarClass.php'))));
        $uses = $tokenScanner->getUses();
        $this->assertInternalType('array', $uses);
        $foundUses = array();
        foreach ($uses as $use) {
            $foundUses[] = $use['use'];
        }
        $this->assertContains('ArrayObject', $foundUses);
    }

    public function testScannerReturnsClassNames()
    {
        $tokenScanner = new TokenArrayScanner(token_get_all(file_get_contents((__DIR__ . '/../TestAsset/FooClass.php'))));
        $classes = $tokenScanner->getClassNames();
        $this->assertInternalType('array', $classes);
        $this->assertContains('ZendTest\Code\TestAsset\FooClass', $classes);
    }

    public function testScannerReturnsFunctions()
    {
        $tokenScanner = new TokenArrayScanner(token_get_all(file_get_contents((__DIR__ . '/../TestAsset/functions.php'))));
        $functions = $tokenScanner->getFunctionNames();
        $this->assertInternalType('array', $functions);
        $this->assertContains('ZendTest\Code\TestAsset\foo_bar', $functions);
    }

    public function testScannerReturnsClassScanner()
    {
        $tokenScanner = new TokenArrayScanner(token_get_all(file_get_contents((__DIR__ . '/../TestAsset/FooClass.php'))));
        $classes = $tokenScanner->getClasses(true);
        $this->assertInternalType('array', $classes);
        foreach ($classes as $class) {
            $this->assertInstanceOf('Zend\Code\Scanner\ClassScanner', $class);
        }
    }

    public function testScannerCanHandleMultipleNamespaceFile()
    {
        $tokenScanner = new TokenArrayScanner(token_get_all(file_get_contents((__DIR__ . '/../TestAsset/MultipleNamespaces.php'))));
        $this->assertEquals('ZendTest\Code\TestAsset\Baz', $tokenScanner->getClass('ZendTest\Code\TestAsset\Baz')->getName());
        $this->assertEquals('Foo', $tokenScanner->getClass('Foo')->getName());
    }

}
