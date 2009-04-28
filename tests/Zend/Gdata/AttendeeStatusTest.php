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

require_once 'Zend/Gdata/Extension/AttendeeStatus.php';
require_once 'Zend/Gdata.php';

/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_AttendeeStatusTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->attendeeStatusText = file_get_contents(
                'Zend/Gdata/_files/AttendeeStatusElementSample1.xml',
                true);
        $this->attendeeStatus = new Zend_Gdata_Extension_AttendeeStatus();
    }
    
    public function testEmptyAttendeeStatusShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->attendeeStatus->extensionElements));
        $this->assertTrue(count($this->attendeeStatus->extensionElements) == 0);
    }

    public function testEmptyAttendeeStatusShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->attendeeStatus->extensionAttributes));
        $this->assertTrue(count($this->attendeeStatus->extensionAttributes) == 0);
    }

    public function testSampleAttendeeStatusShouldHaveNoExtensionElements() {
        $this->attendeeStatus->transferFromXML($this->attendeeStatusText);
        $this->assertTrue(is_array($this->attendeeStatus->extensionElements));
        $this->assertTrue(count($this->attendeeStatus->extensionElements) == 0);
    }

    public function testSampleAttendeeStatusShouldHaveNoExtensionAttributes() {
        $this->attendeeStatus->transferFromXML($this->attendeeStatusText);
        $this->assertTrue(is_array($this->attendeeStatus->extensionAttributes));
        $this->assertTrue(count($this->attendeeStatus->extensionAttributes) == 0);
    }
    
    public function testNormalAttendeeStatusShouldHaveNoExtensionElements() {
        $this->attendeeStatus->value = "http://schemas.google.com/g/2005#event.accepted";
        
        $this->assertEquals("http://schemas.google.com/g/2005#event.accepted", $this->attendeeStatus->value);
                
        $this->assertEquals(0, count($this->attendeeStatus->extensionElements));
        $newAttendeeStatus = new Zend_Gdata_Extension_AttendeeStatus(); 
        $newAttendeeStatus->transferFromXML($this->attendeeStatus->saveXML());
        $this->assertEquals(0, count($newAttendeeStatus->extensionElements));
        $newAttendeeStatus->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newAttendeeStatus->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#event.accepted", $newAttendeeStatus->value);

        /* try constructing using magic factory */
        $gdata = new Zend_Gdata();
        $newAttendeeStatus2 = $gdata->newAttendeeStatus();
        $newAttendeeStatus2->transferFromXML($newAttendeeStatus->saveXML());
        $this->assertEquals(1, count($newAttendeeStatus2->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#event.accepted", $newAttendeeStatus2->value);
    }

    public function testEmptyAttendeeStatusToAndFromStringShouldMatch() {
        $attendeeStatusXml = $this->attendeeStatus->saveXML();
        $newAttendeeStatus = new Zend_Gdata_Extension_AttendeeStatus();
        $newAttendeeStatus->transferFromXML($attendeeStatusXml);
        $newAttendeeStatusXml = $newAttendeeStatus->saveXML();
        $this->assertTrue($attendeeStatusXml == $newAttendeeStatusXml);
    }

    public function testAttendeeStatusWithValueToAndFromStringShouldMatch() {
        $this->attendeeStatus->value = "http://schemas.google.com/g/2005#event.accepted";
        $attendeeStatusXml = $this->attendeeStatus->saveXML();
        $newAttendeeStatus = new Zend_Gdata_Extension_AttendeeStatus();
        $newAttendeeStatus->transferFromXML($attendeeStatusXml);
        $newAttendeeStatusXml = $newAttendeeStatus->saveXML();
        $this->assertTrue($attendeeStatusXml == $newAttendeeStatusXml);
        $this->assertEquals("http://schemas.google.com/g/2005#event.accepted", $this->attendeeStatus->value);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->attendeeStatus->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->attendeeStatus->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->attendeeStatus->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->attendeeStatus->extensionAttributes['foo2']['value']);
        $attendeeStatusXml = $this->attendeeStatus->saveXML();
        $newAttendeeStatus = new Zend_Gdata_Extension_AttendeeStatus();
        $newAttendeeStatus->transferFromXML($attendeeStatusXml);
        $this->assertEquals('bar', $newAttendeeStatus->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newAttendeeStatus->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullAttendeeStatusToAndFromString() {
        $this->attendeeStatus->transferFromXML($this->attendeeStatusText);
        $this->assertEquals("http://schemas.google.com/g/2005#event.invited", $this->attendeeStatus->value);
    }

}
