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
 * @category     Zend
 * @package      Zend_Gdata
 * @subpackage   UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Extension/OpenSearchStartIndex.php';
require_once 'Zend/Gdata.php';

/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_OpenSearchStartIndexTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->openSearchStartIndexText = file_get_contents(
                'Zend/Gdata/_files/OpenSearchStartIndexElementSample1.xml',
                true);
        $this->openSearchStartIndex = new Zend_Gdata_Extension_OpenSearchStartIndex();
    }
    
    public function testEmptyOpenSearchStartIndexShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->openSearchStartIndex->extensionElements));
        $this->assertTrue(count($this->openSearchStartIndex->extensionElements) == 0);
    }

    public function testEmptyOpenSearchStartIndexShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->openSearchStartIndex->extensionAttributes));
        $this->assertTrue(count($this->openSearchStartIndex->extensionAttributes) == 0);
    }

    public function testSampleOpenSearchStartIndexShouldHaveNoExtensionElements() {
        $this->openSearchStartIndex->transferFromXML($this->openSearchStartIndexText);
        $this->assertTrue(is_array($this->openSearchStartIndex->extensionElements));
        $this->assertTrue(count($this->openSearchStartIndex->extensionElements) == 0);
    }

    public function testSampleOpenSearchStartIndexShouldHaveNoExtensionAttributes() {
        $this->openSearchStartIndex->transferFromXML($this->openSearchStartIndexText);
        $this->assertTrue(is_array($this->openSearchStartIndex->extensionAttributes));
        $this->assertTrue(count($this->openSearchStartIndex->extensionAttributes) == 0);
    }
    
    public function testNormalOpenSearchStartIndexShouldHaveNoExtensionElements() {
        $this->openSearchStartIndex->text = "20";
        
        $this->assertEquals("20", $this->openSearchStartIndex->text);
                
        $this->assertEquals(0, count($this->openSearchStartIndex->extensionElements));
        $newOpenSearchStartIndex = new Zend_Gdata_Extension_OpenSearchStartIndex(); 
        $newOpenSearchStartIndex->transferFromXML($this->openSearchStartIndex->saveXML());
        $this->assertEquals(0, count($newOpenSearchStartIndex->extensionElements));
        $newOpenSearchStartIndex->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newOpenSearchStartIndex->extensionElements));
        $this->assertEquals("20", $newOpenSearchStartIndex->text);

        /* try constructing using magic factory */
        $gdata = new Zend_Gdata();
        $newOpenSearchStartIndex2 = $gdata->newOpenSearchStartIndex();
        $newOpenSearchStartIndex2->transferFromXML($newOpenSearchStartIndex->saveXML());
        $this->assertEquals(1, count($newOpenSearchStartIndex2->extensionElements));
        $this->assertEquals("20", $newOpenSearchStartIndex2->text);
    }

    public function testEmptyOpenSearchStartIndexToAndFromStringShouldMatch() {
        $openSearchStartIndexXml = $this->openSearchStartIndex->saveXML();
        $newOpenSearchStartIndex = new Zend_Gdata_Extension_OpenSearchStartIndex();
        $newOpenSearchStartIndex->transferFromXML($openSearchStartIndexXml);
        $newOpenSearchStartIndexXml = $newOpenSearchStartIndex->saveXML();
        $this->assertTrue($openSearchStartIndexXml == $newOpenSearchStartIndexXml);
    }

    public function testOpenSearchStartIndexWithValueToAndFromStringShouldMatch() {
        $this->openSearchStartIndex->text = "20";
        $openSearchStartIndexXml = $this->openSearchStartIndex->saveXML();
        $newOpenSearchStartIndex = new Zend_Gdata_Extension_OpenSearchStartIndex();
        $newOpenSearchStartIndex->transferFromXML($openSearchStartIndexXml);
        $newOpenSearchStartIndexXml = $newOpenSearchStartIndex->saveXML();
        $this->assertTrue($openSearchStartIndexXml == $newOpenSearchStartIndexXml);
        $this->assertEquals("20", $this->openSearchStartIndex->text);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->openSearchStartIndex->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->openSearchStartIndex->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->openSearchStartIndex->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->openSearchStartIndex->extensionAttributes['foo2']['value']);
        $openSearchStartIndexXml = $this->openSearchStartIndex->saveXML();
        $newOpenSearchStartIndex = new Zend_Gdata_Extension_OpenSearchStartIndex();
        $newOpenSearchStartIndex->transferFromXML($openSearchStartIndexXml);
        $this->assertEquals('bar', $newOpenSearchStartIndex->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newOpenSearchStartIndex->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullOpenSearchStartIndexToAndFromString() {
        $this->openSearchStartIndex->transferFromXML($this->openSearchStartIndexText);
        $this->assertEquals("5", $this->openSearchStartIndex->text);
    }

}
