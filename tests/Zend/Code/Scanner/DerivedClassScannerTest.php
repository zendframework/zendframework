<?php

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\DirectoryScanner;
use Zend\Code\Scanner\AggregateDirectoryScanner;
use Zend\Code\Scanner\DerivedClassScanner;

class DerivedClassScannerTest extends \PHPUnit_Framework_TestCase
{
    
    public function testCreatesClass()
    {
        $ds = new DirectoryScanner();
        $ds->addDirectory(__DIR__ . '/TestAsset');
        $ads = new AggregateDirectoryScanner();
        $ads->addDirectoryScanner($ds);
        $c = $ads->getClass('ZendTest\Code\Scanner\TestAsset\MapperExample\RepositoryB');
        $this->assertEquals('ZendTest\Code\Scanner\TestAsset\MapperExample\RepositoryB', $c->getName());
    }
    
    
}
