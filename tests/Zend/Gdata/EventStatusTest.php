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

require_once 'Zend/Gdata/Extension/EventStatus.php';
require_once 'Zend/Gdata.php';

/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_EventStatusTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->eventStatusText = file_get_contents(
                'Zend/Gdata/_files/EventStatusElementSample1.xml',
                true);
        $this->eventStatus = new Zend_Gdata_Extension_EventStatus();
    }
    
    public function testEmptyEventStatusShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->eventStatus->extensionElements));
        $this->assertTrue(count($this->eventStatus->extensionElements) == 0);
    }

    public function testEmptyEventStatusShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->eventStatus->extensionAttributes));
        $this->assertTrue(count($this->eventStatus->extensionAttributes) == 0);
    }

    public function testSampleEventStatusShouldHaveNoExtensionElements() {
        $this->eventStatus->transferFromXML($this->eventStatusText);
        $this->assertTrue(is_array($this->eventStatus->extensionElements));
        $this->assertTrue(count($this->eventStatus->extensionElements) == 0);
    }

    public function testSampleEventStatusShouldHaveNoExtensionAttributes() {
        $this->eventStatus->transferFromXML($this->eventStatusText);
        $this->assertTrue(is_array($this->eventStatus->extensionAttributes));
        $this->assertTrue(count($this->eventStatus->extensionAttributes) == 0);
    }
    
    public function testNormalEventStatusShouldHaveNoExtensionElements() {
        $this->eventStatus->value = "http://schemas.google.com/g/2005#event.tentative";
        
        $this->assertEquals("http://schemas.google.com/g/2005#event.tentative", $this->eventStatus->value);
                
        $this->assertEquals(0, count($this->eventStatus->extensionElements));
        $newEventStatus = new Zend_Gdata_Extension_EventStatus(); 
        $newEventStatus->transferFromXML($this->eventStatus->saveXML());
        $this->assertEquals(0, count($newEventStatus->extensionElements));
        $newEventStatus->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newEventStatus->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#event.tentative", $newEventStatus->value);

        /* try constructing using magic factory */
        $gdata = new Zend_Gdata();
        $newEventStatus2 = $gdata->newEventStatus();
        $newEventStatus2->transferFromXML($newEventStatus->saveXML());
        $this->assertEquals(1, count($newEventStatus2->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#event.tentative", $newEventStatus2->value);
    }

    public function testEmptyEventStatusToAndFromStringShouldMatch() {
        $eventStatusXml = $this->eventStatus->saveXML();
        $newEventStatus = new Zend_Gdata_Extension_EventStatus();
        $newEventStatus->transferFromXML($eventStatusXml);
        $newEventStatusXml = $newEventStatus->saveXML();
        $this->assertTrue($eventStatusXml == $newEventStatusXml);
    }

    public function testEventStatusWithValueToAndFromStringShouldMatch() {
        $this->eventStatus->value = "http://schemas.google.com/g/2005#event.tentative";
        $eventStatusXml = $this->eventStatus->saveXML();
        $newEventStatus = new Zend_Gdata_Extension_EventStatus();
        $newEventStatus->transferFromXML($eventStatusXml);
        $newEventStatusXml = $newEventStatus->saveXML();
        $this->assertTrue($eventStatusXml == $newEventStatusXml);
        $this->assertEquals("http://schemas.google.com/g/2005#event.tentative", $this->eventStatus->value);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->eventStatus->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->eventStatus->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->eventStatus->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->eventStatus->extensionAttributes['foo2']['value']);
        $eventStatusXml = $this->eventStatus->saveXML();
        $newEventStatus = new Zend_Gdata_Extension_EventStatus();
        $newEventStatus->transferFromXML($eventStatusXml);
        $this->assertEquals('bar', $newEventStatus->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newEventStatus->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullEventStatusToAndFromString() {
        $this->eventStatus->transferFromXML($this->eventStatusText);
        $this->assertEquals("http://schemas.google.com/g/2005#event.confirmed", $this->eventStatus->value);
    }

}
