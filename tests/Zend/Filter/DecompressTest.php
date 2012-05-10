<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\Decompress as DecompressFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DecompressTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('bz2')) {
            $this->markTestSkipped('This filter is tested with the bz2 extension');
        }
    }

    public function tearDown()
    {
        if (file_exists(__DIR__ . '/../_files/compressed.bz2')) {
            unlink(__DIR__ . '/../_files/compressed.bz2');
        }
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testBasicUsage()
    {
        if (version_compare(phpversion(), '5.4', '>=')) {
            $this->markTestIncomplete('Code to test is not compatible with PHP 5.4 ');
        }

        $filter  = new DecompressFilter('bz2');

        $text       = 'compress me';
        $compressed = $filter->compress($text);
        $this->assertNotEquals($text, $compressed);

        $decompressed = $filter($compressed);
        $this->assertEquals($text, $decompressed);
    }

    /**
     * Setting Archive
     *
     * @return void
     */
    public function testCompressToFile()
    {
        $filter   = new DecompressFilter('bz2');
        $archive = __DIR__ . '/../_files/compressed.bz2';
        $filter->setArchive($archive);

        $content = $filter->compress('compress me');
        $this->assertTrue($content);

        $filter2  = new DecompressFilter('bz2');
        $content2 = $filter2($archive);
        $this->assertEquals('compress me', $content2);

        $filter3 = new DecompressFilter('bz2');
        $filter3->setArchive($archive);
        $content3 = $filter3(null);
        $this->assertEquals('compress me', $content3);
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testDecompressArchive()
    {
        $filter   = new DecompressFilter('bz2');
        $archive = __DIR__ . '/../_files/compressed.bz2';
        $filter->setArchive($archive);

        $content = $filter->compress('compress me');
        $this->assertTrue($content);

        $filter2  = new DecompressFilter('bz2');
        $content2 = $filter2($archive);
        $this->assertEquals('compress me', $content2);
    }
}
