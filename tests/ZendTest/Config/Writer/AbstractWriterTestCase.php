<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Config\Writer;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Config\Config;

/**
 * @group      Zend_Config
 */
abstract class AbstractWriterTestCase extends TestCase
{
    /**
     * @var \Zend\Config\Reader\ReaderInterface
     */
    protected $reader;

    /**
     *
     * @var \Zend\Config\Writer\WriterInterface
     */
    protected $writer;

    /**
     *
     * @var string
     */
    protected $tmpfile;

    /**
     * Get test asset name for current test case.
     *
     * @return string
     */
    protected function getTestAssetFileName()
    {
        if (empty($this->tmpfile)) {
            $this->tmpfile = tempnam(sys_get_temp_dir(), 'zend-config-writer');
        }
        return $this->tmpfile;
    }

    public function tearDown()
    {
        if (file_exists($this->getTestAssetFileName())) {
            if (!is_writable($this->getTestAssetFileName())) {
                chmod($this->getTestAssetFileName(), 0777);
            }
            @unlink($this->getTestAssetFileName());
        }
    }

    public function testNoFilenameSet()
    {
        $this->setExpectedException('Zend\Config\Exception\InvalidArgumentException', 'No file name specified');
        $this->writer->toFile('', '');
    }

    public function testFileNotValid()
    {
        $this->setExpectedException('Zend\Config\Exception\RuntimeException');
        $this->writer->toFile('.', new Config(array()));
    }

    public function testFileNotWritable()
    {
        $this->setExpectedException('Zend\Config\Exception\RuntimeException');
        chmod($this->getTestAssetFileName(), 0444);
        $this->writer->toFile($this->getTestAssetFileName(), new Config(array()));
    }

    public function testWriteAndRead()
    {
        $config = new Config(array('default' => array('test' => 'foo')));

        $this->writer->toFile($this->getTestAssetFileName(), $config);

        $config = $this->reader->fromFile($this->getTestAssetFileName());

        $this->assertEquals('foo', $config['default']['test']);
    }
}
