<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace ZendTest\Filter\Compress;

use Zend\Filter\Compress\Zip as ZipCompression;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class ZipTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('zip')) {
            $this->markTestSkipped('This adapter needs the zip extension');
        }

        $files = array(
            dirname(__DIR__) . '/_files/compressed.zip',
            dirname(__DIR__) . '/_files/zipextracted.txt',
            dirname(__DIR__) . '/_files/zip.tmp',
            dirname(__DIR__) . '/_files/_compress/Compress/First/Second/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress/Compress/First/Second',
            dirname(__DIR__) . '/_files/_compress/Compress/First/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress/Compress/First',
            dirname(__DIR__) . '/_files/_compress/Compress/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress/Compress',
            dirname(__DIR__) . '/_files/_compress/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress'
        );

        foreach ($files as $file) {
            if (file_exists($file)) {
                if (is_dir($file)) {
                    rmdir($file);
                } else {
                    unlink($file);
                }
            }
        }

        if (!file_exists(dirname(__DIR__) . '/_files/Compress/First/Second')) {
            mkdir(dirname(__DIR__) . '/_files/Compress/First/Second', 0777, true);
            file_put_contents(dirname(__DIR__) . '/_files/Compress/First/Second/zipextracted.txt', 'compress me');
            file_put_contents(dirname(__DIR__) . '/_files/Compress/First/zipextracted.txt', 'compress me');
            file_put_contents(dirname(__DIR__) . '/_files/Compress/zipextracted.txt', 'compress me');
        }
    }

    public function tearDown()
    {
        $files = array(
            dirname(__DIR__) . '/_files/compressed.zip',
            dirname(__DIR__) . '/_files/zipextracted.txt',
            dirname(__DIR__) . '/_files/zip.tmp',
            dirname(__DIR__) . '/_files/_compress/Compress/First/Second/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress/Compress/First/Second',
            dirname(__DIR__) . '/_files/_compress/Compress/First/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress/Compress/First',
            dirname(__DIR__) . '/_files/_compress/Compress/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress/Compress',
            dirname(__DIR__) . '/_files/_compress/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress'
        );

        foreach ($files as $file) {
            if (file_exists($file)) {
                if (is_dir($file)) {
                    rmdir($file);
                } else {
                    unlink($file);
                }
            }
        }

        if (!file_exists(dirname(__DIR__) . '/_files/Compress/First/Second')) {
            mkdir(dirname(__DIR__) . '/_files/Compress/First/Second', 0777, true);
            file_put_contents(dirname(__DIR__) . '/_files/Compress/First/Second/zipextracted.txt', 'compress me');
            file_put_contents(dirname(__DIR__) . '/_files/Compress/First/zipextracted.txt', 'compress me');
            file_put_contents(dirname(__DIR__) . '/_files/Compress/zipextracted.txt', 'compress me');
        }
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testBasicUsage()
    {
        $filter  = new ZipCompression(
            array(
                'archive' => dirname(__DIR__) . '/_files/compressed.zip',
                'target'  => dirname(__DIR__) . '/_files/zipextracted.txt'
            )
        );

        $content = $filter->compress('compress me');
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $content = $filter->decompress($content);
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(dirname(__DIR__) . '/_files/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Setting Options
     *
     * @return void
     */
    public function testZipGetSetOptions()
    {
        $filter = new ZipCompression();
        $this->assertEquals(array('archive' => null, 'target' => null), $filter->getOptions());

        $this->assertEquals(null, $filter->getOptions('archive'));

        $this->assertNull($filter->getOptions('nooption'));
        $filter->setOptions(array('nooption' => 'foo'));
        $this->assertNull($filter->getOptions('nooption'));

        $filter->setOptions(array('archive' => 'temp.txt'));
        $this->assertEquals('temp.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Archive
     *
     * @return void
     */
    public function testZipGetSetArchive()
    {
        $filter = new ZipCompression();
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
    public function testZipGetSetTarget()
    {
        $filter = new ZipCompression();
        $this->assertNull($filter->getTarget());
        $filter->setTarget('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getTarget());
        $this->assertEquals('Testfile.txt', $filter->getOptions('target'));

        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'does not exist');
        $filter->setTarget('/unknown/path/to/file.txt');
    }

    /**
     * Compress to Archive
     *
     * @return void
     */
    public function testZipCompressFile()
    {
        $filter  = new ZipCompression(
            array(
                'archive' => dirname(__DIR__) . '/_files/compressed.zip',
                'target'  => dirname(__DIR__) . '/_files/zipextracted.txt'
            )
        );
        file_put_contents(dirname(__DIR__) . '/_files/zipextracted.txt', 'compress me');

        $content = $filter->compress(dirname(__DIR__) . '/_files/zipextracted.txt');
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $content = $filter->decompress($content);
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(dirname(__DIR__) . '/_files/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testCompressNonExistingTargetFile()
    {
        $filter  = new ZipCompression(
            array(
                'archive' => dirname(__DIR__) . '/_files/compressed.zip',
                'target'  => dirname(__DIR__) . '/_files'
            )
        );

        $content = $filter->compress('compress me');
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $content = $filter->decompress($content);
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(dirname(__DIR__) . '/_files/zip.tmp');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Compress directory to Archive
     *
     * @return void
     */
    public function testZipCompressDirectory()
    {
        $filter  = new ZipCompression(
            array(
                'archive' => dirname(__DIR__) . '/_files/compressed.zip',
                'target'  => dirname(__DIR__) . '/_files/_compress'
            )
        );
        $content = $filter->compress(dirname(__DIR__) . '/_files/Compress');
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        mkdir(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . '_compress');
        $content = $filter->decompress($content);
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . '_compress'
                            . DIRECTORY_SEPARATOR, $content);

        $base = dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files'
              . DIRECTORY_SEPARATOR . '_compress' . DIRECTORY_SEPARATOR . 'Compress' . DIRECTORY_SEPARATOR;
        $this->assertTrue(file_exists($base));
        $this->assertTrue(file_exists($base . 'zipextracted.txt'));
        $this->assertTrue(file_exists($base . 'First' . DIRECTORY_SEPARATOR . 'zipextracted.txt'));
        $this->assertTrue(file_exists($base . 'First' . DIRECTORY_SEPARATOR .
                          'Second' . DIRECTORY_SEPARATOR . 'zipextracted.txt'));
        $content = file_get_contents(dirname(__DIR__) . '/_files/Compress/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * testing toString
     *
     * @return void
     */
    public function testZipToString()
    {
        $filter = new ZipCompression();
        $this->assertEquals('Zip', $filter->toString());
    }

    public function testDecompressWillThrowExceptionWhenDecompressingWithNoTarget()
    {
        $filter  = new ZipCompression(
            array(
                'archive' => dirname(__DIR__) . '/_files/compressed.zip',
                'target'  => dirname(__DIR__) . '/_files/_compress'
            )
        );

        $content = $filter->compress('compress me');
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $filter  = new ZipCompression(
            array(
                'archive' => dirname(__DIR__) . '/_files/compressed.zip',
                'target'  => dirname(__DIR__) . '/_files/_compress'
            )
        );
        $content = $filter->decompress($content);
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(dirname(__DIR__) . '/_files/_compress');
        $this->assertEquals('compress me', $content);
    }
}
