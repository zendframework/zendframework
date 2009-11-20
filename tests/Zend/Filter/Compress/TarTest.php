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
    define('PHPUnit_MAIN_METHOD', 'Zend_Filter_Compress_TarTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Filter_Compress_Tar
 */
require_once 'Zend/Filter/Compress/Tar.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_Compress_TarTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Filter_Compress_TarTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!class_exists('Archive_Tar')) {
            require_once 'Zend/Loader.php';
            try {
                Zend_Loader::loadClass('Archive_Tar');
            } catch (Zend_Exception $e) {
                $this->markTestSkipped('This filter needs PEARs Archive_Tar');
            }
        }

        $files = array(
            dirname(__FILE__) . '/../_files/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress/Compress/First/Second/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress/Compress/First/Second',
            dirname(__FILE__) . '/../_files/_compress/Compress/First/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress/Compress/First',
            dirname(__FILE__) . '/../_files/_compress/Compress/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress/Compress',
            dirname(__FILE__) . '/../_files/_compress/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress',
            dirname(__FILE__) . '/../_files/compressed.tar'
        );

        foreach($files as $file) {
            if (file_exists($file)) {
                if (is_dir($file)) {
                    rmdir($file);
                } else {
                    unlink($file);
                }
            }
        }

        if (!file_exists(dirname(__FILE__) . '/../_files/Compress/First/Second')) {
            mkdir(dirname(__FILE__) . '/../_files/Compress/First/Second', 0777, true);
            file_put_contents(dirname(__FILE__) . '/../_files/Compress/First/Second/zipextracted.txt', 'compress me');
            file_put_contents(dirname(__FILE__) . '/../_files/Compress/First/zipextracted.txt', 'compress me');
            file_put_contents(dirname(__FILE__) . '/../_files/Compress/zipextracted.txt', 'compress me');
        }
    }

    public function tearDown()
    {
        $files = array(
            dirname(__FILE__) . '/../_files/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress/Compress/First/Second/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress/Compress/First/Second',
            dirname(__FILE__) . '/../_files/_compress/Compress/First/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress/Compress/First',
            dirname(__FILE__) . '/../_files/_compress/Compress/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress/Compress',
            dirname(__FILE__) . '/../_files/_compress/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress',
            dirname(__FILE__) . '/../_files/compressed.tar'
        );

        foreach($files as $file) {
            if (file_exists($file)) {
                if (is_dir($file)) {
                    rmdir($file);
                } else {
                    unlink($file);
                }
            }
        }

        if (!file_exists(dirname(__FILE__) . '/../_files/Compress/First/Second')) {
            mkdir(dirname(__FILE__) . '/../_files/Compress/First/Second', 0777, true);
            file_put_contents(dirname(__FILE__) . '/../_files/Compress/First/Second/zipextracted.txt', 'compress me');
            file_put_contents(dirname(__FILE__) . '/../_files/Compress/First/zipextracted.txt', 'compress me');
            file_put_contents(dirname(__FILE__) . '/../_files/Compress/zipextracted.txt', 'compress me');
        }
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testBasicUsage()
    {
        $filter  = new Zend_Filter_Compress_Tar(
            array(
                'archive'  => dirname(__FILE__) . '/../_files/compressed.tar',
                'target'   => dirname(__FILE__) . '/../_files/zipextracted.txt'
            )
        );

        $content = $filter->compress('compress me');
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.tar', $content);

        $content = $filter->decompress($content);
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(dirname(__FILE__) . '/../_files/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Setting Options
     *
     * @return void
     */
    public function testTarGetSetOptions()
    {
        $filter = new Zend_Filter_Compress_Tar();
        $this->assertEquals(
            array(
                'archive' => null,
                'target'  => '.',
                'mode'    => null),
            $filter->getOptions()
        );

        $this->assertEquals(null, $filter->getOptions('archive'));

        $this->assertNull($filter->getOptions('nooption'));
        $filter->setOptions(array('nooptions' => 'foo'));
        $this->assertNull($filter->getOptions('nooption'));

        $filter->setOptions(array('archive' => 'temp.txt'));
        $this->assertEquals('temp.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Archive
     *
     * @return void
     */
    public function testTarGetSetArchive()
    {
        $filter = new Zend_Filter_Compress_Tar();
        $this->assertEquals(null, $filter->getArchive());
        $filter->setArchive('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getArchive());
        $this->assertEquals('Testfile.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Target
     *
     * @return void
     */
    public function testTarGetSetTarget()
    {
        $filter = new Zend_Filter_Compress_Tar();
        $this->assertEquals('.', $filter->getTarget());
        $filter->setTarget('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getTarget());
        $this->assertEquals('Testfile.txt', $filter->getOptions('target'));

        try {
            $filter->setTarget('/unknown/path/to/file.txt');
            $this->fail('Exception expected');
        } catch(Zend_Filter_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }
    }

    /**
     * Setting Archive
     *
     * @return void
     */
    public function testTarCompressToFile()
    {
        $filter  = new Zend_Filter_Compress_Tar(
            array(
                'archive'  => dirname(__FILE__) . '/../_files/compressed.tar',
                'target'   => dirname(__FILE__) . '/../_files/zipextracted.txt'
            )
        );
        file_put_contents(dirname(__FILE__) . '/../_files/zipextracted.txt', 'compress me');

        $content = $filter->compress(dirname(__FILE__) . '/../_files/zipextracted.txt');
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.tar', $content);

        $content = $filter->decompress($content);
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'
                            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(dirname(__FILE__) . '/../_files/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Compress directory to Filename
     *
     * @return void
     */
    public function testTarCompressDirectory()
    {
        $filter  = new Zend_Filter_Compress_Tar(
            array(
                'archive'  => dirname(__FILE__) . '/../_files/compressed.tar',
                'target'   => dirname(__FILE__) . '/../_files/_compress'
            )
        );
        $content = $filter->compress(dirname(__FILE__) . '/../_files/Compress');
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.tar', $content);
    }

    /**
     * testing toString
     *
     * @return void
     */
    public function testTarToString()
    {
        $filter = new Zend_Filter_Compress_Tar();
        $this->assertEquals('Tar', $filter->toString());
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Filter_Compress_TarTest::main') {
    Zend_Filter_Compress_TarTest::main();
}
