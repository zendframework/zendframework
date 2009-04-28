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

require_once 'Zend/Gdata/Extension/Who.php';
require_once 'Zend/Gdata.php';

/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_WhoTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->whoText = file_get_contents(
                'Zend/Gdata/_files/WhoElementSample1.xml',
                true);
        $this->who = new Zend_Gdata_Extension_Who();
    }
    
    public function testEmptyWhoShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->who->extensionElements));
        $this->assertTrue(count($this->who->extensionElements) == 0);
    }

    public function testEmptyWhoShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->who->extensionAttributes));
        $this->assertTrue(count($this->who->extensionAttributes) == 0);
    }

    public function testSampleWhoShouldHaveNoExtensionElements() {
        $this->who->transferFromXML($this->whoText);
        $this->assertTrue(is_array($this->who->extensionElements));
        $this->assertTrue(count($this->who->extensionElements) == 0);
    }

    public function testSampleWhoShouldHaveNoExtensionAttributes() {
        $this->who->transferFromXML($this->whoText);
        $this->assertTrue(is_array($this->who->extensionAttributes));
        $this->assertTrue(count($this->who->extensionAttributes) == 0);
    }
    
    public function testNormalWhoShouldHaveNoExtensionElements() {
        $this->who->valueString = "Test Value String";
        $this->who->rel = "http://schemas.google.com/g/2005#event.speaker";
        $this->who->email = "testemail@somewhere.domain.invalid";
        
        $this->assertEquals("Test Value String", $this->who->valueString);
        $this->assertEquals("http://schemas.google.com/g/2005#event.speaker", $this->who->rel);
        $this->assertEquals("testemail@somewhere.domain.invalid", $this->who->email);
                
        $this->assertEquals(0, count($this->who->extensionElements));
        $newWho = new Zend_Gdata_Extension_Who(); 
        $newWho->transferFromXML($this->who->saveXML());
        $this->assertEquals(0, count($newWho->extensionElements));
        $newWho->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newWho->extensionElements));
        $this->assertEquals("Test Value String", $newWho->valueString);
        $this->assertEquals("http://schemas.google.com/g/2005#event.speaker", $newWho->rel);
        $this->assertEquals("testemail@somewhere.domain.invalid", $newWho->email);

        /* try constructing using magic factory */
        $gdata = new Zend_Gdata();
        $newWho2 = $gdata->newWho();
        $newWho2->transferFromXML($newWho->saveXML());
        $this->assertEquals(1, count($newWho2->extensionElements));
        $this->assertEquals("Test Value String", $newWho2->valueString);
        $this->assertEquals("http://schemas.google.com/g/2005#event.speaker", $newWho2->rel);
        $this->assertEquals("testemail@somewhere.domain.invalid", $newWho2->email);
    }

    public function testEmptyWhoToAndFromStringShouldMatch() {
        $whoXml = $this->who->saveXML();
        $newWho = new Zend_Gdata_Extension_Who();
        $newWho->transferFromXML($whoXml);
        $newWhoXml = $newWho->saveXML();
        $this->assertTrue($whoXml == $newWhoXml);
    }

    public function testWhoWithValueToAndFromStringShouldMatch() {
        $this->who->valueString = "Test Value String";
        $this->who->rel = "http://schemas.google.com/g/2005#event.speaker";
        $this->who->email = "testemail@somewhere.domain.invalid";
        $whoXml = $this->who->saveXML();
        $newWho = new Zend_Gdata_Extension_Who();
        $newWho->transferFromXML($whoXml);
        $newWhoXml = $newWho->saveXML();
        $this->assertTrue($whoXml == $newWhoXml);
        $this->assertEquals("Test Value String", $this->who->valueString);
        $this->assertEquals("http://schemas.google.com/g/2005#event.speaker", $this->who->rel);
        $this->assertEquals("testemail@somewhere.domain.invalid", $this->who->email);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->who->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->who->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->who->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->who->extensionAttributes['foo2']['value']);
        $whoXml = $this->who->saveXML();
        $newWho = new Zend_Gdata_Extension_Who();
        $newWho->transferFromXML($whoXml);
        $this->assertEquals('bar', $newWho->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newWho->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullWhoToAndFromString() {
        $this->who->transferFromXML($this->whoText);
        $this->assertEquals("Jo", $this->who->valueString);
        $this->assertEquals("http://schemas.google.com/g/2005#event.attendee", $this->who->rel);
        $this->assertEquals("jo@nowhere.invalid", $this->who->email);
		$this->assertTrue($this->who->attendeeStatus instanceof Zend_Gdata_Extension_AttendeeStatus);
		$this->assertEquals("http://schemas.google.com/g/2005#event.tentative", $this->who->attendeeStatus->value);
		$this->assertTrue($this->who->attendeeType instanceof Zend_Gdata_Extension_AttendeeType);
		$this->assertEquals("http://schemas.google.com/g/2005#event.required", $this->who->attendeeType->value);
		$this->assertTrue($this->who->entryLink instanceof Zend_Gdata_Extension_EntryLink);
		$this->assertEquals("http://gmail.com/jo/contacts/Jo", $this->who->entryLink->href);
    }

}
