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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Filter_Compress_GzTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Filter_Compress_Gz
 */
require_once 'Zend/Filter/Compress/Gz.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_Compress_GzTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Filter_Compress_GzTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!extension_loaded('zlib')) {
            $this->markTestSkipped('This adapter needs the zlib extension');
        }
    }

    public function tearDown()
    {
        if (file_exists(dirname(__FILE__) . '/../_files/compressed.gz')) {
            unlink(dirname(__FILE__) . '/../_files/compressed.gz');
        }
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testBasicUsage()
    {
        $filter  = new Zend_Filter_Compress_Gz();

        $content = $filter->compress('compress me');
        $this->assertNotEquals('compress me', $content);

        $content = $filter->decompress($content);
        $this->assertEquals('compress me', $content);
    }

    /**
     * Setting Options
     *
     * @return void
     */
    public function testGzGetSetOptions()
    {
        $filter = new Zend_Filter_Compress_Gz();
        $this->assertEquals(array('mode' => 'compress', 'level' => 9, 'archive' => null), $filter->getOptions());

        $this->assertEquals(9, $filter->getOptions('level'));

        $this->assertNull($filter->getOptions('nooption'));
        $filter->setOptions(array('nooption' => 'foo'));
        $this->assertNull($filter->getOptions('nooption'));

        $filter->setOptions(array('level' => 6));
        $this->assertEquals(6, $filter->getOptions('level'));

        $filter->setOptions(array('mode' => 'deflate'));
        $this->assertEquals('deflate', $filter->getOptions('mode'));

        $filter->setOptions(array('archive' => 'test.txt'));
        $this->assertEquals('test.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Options through constructor
     *
     * @return void
     */
    public function testGzGetSetOptionsInConstructor()
    {
        $filter2= new Zend_Filter_Compress_Gz(array('level' => 8));
        $this->assertEquals(array('mode' => 'compress', 'level' => 8, 'archive' => null), $filter2->getOptions());
    }

    /**
     * Setting Level
     *
     * @return void
     */
    public function testGzGetSetLevel()
    {
        $filter = new Zend_Filter_Compress_Gz();
        $this->assertEquals(9, $filter->getLevel());
        $filter->setLevel(6);
        $this->assertEquals(6, $filter->getOptions('level'));

        try {
            $filter->setLevel(15);
            $this->fail('Exception expected');
        } catch(Zend_Filter_Exception $e) {
            $this->assertContains('must be between', $e->getMessage());
        }
    }

    /**
     * Setting Mode
     *
     * @return void
     */
    public function testGzGetSetMode()
    {
        $filter = new Zend_Filter_Compress_Gz();
        $this->assertEquals('compress', $filter->getMode());
        $filter->setMode('deflate');
        $this->assertEquals('deflate', $filter->getOptions('mode'));

        try {
            $filter->setMode('unknown');
            $this->fail('Exception expected');
        } catch(Zend_Filter_Exception $e) {
            $this->assertContains('mode not supported', $e->getMessage());
        }
    }

    /**
     * Setting Archive
     *
     * @return void
     */
    public function testGzGetSetArchive()
    {
        $filter = new Zend_Filter_Compress_Gz();
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
    public function testGzCompressToFile()
    {
        $filter   = new Zend_Filter_Compress_Gz();
        $archive = dirname(__FILE__) . '/../_files/compressed.gz';
        $filter->setArchive($archive);

        $content = $filter->compress('compress me');
        $this->assertTrue($content);

        $filter2  = new Zend_Filter_Compress_Gz();
        $content2 = $filter2->decompress($archive);
        $this->assertEquals('compress me', $content2);

        $filter3 = new Zend_Filter_Compress_Gz();
        $filter3->setArchive($archive);
        $content3 = $filter3->decompress(null);
        $this->assertEquals('compress me', $content3);
    }

    /**
     * Test deflate
     *
     * @return void
     */
    public function testGzDeflate()
    {
        $filter  = new Zend_Filter_Compress_Gz(array('mode' => 'deflate'));

        $content = $filter->compress('compress me');
        $this->assertNotEquals('compress me', $content);

        $content = $filter->decompress($content);
        $this->assertEquals('compress me', $content);
    }

    /**
     * testing toString
     *
     * @return void
     */
    public function testGzToString()
    {
        $filter = new Zend_Filter_Compress_Gz();
        $this->assertEquals('Gz', $filter->toString());
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Filter_Compress_GzTest::main') {
    Zend_Filter_Compress_GzTest::main();
}
