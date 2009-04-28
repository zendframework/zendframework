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
 * @feed     Zend
 * @package      Zend_Gdata_App
 * @subpackage UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Calendar/EventEntry.php';
require_once 'Zend/Gdata/Calendar.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_Calendar_EventEntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/Gdata/Calendar/_files/EventEntrySample1.xml',
                true);
        $this->entry = new Zend_Gdata_Calendar_EventEntry();
    }

    public function testSetters() {
        $entry = new Zend_Gdata_Calendar_EventEntry();
        $who = new Zend_Gdata_Extension_Who();
        $who->setValueString("John Doe");
        $who->setEmail("john@doe.com");
        $entry->setWho($who);
        $whoRetrieved = $entry->getWho();
        $this->assertEquals("john@doe.com", $whoRetrieved->getEmail());
        $this->assertEquals("John Doe", $whoRetrieved->getValueString());
    }
      
    public function testEmptyEntryShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionElements() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionAttributes() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }
    
    public function testEmptyEventEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newEventEntry = new Zend_Gdata_Calendar_EventEntry();
        $newEventEntry->transferFromXML($entryXml);
        $newEventEntryXml = $newEventEntry->saveXML();
        $this->assertTrue($entryXml == $newEventEntryXml);
    }

    public function testConvertEventEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newEventEntry = new Zend_Gdata_Calendar_EventEntry();
        $newEventEntry->transferFromXML($entryXml);
        $newEventEntryXml = $newEventEntry->saveXML();
        $this->assertEquals($entryXml, $newEventEntryXml);
        $this->assertEquals('http://www.google.com/calendar/feeds/default/private/full/s0dtsvq4pe15ku09jideg67fv4_20070509T193000Z',
                $newEventEntry->id->text);
        $this->assertEquals('Mantek', 
                $newEventEntry->extendedProperty[0]->value);
        $this->assertEquals('s0dtsvq4pe15ku09jideg67fv4', 
            $newEventEntry->originalEvent->id);
        $this->assertEquals('s0dtsvq4pe15ku09jideg67fv4', 
            $newEventEntry->originalEvent->id);
        $this->assertEquals('http://www.google.com/calendar/feeds/default/private/full/s0dtsvq4pe15ku09jideg67fv4_20070509T193000Z/comments', 
            $newEventEntry->comments->feedLink->href);
    }

/*
    public function testEventEntryWithTextAndTypeToAndFromStringShouldMatch() {
        $this->feed->text = '<img src="http://www.example.com/image.jpg"/>';
        $this->feed->type = 'xhtml';
        $feedXml = $this->feed->saveXML();
        $newEventEntry = new Zend_Gdata_App_EventEntry();
        $newEventEntry->transferFromXML($feedXml);
        $newEventEntryXml = $newEventEntry->saveXML();
        $this->assertEquals($newEventEntryXml, $feedXml);
        $this->assertEquals('<img src="http://www.example.com/image.jpg"/>', $newEventEntry->text);
        $this->assertEquals('xhtml', $newEventEntry->type);
    }

    public function testEventEntryWithSrcAndTypeToAndFromStringShouldMatch() {
        $this->feed->src = 'http://www.example.com/image.png';
        $this->feed->type = 'image/png';
        $feedXml = $this->feed->saveXML();
        $newEventEntry = new Zend_Gdata_App_EventEntry();
        $newEventEntry->transferFromXML($feedXml);
        $newEventEntryXml = $newEventEntry->saveXML();
        $this->assertEquals($newEventEntryXml, $feedXml);
        $this->assertEquals('http://www.example.com/image.png', $newEventEntry->src);
        $this->assertEquals('image/png', $newEventEntry->type);
    }

    public function testConvertEventEntryWithSrcAndTypeToAndFromString() {
        $this->feed->transferFromXML($this->feedText);
        $this->assertEquals('http://www.example.com/image.png', $this->feed->src);
        $this->assertEquals('image/png', $this->feed->type);
    }
*/

}
