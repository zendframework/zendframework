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

use \Zend\Config\Writer\Ini,
    \Zend\Config\Config,
    \Zend\Config\Ini as IniConfig;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
class IniTest extends \PHPUnit_Framework_TestCase
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
        $writer = new Ini(array('config' => new Config(array())));
        $this->setExpectedException('\\Zend\\Config\\Exception', 'No filename was set');
        $writer->write();
    }

    public function testNoConfigSet()
    {
        $writer = new Ini(array('filename' => $this->_tempName));
        $this->setExpectedException('\\Zend\\Config\\Exception', 'No config was set');
        $writer->write();
    }

    public function testFileNotWritable()
    {
        $writer = new Ini(array('config' => new Config(array()), 'filename' => '/../../../'));

        $this->setExpectedException('\\Zend\\Config\\Exception', 'Could not write to file');
        $writer->write();
    }

    public function testWriteAndRead()
    {
        $config = new Config(array('default' => array('test' => 'foo')));

        $writer = new Ini(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new IniConfig($this->_tempName, null);

        $this->assertEquals('foo', $config->default->test);
    }

    public function testNoSection()
    {
        $config = new Config(array('test' => 'foo', 'test2' => array('test3' => 'bar')));

        $writer = new Ini(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new IniConfig($this->_tempName, null);

        $this->assertEquals('foo', $config->test);
        $this->assertEquals('bar', $config->test2->test3);
    }

    public function testWriteAndReadOriginalFile()
    {
        $config = new IniConfig(__DIR__ . '/files/allsections.ini', null, array('skipExtends' => true));

        $writer = new Ini(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new IniConfig($this->_tempName, null);
        $this->assertEquals('multi', $config->staging->one->two->three);

        $config = new IniConfig($this->_tempName, null, array('skipExtends' => true));
        $this->assertFalse(isset($config->staging->one));
    }


    public function testWriteAndReadSingleSection()
    {
        $config = new IniConfig(__DIR__ . '/files/allsections.ini', 'staging', array('skipExtends' => true));

        $writer = new Ini(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new IniConfig($this->_tempName, null);

        $this->assertEquals('staging', $config->staging->hostname);
        $this->assertEquals('', $config->staging->debug);
        $this->assertEquals(null, @$config->production);
    }

    public function testArgumentOverride()
    {
        $config = new Config(array('default' => array('test' => 'foo')));

        $writer = new Ini();
        $writer->write($this->_tempName, $config);

        $config = new IniConfig($this->_tempName, null);

        $this->assertEquals('foo', $config->default->test);
    }

    /**
     * @group ZF-8234
     */
    public function testRender()
    {
        $config = new Config(array('test' => 'foo', 'bar' => array(0 => 'baz', 1 => 'foo')));

        $writer = new Ini();
        $iniString = $writer->setConfig($config)->render();

        $expected = <<<ECS
test = "foo"
[bar]
0 = "baz"
1 = "foo"


ECS;
        $this->assertEquals($expected, $iniString);
    }

    public function testRenderWithoutSections()
    {
        $config = new Config(array('test' => 'foo', 'test2' => array('test3' => 'bar')));

        $writer = new Ini();
        $writer->setRenderWithoutSections();
        $iniString = $writer->setConfig($config)->render();

        $expected = <<<ECS
test = "foo"
test2.test3 = "bar"

ECS;
        $this->assertEquals($expected, $iniString);
    }

    public function testRenderWithoutSections2()
    {
        $config = new IniConfig(__DIR__ . '/files/allsections.ini', null, array('skipExtends' => true));

        $writer = new Ini();
        $writer->setRenderWithoutSections();
        $iniString = $writer->setConfig($config)->render();

        $expected = <<<ECS
all.hostname = "all"
all.name = "thisname"
all.db.host = "127.0.0.1"
all.db.user = "username"
all.db.pass = "password"
all.db.name = "live"
all.one.two.three = "multi"
staging.hostname = "staging"
staging.db.name = "dbstaging"
staging.debug = ""
debug.hostname = "debug"
debug.debug = "1"
debug.values.changed = "1"
debug.db.name = "dbdebug"
debug.special.no = ""
debug.special.null = ""
debug.special.false = ""
other_staging.only_in = "otherStaging"
other_staging.db.pass = "anotherpwd"

ECS;
        $this->assertEquals($expected, $iniString);
    }

    /**
     * @group ZF-6521
     */
    public function testNoDoubleQuoutesInValue()
    {
        $config = new Config(array('default' => array('test' => 'fo"o')));

        $this->setExpectedException('\\Zend\\Config\\Exception', 'Value can not contain double quotes');
        $writer = new Ini(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();
    }
    
    /**
     * @group ZF-6289
     */
    public function testZF6289_NonSectionElementsAndSectionJumbling()
    {
        $config = new \Zend\Config\Config(array(
            'one'   => 'element',
            'two'   => array('type' => 'section'),
            'three' => 'element',
            'four'  => array('type' => 'section'),
            'five'  => 'element'
        ));
        
        $writer = new \Zend\Config\Writer\Ini;
        $iniString = $writer->setConfig($config)->render($config);
        
        $expected = <<<ECS
one = "element"
three = "element"
five = "element"
[two]
type = "section"

[four]
type = "section"


ECS;
        
        $this->assertEquals(
            $expected,
            $iniString
        );
    }
}
