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

use Zend\Filter\Compress\Rar as RarCompression;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class RarTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('rar')) {
            $this->markTestSkipped('This adapter needs the rar extension');
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
            dirname(__DIR__) . '/_files/zipextracted.txt',
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
        $filter  = new RarCompression(
            array(
                'archive'  => dirname(__DIR__) . '/_files/compressed.rar',
                'target'   => dirname(__DIR__) . '/_files/zipextracted.txt',
                'callback' => array(__CLASS__, 'rarCompress')
            )
        );

        $content = $filter->compress('compress me');
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.rar', $content);

        $content = $filter->decompress($content);
        $this->assertTrue($content);
        $content = file_get_contents(dirname(__DIR__) . '/_files/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Setting Options
     *
     * @return void
     */
    public function testRarGetSetOptions()
    {
        $filter = new RarCompression();
        $this->assertEquals(
            array(
                'archive'  => null,
                'callback' => null,
                'password' => null,
                'target'   => '.',
            ),
            $filter->getOptions()
        );

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
    public function testRarGetSetArchive()
    {
        $filter = new RarCompression();
        $this->assertEquals(null, $filter->getArchive());
        $filter->setArchive('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getArchive());
        $this->assertEquals('Testfile.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Password
     *
     * @return void
     */
    public function testRarGetSetPassword()
    {
        $filter = new RarCompression();
        $this->assertEquals(null, $filter->getPassword());
        $filter->setPassword('test');
        $this->assertEquals('test', $filter->getPassword());
        $this->assertEquals('test', $filter->getOptions('password'));
        $filter->setOptions(array('password' => 'test2'));
        $this->assertEquals('test2', $filter->getPassword());
        $this->assertEquals('test2', $filter->getOptions('password'));
    }

    /**
     * Setting Target
     *
     * @return void
     */
    public function testRarGetSetTarget()
    {
        $filter = new RarCompression();
        $this->assertEquals('.', $filter->getTarget());
        $filter->setTarget('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getTarget());
        $this->assertEquals('Testfile.txt', $filter->getOptions('target'));

        $this->setExpectedException('Zend\Filter\Exception\InvalidArgumentException', 'does not exist');
        $filter->setTarget('/unknown/path/to/file.txt');
    }

    /**
     * Setting Callback
     *
     * @return void
     */
    public function testSettingCallback()
    {
        $filter = new RarCompression();

        $callback = array(__CLASS__, 'rarCompress');
        $filter->setCallback($callback);
        $this->assertEquals($callback, $filter->getCallback());

    }

    public function testSettingCallbackThrowsExceptionOnMissingCallback()
    {
        $filter = new RarCompression();

        $this->setExpectedException('Zend\Filter\Exception\RuntimeException', 'No compression callback available');
        $filter->compress('test.txt');
    }

    public function testSettingCallbackThrowsExceptionOnInvalidCallback()
    {
        $filter = new RarCompression();

        $this->setExpectedException('Zend\Filter\Exception\InvalidArgumentException', 'Invalid callback provided');
        $filter->setCallback('invalidCallback');
    }

    /**
     * Compress to Archive
     *
     * @return void
     */
    public function testRarCompressFile()
    {
        $filter  = new RarCompression(
            array(
                'archive'  => dirname(__DIR__) . '/_files/compressed.rar',
                'target'   => dirname(__DIR__) . '/_files/zipextracted.txt',
                'callback' => array(__CLASS__, 'rarCompress')
            )
        );
        file_put_contents(dirname(__DIR__) . '/_files/zipextracted.txt', 'compress me');

        $content = $filter->compress(dirname(__DIR__) . '/_files/zipextracted.txt');
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.rar', $content);

        $content = $filter->decompress($content);
        $this->assertTrue($content);
        $content = file_get_contents(dirname(__DIR__) . '/_files/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Compress directory to Filename
     *
     * @return void
     */
    public function testRarCompressDirectory()
    {
        $filter  = new RarCompression(
            array(
                'archive'  => dirname(__DIR__) . '/_files/compressed.rar',
                'target'   => dirname(__DIR__) . '/_files/_compress',
                'callback' => array(__CLASS__, 'rarCompress')
            )
        );
        $content = $filter->compress(dirname(__DIR__) . '/_files/Compress');
        $this->assertEquals(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.rar', $content);

        mkdir(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . '_compress');
        $content = $filter->decompress($content);
        $this->assertTrue($content);

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
    public function testRarToString()
    {
        $filter = new RarCompression();
        $this->assertEquals('Rar', $filter->toString());
    }

    /**
     * Test callback for compression
     *
     * @return unknown
     */
    public static function rarCompress()
    {
        return true;
    }
}
