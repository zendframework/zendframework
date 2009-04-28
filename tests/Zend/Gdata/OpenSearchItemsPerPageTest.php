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

require_once 'Zend/Gdata/Extension/OpenSearchItemsPerPage.php';
require_once 'Zend/Gdata.php';

/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_OpenSearchItemsPerPageTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->openSearchItemsPerPageText = file_get_contents(
                'Zend/Gdata/_files/OpenSearchItemsPerPageElementSample1.xml',
                true);
        $this->openSearchItemsPerPage = new Zend_Gdata_Extension_OpenSearchItemsPerPage();
    }
    
    public function testEmptyOpenSearchItemsPerPageShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->openSearchItemsPerPage->extensionElements));
        $this->assertTrue(count($this->openSearchItemsPerPage->extensionElements) == 0);
    }

    public function testEmptyOpenSearchItemsPerPageShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->openSearchItemsPerPage->extensionAttributes));
        $this->assertTrue(count($this->openSearchItemsPerPage->extensionAttributes) == 0);
    }

    public function testSampleOpenSearchItemsPerPageShouldHaveNoExtensionElements() {
        $this->openSearchItemsPerPage->transferFromXML($this->openSearchItemsPerPageText);
        $this->assertTrue(is_array($this->openSearchItemsPerPage->extensionElements));
        $this->assertTrue(count($this->openSearchItemsPerPage->extensionElements) == 0);
    }

    public function testSampleOpenSearchItemsPerPageShouldHaveNoExtensionAttributes() {
        $this->openSearchItemsPerPage->transferFromXML($this->openSearchItemsPerPageText);
        $this->assertTrue(is_array($this->openSearchItemsPerPage->extensionAttributes));
        $this->assertTrue(count($this->openSearchItemsPerPage->extensionAttributes) == 0);
    }
    
    public function testNormalOpenSearchItemsPerPageShouldHaveNoExtensionElements() {
        $this->openSearchItemsPerPage->text = "200";
        
        $this->assertEquals("200", $this->openSearchItemsPerPage->text);
                
        $this->assertEquals(0, count($this->openSearchItemsPerPage->extensionElements));
        $newOpenSearchItemsPerPage = new Zend_Gdata_Extension_OpenSearchItemsPerPage(); 
        $newOpenSearchItemsPerPage->transferFromXML($this->openSearchItemsPerPage->saveXML());
        $this->assertEquals(0, count($newOpenSearchItemsPerPage->extensionElements));
        $newOpenSearchItemsPerPage->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newOpenSearchItemsPerPage->extensionElements));
        $this->assertEquals("200", $newOpenSearchItemsPerPage->text);

        /* try constructing using magic factory */
        $gdata = new Zend_Gdata();
        $newOpenSearchItemsPerPage2 = $gdata->newOpenSearchItemsPerPage();
        $newOpenSearchItemsPerPage2->transferFromXML($newOpenSearchItemsPerPage->saveXML());
        $this->assertEquals(1, count($newOpenSearchItemsPerPage2->extensionElements));
        $this->assertEquals("200", $newOpenSearchItemsPerPage2->text);
    }

    public function testEmptyOpenSearchItemsPerPageToAndFromStringShouldMatch() {
        $openSearchItemsPerPageXml = $this->openSearchItemsPerPage->saveXML();
        $newOpenSearchItemsPerPage = new Zend_Gdata_Extension_OpenSearchItemsPerPage();
        $newOpenSearchItemsPerPage->transferFromXML($openSearchItemsPerPageXml);
        $newOpenSearchItemsPerPageXml = $newOpenSearchItemsPerPage->saveXML();
        $this->assertTrue($openSearchItemsPerPageXml == $newOpenSearchItemsPerPageXml);
    }

    public function testOpenSearchItemsPerPageWithValueToAndFromStringShouldMatch() {
        $this->openSearchItemsPerPage->text = "200";
        $openSearchItemsPerPageXml = $this->openSearchItemsPerPage->saveXML();
        $newOpenSearchItemsPerPage = new Zend_Gdata_Extension_OpenSearchItemsPerPage();
        $newOpenSearchItemsPerPage->transferFromXML($openSearchItemsPerPageXml);
        $newOpenSearchItemsPerPageXml = $newOpenSearchItemsPerPage->saveXML();
        $this->assertTrue($openSearchItemsPerPageXml == $newOpenSearchItemsPerPageXml);
        $this->assertEquals("200", $this->openSearchItemsPerPage->text);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->openSearchItemsPerPage->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->openSearchItemsPerPage->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->openSearchItemsPerPage->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->openSearchItemsPerPage->extensionAttributes['foo2']['value']);
        $openSearchItemsPerPageXml = $this->openSearchItemsPerPage->saveXML();
        $newOpenSearchItemsPerPage = new Zend_Gdata_Extension_OpenSearchItemsPerPage();
        $newOpenSearchItemsPerPage->transferFromXML($openSearchItemsPerPageXml);
        $this->assertEquals('bar', $newOpenSearchItemsPerPage->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newOpenSearchItemsPerPage->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullOpenSearchItemsPerPageToAndFromString() {
        $this->openSearchItemsPerPage->transferFromXML($this->openSearchItemsPerPageText);
        $this->assertEquals("25", $this->openSearchItemsPerPage->text);
    }

}
