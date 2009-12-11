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
    define('PHPUnit_MAIN_METHOD', 'Zend_Filter_Compress_Bz2Test::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Filter_Compress_Bz2
 */
require_once 'Zend/Filter/Compress/Bz2.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_Compress_Bz2Test extends PHPUnit_Framework_TestCase
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Filter_Compress_Bz2Test');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!extension_loaded('bz2')) {
            $this->markTestSkipped('This adapter needs the bz2 extension');
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
        $filter  = new Zend_Filter_Compress_Bz2();

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
    public function testBz2GetSetOptions()
    {
        $filter = new Zend_Filter_Compress_Bz2();
        $this->assertEquals(array('blocksize' => 4, 'archive' => null), $filter->getOptions());

        $this->assertEquals(4, $filter->getOptions('blocksize'));

        $this->assertNull($filter->getOptions('nooption'));

        $filter->setOptions(array('blocksize' => 6));
        $this->assertEquals(6, $filter->getOptions('blocksize'));

        $filter->setOptions(array('archive' => 'test.txt'));
        $this->assertEquals('test.txt', $filter->getOptions('archive'));

        $filter->setOptions(array('nooption' => 0));
        $this->assertNull($filter->getOptions('nooption'));
    }

    /**
     * Setting Options through constructor
     *
     * @return void
     */
    public function testBz2GetSetOptionsInConstructor()
    {
        $filter2= new Zend_Filter_Compress_Bz2(array('blocksize' => 8));
        $this->assertEquals(array('blocksize' => 8, 'archive' => null), $filter2->getOptions());
    }

    /**
     * Setting Blocksize
     *
     * @return void
     */
    public function testBz2GetSetBlocksize()
    {
        $filter = new Zend_Filter_Compress_Bz2();
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
    public function testBz2GetSetArchive()
    {
        $filter = new Zend_Filter_Compress_Bz2();
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
    public function testBz2CompressToFile()
    {
        $filter   = new Zend_Filter_Compress_Bz2();
        $archive = dirname(__FILE__) . '/../_files/compressed.bz2';
        $filter->setArchive($archive);

        $content = $filter->compress('compress me');
        $this->assertTrue($content);

        $filter2  = new Zend_Filter_Compress_Bz2();
        $content2 = $filter2->decompress($archive);
        $this->assertEquals('compress me', $content2);

        $filter3 = new Zend_Filter_Compress_Bz2();
        $filter3->setArchive($archive);
        $content3 = $filter3->decompress(null);
        $this->assertEquals('compress me', $content3);
    }

    /**
     * testing toString
     *
     * @return void
     */
    public function testBz2ToString()
    {
        $filter = new Zend_Filter_Compress_Bz2();
        $this->assertEquals('Bz2', $filter->toString());
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testBz2DecompressArchive()
    {
        $filter   = new Zend_Filter_Compress_Bz2();
        $archive = dirname(__FILE__) . '/../_files/compressed.bz2';
        $filter->setArchive($archive);

        $content = $filter->compress('compress me');
        $this->assertTrue($content);

        $filter2  = new Zend_Filter_Compress_Bz2();
        $content2 = $filter2->decompress($archive);
        $this->assertEquals('compress me', $content2);
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Filter_Compress_Bz2Test::main') {
    Zend_Filter_Compress_Bz2Test::main();
}
