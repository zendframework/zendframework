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
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Config\Writer;

use \PHPUnit_Framework_TestCase as TestCase,
    \Zend\Config\Config;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
abstract class AbstractWriterTestCase extends TestCase
{
    /**
     * @var Reader
     */
    protected $reader;
    
    /**
     *
     * @var Writer
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
        @unlink($this->getTestAssetFileName());
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
