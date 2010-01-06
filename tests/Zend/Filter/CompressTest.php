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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Filter_CompressTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Filter_Compress
 */
require_once 'Zend/Filter/Compress.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_CompressTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Filter_CompressTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!extension_loaded('bz2')) {
            $this->markTestSkipped('This filter is tested with the bz2 extension');
        }
    }

    public function tearDown()
    {
        if (file_exists(dirname(__FILE__) . '/../_files/compressed.bz2')) {
            unlink(dirname(__FILE__) . '/../_files/compressed.bz2');
        }
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testBasicUsage()
    {
        $filter  = new Zend_Filter_Compress('bz2');

        $text     = 'compress me';
        $compressed = $filter->filter($text);
        $this->assertNotEquals($text, $compressed);

        $decompressed = $filter->decompress($compressed);
        $this->assertEquals($text, $decompressed);
    }

    /**
     * Setting Options
     *
     * @return void
     */
    public function testGetSetAdapterOptionsInConstructor()
    {
        $filter = new Zend_Filter_Compress(array(
            'adapter' => 'bz2',
            'options' => array(
                'blocksize' => 6,
                'archive'   => 'test.txt',
            )
        ));

        $this->assertEquals(
            array('blocksize' => 6, 'archive' => 'test.txt'),
            $filter->getAdapterOptions()
        );

        $adapter = $filter->getAdapter();
        $this->assertEquals(6, $adapter->getBlocksize());
        $this->assertEquals('test.txt', $adapter->getArchive());
    }

    /**
     * Setting Options through constructor
     *
     * @return void
     */
    public function testGetSetAdapterOptions()
    {
        $filter = new Zend_Filter_Compress('bz2');
        $filter->setAdapterOptions(array(
            'blocksize' => 6,
            'archive'   => 'test.txt',
        ));
        $this->assertEquals(
            array('blocksize' => 6, 'archive'   => 'test.txt'),
            $filter->getAdapterOptions()
        );
        $adapter = $filter->getAdapter();
        $this->assertEquals(6, $adapter->getBlocksize());
        $this->assertEquals('test.txt', $adapter->getArchive());
    }

    /**
     * Setting Blocksize
     *
     * @return void
     */
    public function testGetSetBlocksize()
    {
        $filter = new Zend_Filter_Compress('bz2');
        $this->assertEquals(4, $filter->getBlocksize());
        $filter->setBlocksize(6);
        $this->assertEquals(6, $filter->getOptions('blocksize'));

        try {
            $filter->setBlocksize(15);
            $this->fail('Exception expected');
        } catch(Zend_Filter_Exception $e) {
            $this->assertContains('must be between', $e->getMessage());
        }
    }

    /**
     * Setting Archive
     *
     * @return void
     */
    public function testGetSetArchive()
    {
        $filter = new Zend_Filter_Compress('bz2');
        $this->assertEquals(null, $filter->getArchive());
        $filter->setArchive('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getArchive());
        $this->assertEquals('Testfile.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Archive
     *
     * @return void
     */
    public function testCompressToFile()
    {
        $filter   = new Zend_Filter_Compress('bz2');
        $archive = dirname(__FILE__) . '/../_files/compressed.bz2';
        $filter->setArchive($archive);

        $content = $filter->filter('compress me');
        $this->assertTrue($content);

        $filter2  = new Zend_Filter_Compress('bz2');
        $content2 = $filter2->decompress($archive);
        $this->assertEquals('compress me', $content2);

        $filter3 = new Zend_Filter_Compress('bz2');
        $filter3->setArchive($archive);
        $content3 = $filter3->decompress(null);
        $this->assertEquals('compress me', $content3);
    }

    /**
     * testing toString
     *
     * @return void
     */
    public function testToString()
    {
        $filter = new Zend_Filter_Compress('bz2');
        $this->assertEquals('Bz2', $filter->toString());
    }

    /**
     * testing getAdapter
     *
     * @return void
     */
    public function testGetAdapter()
    {
        $filter = new Zend_Filter_Compress('bz2');
        $adapter = $filter->getAdapter();
        $this->assertTrue($adapter instanceof Zend_Filter_Compress_CompressInterface);
        $this->assertEquals('Bz2', $filter->getAdapterName());
    }

    /**
     * Setting Adapter
     *
     * @return void
     */
    public function testSetAdapter()
    {
        if (!extension_loaded('zlib')) {
            $this->markTestSkipped('This filter is tested with the zlib extension');
        }

        $filter = new Zend_Filter_Compress();
        $this->assertEquals('Gz', $filter->getAdapterName());

        try {
            $filter->setAdapter('Zend_Filter');
            $adapter = $filter->getAdapter();
            $this->fail('Invalid adapter should fail when retrieved');
        } catch (Zend_Filter_Exception $e) {
            $this->assertContains('does not implement', $e->getMessage());
        }
    }

    /**
     * Decompress archiv
     *
     * @return void
     */
    public function testDecompressArchive()
    {
        $filter   = new Zend_Filter_Compress('bz2');
        $archive = dirname(__FILE__) . '/../_files/compressed.bz2';
        $filter->setArchive($archive);

        $content = $filter->filter('compress me');
        $this->assertTrue($content);

        $filter2  = new Zend_Filter_Compress('bz2');
        $content2 = $filter2->decompress($archive);
        $this->assertEquals('compress me', $content2);
    }

    /**
     * Setting invalid method
     *
     * @return void
     */
    public function testInvalidMethod()
    {
        $filter = new Zend_Filter_Compress();
        try {
            $filter->invalidMethod();
            $this->fail('Exception expected');
        } catch (Zend_Exception $e) {
            $this->assertContains('Unknown method', $e->getMessage());
        }
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Filter_CompressTest::main') {
    Zend_Filter_CompressTest::main();
}
