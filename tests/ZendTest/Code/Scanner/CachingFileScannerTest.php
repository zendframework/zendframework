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

use Zend\Code\Scanner\CachingFileScanner;
use Zend\Code\Annotation\AnnotationManager;

class CachingFileScannerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        CachingFileScanner::clearCache();
    }

    public function testCachingFileScannerWillUseSameInternalFileScannerWithMatchingFileNameAnAnnotationManagerObject()
    {
        CachingFileScanner::clearCache();

        // single entry, based on file
        $cfs1 = new CachingFileScanner(__DIR__ . '/../TestAsset/BarClass.php');
        $this->assertContains('ZendTest\Code\TestAsset\BarClass', $cfs1->getClassNames());
        $this->assertEquals(1, $this->getCacheCount($cfs1));

        // ensure same class is used internally
        $cfs2 = new CachingFileScanner(__DIR__ . '/../TestAsset/BarClass.php');
        $this->assertEquals(1, $this->getCacheCount($cfs2));
        $this->assertSameInternalFileScanner($cfs1, $cfs2);

        // ensure
        $cfs3 = new CachingFileScanner(__DIR__ . '/../TestAsset/FooClass.php');
        $this->assertEquals(2, $this->getCacheCount($cfs3));
        $this->assertDifferentInternalFileScanner($cfs2, $cfs3);

        $annoManager = new AnnotationManager();
        $cfs4 = new CachingFileScanner(__DIR__ . '/../TestAsset/FooClass.php', $annoManager);
        $this->assertEquals(3, $this->getCacheCount($cfs4));
        $this->assertDifferentInternalFileScanner($cfs3, $cfs4);

        $cfs5 = new CachingFileScanner(__DIR__ . '/../TestAsset/FooClass.php', $annoManager);
        $this->assertEquals(3, $this->getCacheCount($cfs5));
        $this->assertSameInternalFileScanner($cfs4, $cfs5);

        $cfs6 = new CachingFileScanner(__DIR__ . '/../TestAsset/BarClass.php', $annoManager);
        $this->assertEquals(4, $this->getCacheCount($cfs6));
        $this->assertDifferentInternalFileScanner($cfs5, $cfs6);
    }

    protected function getCacheCount(CachingFileScanner $cfs)
    {
        $r = new \ReflectionObject($cfs);
        $cacheProp = $r->getProperty('cache');
        $cacheProp->setAccessible(true);
        return count($cacheProp->getValue($cfs));
    }

    protected function assertSameInternalFileScanner(CachingFileScanner $one, CachingFileScanner $two)
    {
        $rOne = new \ReflectionObject($one);
        $fileScannerPropOne = $rOne->getProperty('fileScanner');
        $fileScannerPropOne->setAccessible(true);
        $rTwo = new \ReflectionObject($two);
        $fileScannerPropTwo = $rTwo->getProperty('fileScanner');
        $fileScannerPropTwo->setAccessible(true);
        $this->assertSame($fileScannerPropOne->getValue($one), $fileScannerPropTwo->getValue($two));
    }

    protected function assertDifferentInternalFileScanner(CachingFileScanner $one, CachingFileScanner $two)
    {
        $rOne = new \ReflectionObject($one);
        $fileScannerPropOne = $rOne->getProperty('fileScanner');
        $fileScannerPropOne->setAccessible(true);
        $rTwo = new \ReflectionObject($two);
        $fileScannerPropTwo = $rTwo->getProperty('fileScanner');
        $fileScannerPropTwo->setAccessible(true);
        $this->assertNotSame($fileScannerPropOne->getValue($one), $fileScannerPropTwo->getValue($two));
    }

}
