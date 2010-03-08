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

/**
 * Zend_Config
 */

/**
 * Zend_Config_Writer_Array
 */

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
class Zend_Config_Writer_ArrayTest extends PHPUnit_Framework_TestCase
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
        $writer = new Zend_Config_Writer_Array(array('config' => new Zend_Config(array())));

        try {
            $writer->write();
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('No filename was set', $expected->getMessage());
        }
    }

    public function testNoConfigSet()
    {
        $writer = new Zend_Config_Writer_Array(array('filename' => $this->_tempName));

        try {
            $writer->write();
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('No config was set', $expected->getMessage());
        }
    }

    public function testFileNotWritable()
    {
        $writer = new Zend_Config_Writer_Array(array('config' => new Zend_Config(array()), 'filename' => '/../../../'));

        try {
            $writer->write();
            $this->fail('An expected Zend_Config_Exception has not been raised');
        } catch (Zend_Config_Exception $expected) {
            $this->assertContains('Could not write to file', $expected->getMessage());
        }
    }

    public function testWriteAndRead()
    {
        $config = new Zend_Config(array('test' => 'foo'));

        $writer = new Zend_Config_Writer_Array(array('config' => $config, 'filename' => $this->_tempName));
        $writer->write();

        $config = new Zend_Config(include $this->_tempName);

        $this->assertEquals('foo', $config->test);
    }

    public function testArgumentOverride()
    {
        $config = new Zend_Config(array('test' => 'foo'));

        $writer = new Zend_Config_Writer_Array();
        $writer->write($this->_tempName, $config);

        $config = new Zend_Config(include $this->_tempName);

        $this->assertEquals('foo', $config->test);
    }

    /**
     * @group ZF-8234
     */
    public function testRender()
    {
        $config = new Zend_Config(array('test' => 'foo', 'bar' => array(0 => 'baz', 1 => 'foo')));

        $writer = new Zend_Config_Writer_Array();
        $configString = $writer->setConfig($config)->render();


        // build string line by line as we are trailing-whitespace sensitive.
        $expected = "<?php\n";
        $expected .= "return array (\n";
        $expected .= "  'test' => 'foo',\n";
        $expected .= "  'bar' => \n";
        $expected .= "  array (\n";
        $expected .= "    0 => 'baz',\n";
        $expected .= "    1 => 'foo',\n";
        $expected .= "  ),\n";
        $expected .= ");\n";
        
        $this->assertEquals($expected, $configString);
    }
}
