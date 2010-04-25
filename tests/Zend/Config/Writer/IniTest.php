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

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * Zend_Config_Ini
 */
require_once 'Zend/Config/Ini.php';

/**
 * Zend_Config_Writer_Ini
 */
require_once 'Zend/Config/Writer/Ini.php';

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
class Zend_Config_Writer_IniTest extends PHPUnit_Framework_TestCase
{
    protected $_tempName;

    public function setUp()
    {
        $this->_tempName = tempnam(dirname(__FILE__) . '/temp', 'tmp');
    }

    public function tearDown()
    {
        @unlink($this->_tempName);
    }

    public function testNoFilenameSet()
    {
        $writer = new Zend_Config_Writer_Ini(array('config' => new Zend_Config(array())));

        try {
            $writer->write();
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('No filename was set', $expected->getMessage());
        }
    }

    public function testNoConfigSet()
    {
        $writer = new Zend_Config_Writer_Ini(array('filename' => $this->_tempName));

        try {
            $writer->write();
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('No config was set', $expected->getMessage());
        }
    }

    public function testFileNotWritable()
    {
        $writer = new Zend_Config_Writer_Ini(array('config' => new Zend_Config(array()), 'filename' => '/../../../'));

        try {
            $writer->write();
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('Could not write to file', $expected->getMessage());
        }
    }

    public function testWriteAndRead()
    {
        $config = new Zend_Config(array('default' => array('test' => 'foo')));

        $writer = new Zend_Config_Writer_Ini(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new Zend_Config_Ini($this->_tempName, null);

        $this->assertEquals('foo', $config->default->test);
    }

    public function testNoSection()
    {
        $config = new Zend_Config(array('test' => 'foo', 'test2' => array('test3' => 'bar')));

        $writer = new Zend_Config_Writer_Ini(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new Zend_Config_Ini($this->_tempName, null);

        $this->assertEquals('foo', $config->test);
        $this->assertEquals('bar', $config->test2->test3);
    }

    public function testWriteAndReadOriginalFile()
    {
        $config = new Zend_Config_Ini(dirname(__FILE__) . '/files/allsections.ini', null, array('skipExtends' => true));

        $writer = new Zend_Config_Writer_Ini(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new Zend_Config_Ini($this->_tempName, null);
        $this->assertEquals('multi', $config->staging->one->two->three);

        $config = new Zend_Config_Ini($this->_tempName, null, array('skipExtends' => true));
        $this->assertFalse(isset($config->staging->one));
    }


    public function testWriteAndReadSingleSection()
    {
        $config = new Zend_Config_Ini(dirname(__FILE__) . '/files/allsections.ini', 'staging', array('skipExtends' => true));

        $writer = new Zend_Config_Writer_Ini(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new Zend_Config_Ini($this->_tempName, null);

        $this->assertEquals('staging', $config->staging->hostname);
        $this->assertEquals('', $config->staging->debug);
        $this->assertEquals(null, @$config->production);
    }

    public function testArgumentOverride()
    {
        $config = new Zend_Config(array('default' => array('test' => 'foo')));

        $writer = new Zend_Config_Writer_Ini();
        $writer->write($this->_tempName, $config);

        $config = new Zend_Config_Ini($this->_tempName, null);

        $this->assertEquals('foo', $config->default->test);
    }

    /**
     * @group ZF-8234
     */
    public function testRender()
    {
        $config = new Zend_Config(array('test' => 'foo', 'bar' => array(0 => 'baz', 1 => 'foo')));

        $writer = new Zend_Config_Writer_Ini();
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
        $config = new Zend_Config(array('test' => 'foo', 'test2' => array('test3' => 'bar')));

        $writer = new Zend_Config_Writer_Ini();
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
        $config = new Zend_Config_Ini(dirname(__FILE__) . '/files/allsections.ini', null, array('skipExtends' => true));

        $writer = new Zend_Config_Writer_Ini();
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

    public function testZF6521_NoDoubleQuoutesInValue()
    {
        $config = new Zend_Config(array('default' => array('test' => 'fo"o')));

        try {
            $writer = new Zend_Config_Writer_Ini(array('config' => $config, 'filename' => $this->_tempName));
            $writer->write();
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('Value can not contain double quotes "', $expected->getMessage());
        }
    }
    
    /**
     * @group ZF-6289
     */
    public function testZF6289_NonSectionElementsAndSectionJumbling()
    {
        $config = new Zend_Config(array(
            'one'   => 'element',
            'two'   => array('type' => 'section'),
            'three' => 'element',
            'four'  => array('type' => 'section'),
            'five'  => 'element'
        ));
        
        $writer = new Zend_Config_Writer_Ini;
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
