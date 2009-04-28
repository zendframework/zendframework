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

require_once 'Zend/Gdata/Extension/EntryLink.php';
require_once 'Zend/Gdata.php';

/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_EntryLinkTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryLinkText = file_get_contents(
                'Zend/Gdata/_files/EntryLinkElementSample1.xml',
                true);
        $this->entryLink = new Zend_Gdata_Extension_EntryLink();
    }
    
    public function testEmptyEntryLinkShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->entryLink->extensionElements));
        $this->assertTrue(count($this->entryLink->extensionElements) == 0);
    }

    public function testEmptyEntryLinkShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->entryLink->extensionAttributes));
        $this->assertTrue(count($this->entryLink->extensionAttributes) == 0);
    }

    public function testSampleEntryLinkShouldHaveNoExtensionElements() {
        $this->entryLink->transferFromXML($this->entryLinkText);
        $this->assertTrue(is_array($this->entryLink->extensionElements));
        $this->assertTrue(count($this->entryLink->extensionElements) == 0);
    }

    public function testSampleEntryLinkShouldHaveNoExtensionAttributes() {
        $this->entryLink->transferFromXML($this->entryLinkText);
        $this->assertTrue(is_array($this->entryLink->extensionAttributes));
        $this->assertTrue(count($this->entryLink->extensionAttributes) == 0);
    }
    
    public function testNormalEntryLinkShouldHaveNoExtensionElements() {
        $this->entryLink->href = "http://gmail.com/jo/contacts/Bob";
        $this->entryLink->rel = "self";
        $this->entryLink->readOnly = "false";
        
        $this->assertEquals("http://gmail.com/jo/contacts/Bob", $this->entryLink->href);
        $this->assertEquals("self", $this->entryLink->rel);
        $this->assertEquals("false", $this->entryLink->readOnly);
                
        $this->assertEquals(0, count($this->entryLink->extensionElements));
        $newEntryLink = new Zend_Gdata_Extension_EntryLink(); 
        $newEntryLink->transferFromXML($this->entryLink->saveXML());
        $this->assertEquals(0, count($newEntryLink->extensionElements));
        $newEntryLink->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newEntryLink->extensionElements));
        $this->assertEquals("http://gmail.com/jo/contacts/Bob", $newEntryLink->href);
        $this->assertEquals("self", $newEntryLink->rel);
        $this->assertEquals("false", $newEntryLink->readOnly);

        /* try constructing using magic factory */
        $gdata = new Zend_Gdata();
        $newEntryLink2 = $gdata->newEntryLink();
        $newEntryLink2->transferFromXML($newEntryLink->saveXML());
        $this->assertEquals(1, count($newEntryLink2->extensionElements));
        $this->assertEquals("http://gmail.com/jo/contacts/Bob", $newEntryLink2->href);
        $this->assertEquals("self", $newEntryLink2->rel);
        $this->assertEquals("false", $newEntryLink2->readOnly);
    }

    public function testEmptyEntryLinkToAndFromStringShouldMatch() {
        $entryLinkXml = $this->entryLink->saveXML();
        $newEntryLink = new Zend_Gdata_Extension_EntryLink();
        $newEntryLink->transferFromXML($entryLinkXml);
        $newEntryLinkXml = $newEntryLink->saveXML();
        $this->assertTrue($entryLinkXml == $newEntryLinkXml);
    }

    public function testEntryLinkWithValueToAndFromStringShouldMatch() {
        $this->entryLink->href = "http://gmail.com/jo/contacts/Bob";
        $this->entryLink->rel = "self";
        $this->entryLink->readOnly = "false";
        $entryLinkXml = $this->entryLink->saveXML();
        $newEntryLink = new Zend_Gdata_Extension_EntryLink();
        $newEntryLink->transferFromXML($entryLinkXml);
        $newEntryLinkXml = $newEntryLink->saveXML();
        $this->assertTrue($entryLinkXml == $newEntryLinkXml);
        $this->assertEquals("http://gmail.com/jo/contacts/Bob", $this->entryLink->href);
        $this->assertEquals("self", $this->entryLink->rel);
        $this->assertEquals("false", $this->entryLink->readOnly);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->entryLink->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->entryLink->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->entryLink->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->entryLink->extensionAttributes['foo2']['value']);
        $entryLinkXml = $this->entryLink->saveXML();
        $newEntryLink = new Zend_Gdata_Extension_EntryLink();
        $newEntryLink->transferFromXML($entryLinkXml);
        $this->assertEquals('bar', $newEntryLink->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newEntryLink->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullEntryLinkToAndFromString() {
        $this->entryLink->transferFromXML($this->entryLinkText);
        $this->assertEquals("http://gmail.com/jo/contacts/Jo", $this->entryLink->href);
        $this->assertEquals("via", $this->entryLink->rel);
        $this->assertEquals("true", $this->entryLink->readOnly);
		$this->assertTrue($this->entryLink->entry instanceof Zend_Gdata_App_Entry);
		$this->assertEquals("Jo March", $this->entryLink->entry->title->text);
    }

}
