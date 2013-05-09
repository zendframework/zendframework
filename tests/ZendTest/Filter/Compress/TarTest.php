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

use Zend\Filter\Compress\Tar as TarCompression;
use Zend\Loader\StandardAutoloader;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class TarTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('Archive_Tar')) {
            $autoloader = new StandardAutoloader();
            $autoloader->setFallbackAutoloader(true);
            if (!$autoloader->autoload('Archive_Tar')) {
                $this->markTestSkipped('This filter needs PEARs Archive_Tar');
            }
        }

        $files = array(
            dirname(__DIR__) . '/_files/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress/Compress/First/Second/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress/Compress/First/Second',
            dirname(__DIR__) . '/_files/_compress/Compress/First/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress/Compress/First',
            dirname(__DIR__) . '/_files/_compress/Compress/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress/Compress',
            dirname(__DIR__) . '/_files/_compress/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress',
            dirname(__DIR__) . '/_files/compressed.tar'
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
            dirname(__DIR__) . '/_files/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress/Compress/First/Second/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress/Compress/First/Second',
            dirname(__DIR__) . '/_files/_compress/Compress/First/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress/Compress/First',
            dirname(__DIR__) . '/_files/_compress/Compress/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress/Compress',
            dirname(__DIR__) . '/_files/_compress/zipextracted.txt',
            dirname(__DIR__) . '/_files/_compress',
            dirname(__DIR__) . '/_files/compressed.tar'
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
        $filter  = new TarCompression(
            array(
                'archive'  => dirname(__DIR__) . '/_files/compressed.tar',
                'target'   => dirname(__DIR__) . '/_files/zipextracted.txt'
            )
        );

        $content = $filter->compress('compress me');
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.tar', $content);

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
    public function testTarGetSetOptions()
    {
        $filter = new TarCompression();
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
        $filter = new TarCompression();
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
        $filter = new TarCompression();
        $this->assertEquals('.', $filter->getTarget());
        $filter->setTarget('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getTarget());
        $this->assertEquals('Testfile.txt', $filter->getOptions('target'));

        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'does not exist');
        $filter->setTarget('/unknown/path/to/file.txt');
    }

    /**
     * Setting Archive
     *
     * @return void
     */
    public function testTarCompressToFile()
    {
        $filter  = new TarCompression(
            array(
                'archive'  => dirname(__DIR__) . '/_files/compressed.tar',
                'target'   => dirname(__DIR__) . '/_files/zipextracted.txt'
            )
        );
        file_put_contents(dirname(__DIR__) . '/_files/zipextracted.txt', 'compress me');

        $content = $filter->compress(dirname(__DIR__) . '/_files/zipextracted.txt');
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.tar', $content);

        $content = $filter->decompress($content);
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(dirname(__DIR__) . '/_files/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Compress directory to Filename
     *
     * @return void
     */
    public function testTarCompressDirectory()
    {
        $filter  = new TarCompression(
            array(
                'archive'  => dirname(__DIR__) . '/_files/compressed.tar',
                'target'   => dirname(__DIR__) . '/_files/_compress'
            )
        );
        $content = $filter->compress(dirname(__DIR__) . '/_files/Compress');
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.tar', $content);
    }

    /**
     * testing toString
     *
     * @return void
     */
    public function testTarToString()
    {
        $filter = new TarCompression();
        $this->assertEquals('Tar', $filter->toString());
    }
}
