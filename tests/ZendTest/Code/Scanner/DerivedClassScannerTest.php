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

use Zend\Code\Scanner\DirectoryScanner;
use Zend\Code\Scanner\AggregateDirectoryScanner;

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
