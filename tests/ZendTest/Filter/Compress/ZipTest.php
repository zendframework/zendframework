<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter\Compress;

use Zend\Filter\Compress\Zip as ZipCompression;

/**
 * @group      Zend_Filter
 */
class ZipTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('zip')) {
            $this->markTestSkipped('This adapter needs the zip extension');
        }

        $this->tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . str_replace('\\', '_', __CLASS__);

        $files = array(
            $this->tmp . '/compressed.zip',
            $this->tmp . '/zipextracted.txt',
            $this->tmp . '/zip.tmp',
            $this->tmp . '/_files/_compress/Compress/First/Second/zipextracted.txt',
            $this->tmp . '/_files/_compress/Compress/First/Second',
            $this->tmp . '/_files/_compress/Compress/First/zipextracted.txt',
            $this->tmp . '/_files/_compress/Compress/First',
            $this->tmp . '/_files/_compress/Compress/zipextracted.txt',
            $this->tmp . '/_files/_compress/Compress',
            $this->tmp . '/_files/_compress/zipextracted.txt',
            $this->tmp . '/_files/_compress'
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

        if (!file_exists($this->tmp . '/Compress/First/Second')) {
            mkdir($this->tmp . '/Compress/First/Second', 0777, true);
            file_put_contents($this->tmp . '/Compress/First/Second/zipextracted.txt', 'compress me');
            file_put_contents($this->tmp . '/Compress/First/zipextracted.txt', 'compress me');
            file_put_contents($this->tmp . '/Compress/zipextracted.txt', 'compress me');
        }
    }

    public function tearDown()
    {
        $files = array(
            $this->tmp . '/compressed.zip',
            $this->tmp . '/zipextracted.txt',
            $this->tmp . '/zip.tmp',
            $this->tmp . '/_compress/Compress/First/Second/zipextracted.txt',
            $this->tmp . '/_compress/Compress/First/Second',
            $this->tmp . '/_compress/Compress/First/zipextracted.txt',
            $this->tmp . '/_compress/Compress/First',
            $this->tmp . '/_compress/Compress/zipextracted.txt',
            $this->tmp . '/_compress/Compress',
            $this->tmp . '/_compress/zipextracted.txt',
            $this->tmp . '/_compress'
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

        if (!file_exists($this->tmp . '/Compress/First/Second')) {
            mkdir($this->tmp . '/Compress/First/Second', 0777, true);
            file_put_contents($this->tmp . '/Compress/First/Second/zipextracted.txt', 'compress me');
            file_put_contents($this->tmp . '/Compress/First/zipextracted.txt', 'compress me');
            file_put_contents($this->tmp . '/Compress/zipextracted.txt', 'compress me');
        }
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testBasicUsage()
    {
        if (!constant('TESTS_ZEND_FILTER_COMPRESS_ZIP_ENABLED')) {
            $this->markTestSkipped('ZIP compression tests are currently disabled');
        }

        $filter  = new ZipCompression(
            array(
                'archive' => $this->tmp . '/compressed.zip',
                'target'  => $this->tmp . '/zipextracted.txt'
            )
        );

        $content = $filter->compress('compress me');
        $this->assertEquals($this->tmp . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $content = $filter->decompress($content);
        $this->assertEquals($this->tmp . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents($this->tmp . '/zipextracted.txt');
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
        if (!constant('TESTS_ZEND_FILTER_COMPRESS_ZIP_ENABLED')) {
            $this->markTestSkipped('ZIP compression tests are currently disabled');
        }

        $filter  = new ZipCompression(
            array(
                'archive' => $this->tmp . '/compressed.zip',
                'target'  => $this->tmp . '/zipextracted.txt'
            )
        );
        file_put_contents($this->tmp . '/zipextracted.txt', 'compress me');

        $content = $filter->compress($this->tmp . '/zipextracted.txt');
        $this->assertEquals($this->tmp . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $content = $filter->decompress($content);
        $this->assertEquals($this->tmp . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents($this->tmp . '/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testCompressNonExistingTargetFile()
    {
        if (!constant('TESTS_ZEND_FILTER_COMPRESS_ZIP_ENABLED')) {
            $this->markTestSkipped('ZIP compression tests are currently disabled');
        }

        $filter  = new ZipCompression(
            array(
                'archive' => $this->tmp . '/compressed.zip',
                'target'  => $this->tmp
            )
        );

        $content = $filter->compress('compress me');
        $this->assertEquals($this->tmp . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $content = $filter->decompress($content);
        $this->assertEquals($this->tmp . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents($this->tmp . '/zip.tmp');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Compress directory to Archive
     *
     * @return void
     */
    public function testZipCompressDirectory()
    {
        if (!constant('TESTS_ZEND_FILTER_COMPRESS_ZIP_ENABLED')) {
            $this->markTestSkipped('ZIP compression tests are currently disabled');
        }

        $filter  = new ZipCompression(
            array(
                'archive' => $this->tmp . '/compressed.zip',
                'target'  => $this->tmp . '/_compress'
            )
        );
        $content = $filter->compress($this->tmp . '/Compress');
        $this->assertEquals($this->tmp . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        mkdir($this->tmp . DIRECTORY_SEPARATOR . '_compress');
        $content = $filter->decompress($content);
        $this->assertEquals($this->tmp . DIRECTORY_SEPARATOR . '_compress'
                            . DIRECTORY_SEPARATOR, $content);

        $base = $this->tmp . DIRECTORY_SEPARATOR . '_compress' . DIRECTORY_SEPARATOR . 'Compress' . DIRECTORY_SEPARATOR;
        $this->assertFileExists($base);
        $this->assertFileExists($base . 'zipextracted.txt');
        $this->assertFileExists($base . 'First' . DIRECTORY_SEPARATOR . 'zipextracted.txt');
        $this->assertFileExists($base . 'First' . DIRECTORY_SEPARATOR .
                          'Second' . DIRECTORY_SEPARATOR . 'zipextracted.txt');
        $content = file_get_contents($this->tmp . '/Compress/zipextracted.txt');
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
        if (!constant('TESTS_ZEND_FILTER_COMPRESS_ZIP_ENABLED')) {
            $this->markTestSkipped('ZIP compression tests are currently disabled');
        }

        $filter  = new ZipCompression(
            array(
                'archive' => $this->tmp . '/compressed.zip',
                'target'  => $this->tmp . '/_compress'
            )
        );

        $content = $filter->compress('compress me');
        $this->assertEquals($this->tmp . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $filter  = new ZipCompression(
            array(
                'archive' => $this->tmp . '/compressed.zip',
                'target'  => $this->tmp . '/_compress'
            )
        );
        $content = $filter->decompress($content);
        $this->assertEquals($this->tmp . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents($this->tmp . '/_compress');
        $this->assertEquals('compress me', $content);
    }

    /**
     * @group 6026
     *
     * @covers \Zend\Filter\Compress\Zip::decompress
     */
    public function testDecompressWhenNoArchieveInClass()
    {
        if (!constant('TESTS_ZEND_FILTER_COMPRESS_ZIP_ENABLED')) {
            $this->markTestSkipped('ZIP compression tests are currently disabled');
        }

        $filter  = new ZipCompression(
            array(
                'archive' => $this->tmp . '/compressed.zip',
                'target'  => $this->tmp . '/_compress'
            )
        );

        $content = $filter->compress('compress me');
        $this->assertEquals($this->tmp . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $filter  = new ZipCompression(
            array(
                'target'  => $this->tmp . '/_compress'
            )
        );
        $content = $filter->decompress($content);
        $this->assertEquals($this->tmp . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents($this->tmp . '/_compress');
        $this->assertEquals('compress me', $content);
    }
}
