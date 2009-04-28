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

require_once 'Zend/Gdata/Extension/OriginalEvent.php';
require_once 'Zend/Gdata.php';

/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_OriginalEventTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->originalEventText = file_get_contents(
                'Zend/Gdata/_files/OriginalEventElementSample1.xml',
                true);
        $this->originalEvent = new Zend_Gdata_Extension_OriginalEvent();
    }
    
    public function testEmptyOriginalEventShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->originalEvent->extensionElements));
        $this->assertTrue(count($this->originalEvent->extensionElements) == 0);
    }

    public function testEmptyOriginalEventShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->originalEvent->extensionAttributes));
        $this->assertTrue(count($this->originalEvent->extensionAttributes) == 0);
    }

    public function testSampleOriginalEventShouldHaveNoExtensionElements() {
        $this->originalEvent->transferFromXML($this->originalEventText);
        $this->assertTrue(is_array($this->originalEvent->extensionElements));
        $this->assertTrue(count($this->originalEvent->extensionElements) == 0);
    }

    public function testSampleOriginalEventShouldHaveNoExtensionAttributes() {
        $this->originalEvent->transferFromXML($this->originalEventText);
        $this->assertTrue(is_array($this->originalEvent->extensionAttributes));
        $this->assertTrue(count($this->originalEvent->extensionAttributes) == 0);
    }
    
    public function testNormalOriginalEventShouldHaveNoExtensionElements() {
        $this->originalEvent->href = "http://www.google.com/calendar/feeds/nobody@gmail.com/private/composite";
        $this->originalEvent->id = "abcdef123456789";
        
        $this->assertEquals("http://www.google.com/calendar/feeds/nobody@gmail.com/private/composite", $this->originalEvent->href);
        $this->assertEquals("abcdef123456789", $this->originalEvent->id);
                
        $this->assertEquals(0, count($this->originalEvent->extensionElements));
        $newOriginalEvent = new Zend_Gdata_Extension_OriginalEvent(); 
        $newOriginalEvent->transferFromXML($this->originalEvent->saveXML());
        $this->assertEquals(0, count($newOriginalEvent->extensionElements));
        $newOriginalEvent->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newOriginalEvent->extensionElements));
        $this->assertEquals("http://www.google.com/calendar/feeds/nobody@gmail.com/private/composite", $newOriginalEvent->href);
        $this->assertEquals("abcdef123456789", $newOriginalEvent->id);

        /* try constructing using magic factory */
        $gdata = new Zend_Gdata();
        $newOriginalEvent2 = $gdata->newOriginalEvent();
        $newOriginalEvent2->transferFromXML($newOriginalEvent->saveXML());
        $this->assertEquals(1, count($newOriginalEvent2->extensionElements));
        $this->assertEquals("http://www.google.com/calendar/feeds/nobody@gmail.com/private/composite", $newOriginalEvent2->href);
        $this->assertEquals("abcdef123456789", $newOriginalEvent2->id);
    }

    public function testEmptyOriginalEventToAndFromStringShouldMatch() {
        $originalEventXml = $this->originalEvent->saveXML();
        $newOriginalEvent = new Zend_Gdata_Extension_OriginalEvent();
        $newOriginalEvent->transferFromXML($originalEventXml);
        $newOriginalEventXml = $newOriginalEvent->saveXML();
        $this->assertTrue($originalEventXml == $newOriginalEventXml);
    }

    public function testOriginalEventWithValueToAndFromStringShouldMatch() {
        $this->originalEvent->href = "http://www.google.com/calendar/feeds/nobody@gmail.com/private/composite";
        $this->originalEvent->id = "abcdef123456789";
        $originalEventXml = $this->originalEvent->saveXML();
        $newOriginalEvent = new Zend_Gdata_Extension_OriginalEvent();
        $newOriginalEvent->transferFromXML($originalEventXml);
        $newOriginalEventXml = $newOriginalEvent->saveXML();
        $this->assertTrue($originalEventXml == $newOriginalEventXml);
        $this->assertEquals("http://www.google.com/calendar/feeds/nobody@gmail.com/private/composite", $this->originalEvent->href);
        $this->assertEquals("abcdef123456789", $this->originalEvent->id);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->originalEvent->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->originalEvent->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->originalEvent->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->originalEvent->extensionAttributes['foo2']['value']);
        $originalEventXml = $this->originalEvent->saveXML();
        $newOriginalEvent = new Zend_Gdata_Extension_OriginalEvent();
        $newOriginalEvent->transferFromXML($originalEventXml);
        $this->assertEquals('bar', $newOriginalEvent->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newOriginalEvent->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullOriginalEventToAndFromString() {
        $this->originalEvent->transferFromXML($this->originalEventText);
        $this->assertEquals("http://www.google.com/calendar/feeds/userID/private/full/123456789", $this->originalEvent->href);
        $this->assertEquals("i8fl1nrv2bl57c1qgr3f0onmgg", $this->originalEvent->id);
		$this->assertTrue($this->originalEvent->when instanceof Zend_Gdata_Extension_When);
		$this->assertEquals("2006-03-17T22:00:00.000Z", $this->originalEvent->when->startTime);
    }

}
