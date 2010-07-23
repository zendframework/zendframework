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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Config\Writer;

use \Zend\Config\Writer\Xml,
    \Zend\Config\Config,
    \Zend\Config\Xml as XmlConfig;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
class XmlTest extends \PHPUnit_Framework_TestCase
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
        $writer = new Xml(array('config' => new Config(array())));

        $this->setExpectedException('\\Zend\\Config\\Exception', 'No filename was set');
        $writer->write();
    }

    public function testNoConfigSet()
    {
        $writer = new Xml(array('filename' => $this->_tempName));

        $this->setExpectedException('\\Zend\\Config\\Exception', 'No config was set');
        $writer->write();
    }

    public function testFileNotWritable()
    {
        $writer = new Xml(array('config' => new Config(array()), 'filename' => '/../../../'));

        $this->setExpectedException('\\Zend\\Config\\Exception', 'Could not write to file');
        $writer->write();
    }

    public function testWriteAndRead()
    {
        $config = new Config(array('default' => array('test' => 'foo')));

        $writer = new Xml(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new XmlConfig($this->_tempName, null);

        $this->assertEquals('foo', $config->default->test);
    }

    public function testNoSection()
    {
        $config = new Config(array('test' => 'foo', 'test2' => array('test3' => 'bar')));

        $writer = new Xml(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new XmlConfig($this->_tempName, null);

        $this->assertEquals('foo', $config->test);
        $this->assertEquals('bar', $config->test2->test3);
    }

    public function testWriteAndReadOriginalFile()
    {
        $config = new XmlConfig(__DIR__ . '/files/allsections.xml', null, array('skipExtends' => true));

        $writer = new Xml(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new XmlConfig($this->_tempName, null);
        $this->assertEquals('multi', $config->staging->one->two->three);

        $config = new XmlConfig($this->_tempName, null, array('skipExtends' => true));
        $this->assertFalse(isset($config->staging->one));
    }

    public function testWriteAndReadSingleSection()
    {
        $config = new XmlConfig(__DIR__ . '/files/allsections.xml', 'staging', array('skipExtends' => true));

        $writer = new Xml(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new XmlConfig($this->_tempName, null);

        $this->assertEquals('staging', $config->staging->hostname);
        $this->assertEquals('false', $config->staging->debug);
        $this->assertEquals(null, @$config->production);
    }

    /**
     * @group ZF-6773
     */
    public function testWriteMultidimensionalArrayWithNumericKeys()
    {
        $writer = new Xml;
        $writer->write($this->_tempName, new Config(array(
            'notification' => array(
                'adress' => array(
                    0 => array(
                        'name' => 'Matthew',
                        'mail' => 'matthew@example.com'
                    ),
                    1 => array(
                        'name' => 'Thomas',
                        'mail' => 'thomas@example.com'
                    )
                )
            )
        )));
    }

    public function testNumericArray()
    {
        $config = new Config(array('foo' => array('bar' => array(1 => 'a', 2 => 'b', 5 => 'c'))));

        $writer = new Xml(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new XmlConfig($this->_tempName, null);

        $this->assertEquals('a', $config->foo->bar->{0});
        $this->assertEquals('b', $config->foo->bar->{1});
        $this->assertEquals('c', $config->foo->bar->{2});
    }

    public function testMixedArrayFailure()
    {
        $config = new Config(array('foo' => array('bar' => array('a', 'b', 'c' => 'd'))));

        $this->setExpectedException('\\Zend\\Config\\Exception', 'Mixing of string and numeric keys is not allowed');
        $writer = new Xml(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();
    }

    public function testArgumentOverride()
    {
        $config = new Config(array('default' => array('test' => 'foo')));

        $writer = new Xml();
        $writer->write($this->_tempName, $config);

        $config = new XmlConfig($this->_tempName, null);

        $this->assertEquals('foo', $config->default->test);
    }

    /**
     * @group ZF-8234
     */
    public function testRender()
    {
        $config = new Config(array('test' => 'foo', 'bar' => array(0 => 'baz', 1 => 'foo')));

        $writer = new Xml();
        $configString = $writer->setConfig($config)->render();

        $expected = <<<ECS
<?xml version="1.0"?>
<zend-config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
  <test>foo</test>
  <bar>baz</bar>
  <bar>foo</bar>
</zend-config>

ECS;

        $this->assertEquals($expected, $configString);
    }
}
