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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Config\Writer;

use Zend\Config\Config,
    Zend\Config\Json as JsonConfig,
    Zend\Config\Writer\Json as JsonWriter;;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{
    protected $_tempName;

    public function setUp()
    {
        $this->_tempName = tempnam(__DIR__ . '/temp', 'tmp');
    }

    public function tearDown()
    {
        @unlink($this->_tempName);
    }

    public function testNoFilenameSet()
    {
        $writer = new JsonWriter(array('config' => new Config(array())));

        $this->setExpectedException('Zend\Config\Exception\InvalidArgumentException', 'No filename was set');
        $writer->write();
    }

    public function testNoConfigSet()
    {
        $writer = new JsonWriter(array('filename' => $this->_tempName));

        $this->setExpectedException('Zend\Config\Exception\InvalidArgumentException', 'No config was set');
        $writer->write();
    }

    public function testFileNotWritable()
    {
        $writer = new JsonWriter(array('config' => new Config(array()), 'filename' => '/../../../'));

        $this->setExpectedException('Zend\Config\Exception\RuntimeException', 'Could not write to file');
        $writer->write();
    }

    public function testWriteAndRead()
    {
        $config = new Config(array('default' => array('test' => 'foo')));

        $writer = new JsonWriter(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new JsonConfig($this->_tempName, null);

        $this->assertEquals('foo', $config->default->test);
    }

    public function testNoSection()
    {
        $config = new Config(array('test' => 'foo', 'test2' => array('test3' => 'bar')));

        $writer = new JsonWriter(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new JsonConfig($this->_tempName);

        $this->assertEquals('foo', $config->test);
        $this->assertEquals('bar', $config->test2->test3);
    }

    public function testWriteAndReadOriginalFile()
    {
        $config = new JsonConfig(__DIR__ . '/files/allsections.json', null, array('skip_extends' => true));

        $writer = new JsonWriter(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new JsonConfig($this->_tempName, null);
        $this->assertEquals('multi', $config->staging->one->two->three, var_export($config->toArray(), 1));

        $config = new JsonConfig($this->_tempName, null, array('skip_extends' => true));
        $this->assertFalse(isset($config->staging->one));
    }


    public function testWriteAndReadSingleSection()
    {
        $config = new JsonConfig(__DIR__ . '/files/allsections.json', 'staging', array('skip_extends' => true));

        $writer = new JsonWriter(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new JsonConfig($this->_tempName, null);

        $this->assertEquals('staging', $config->staging->hostname);
        $this->assertEquals('', $config->staging->debug);
        $this->assertEquals(null, @$config->production);
    }

    public function testArgumentOverride()
    {
        $config = new Config(array('default' => array('test' => 'foo')));

        $writer = new JsonWriter();
        $writer->write($this->_tempName, $config);

        $config = new JsonConfig($this->_tempName, null);

        $this->assertEquals('foo', $config->default->test);
    }

    public function testCanWritePrettyPrintedVersion()
    {
        $config = new JsonConfig(__DIR__ . '/files/allsections-pretty.json');

        $writer = new JsonWriter(array('config' => $config, 'filename' => $this->_tempName));
        $writer->setPrettyPrint(true);
        $writer->write();
        $testOutput     = file_get_contents($this->_tempName);
        $this->assertRegExp('/^\s+/m', $testOutput);
    }
}
