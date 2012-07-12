<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData;

use Zend\GData\Extension;

/**
 * @category   Zend
 * @package    Zend_GData
 * @subpackage UnitTests
 * @group      Zend_GData
 */
class AttendeeStatusTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->attendeeStatusText = file_get_contents(
                'Zend/GData/_files/AttendeeStatusElementSample1.xml',
                true);
        $this->attendeeStatus = new Extension\AttendeeStatus();
    }

    public function testEmptyAttendeeStatusShouldHaveNoExtensionElements()
    {
        $this->assertTrue(is_array($this->attendeeStatus->extensionElements));
        $this->assertTrue(count($this->attendeeStatus->extensionElements) == 0);
    }

    public function testEmptyAttendeeStatusShouldHaveNoExtensionAttributes()
    {
        $this->assertTrue(is_array($this->attendeeStatus->extensionAttributes));
        $this->assertTrue(count($this->attendeeStatus->extensionAttributes) == 0);
    }

    public function testSampleAttendeeStatusShouldHaveNoExtensionElements()
    {
        $this->attendeeStatus->transferFromXML($this->attendeeStatusText);
        $this->assertTrue(is_array($this->attendeeStatus->extensionElements));
        $this->assertTrue(count($this->attendeeStatus->extensionElements) == 0);
    }

    public function testSampleAttendeeStatusShouldHaveNoExtensionAttributes()
    {
        $this->attendeeStatus->transferFromXML($this->attendeeStatusText);
        $this->assertTrue(is_array($this->attendeeStatus->extensionAttributes));
        $this->assertTrue(count($this->attendeeStatus->extensionAttributes) == 0);
    }

    public function testNormalAttendeeStatusShouldHaveNoExtensionElements()
    {
        $this->attendeeStatus->value = "http://schemas.google.com/g/2005#event.accepted";

        $this->assertEquals("http://schemas.google.com/g/2005#event.accepted", $this->attendeeStatus->value);

        $this->assertEquals(0, count($this->attendeeStatus->extensionElements));
        $newAttendeeStatus = new Extension\AttendeeStatus();
        $newAttendeeStatus->transferFromXML($this->attendeeStatus->saveXML());
        $this->assertEquals(0, count($newAttendeeStatus->extensionElements));
        $newAttendeeStatus->extensionElements = array(
                new \Zend\GData\App\Extension\Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newAttendeeStatus->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#event.accepted", $newAttendeeStatus->value);

        /* try constructing using magic factory */
        $gdata = new \Zend\GData\GData();
        $newAttendeeStatus2 = $gdata->newAttendeeStatus();
        $newAttendeeStatus2->transferFromXML($newAttendeeStatus->saveXML());
        $this->assertEquals(1, count($newAttendeeStatus2->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#event.accepted", $newAttendeeStatus2->value);
    }

    public function testEmptyAttendeeStatusToAndFromStringShouldMatch()
    {
        $attendeeStatusXml = $this->attendeeStatus->saveXML();
        $newAttendeeStatus = new Extension\AttendeeStatus();
        $newAttendeeStatus->transferFromXML($attendeeStatusXml);
        $newAttendeeStatusXml = $newAttendeeStatus->saveXML();
        $this->assertTrue($attendeeStatusXml == $newAttendeeStatusXml);
    }

    public function testAttendeeStatusWithValueToAndFromStringShouldMatch()
    {
        $this->attendeeStatus->value = "http://schemas.google.com/g/2005#event.accepted";
        $attendeeStatusXml = $this->attendeeStatus->saveXML();
        $newAttendeeStatus = new Extension\AttendeeStatus();
        $newAttendeeStatus->transferFromXML($attendeeStatusXml);
        $newAttendeeStatusXml = $newAttendeeStatus->saveXML();
        $this->assertTrue($attendeeStatusXml == $newAttendeeStatusXml);
        $this->assertEquals("http://schemas.google.com/g/2005#event.accepted", $this->attendeeStatus->value);
    }

    public function testExtensionAttributes()
    {
        $extensionAttributes = $this->attendeeStatus->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->attendeeStatus->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->attendeeStatus->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->attendeeStatus->extensionAttributes['foo2']['value']);
        $attendeeStatusXml = $this->attendeeStatus->saveXML();
        $newAttendeeStatus = new Extension\AttendeeStatus();
        $newAttendeeStatus->transferFromXML($attendeeStatusXml);
        $this->assertEquals('bar', $newAttendeeStatus->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newAttendeeStatus->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullAttendeeStatusToAndFromString()
    {
        $this->attendeeStatus->transferFromXML($this->attendeeStatusText);
        $this->assertEquals("http://schemas.google.com/g/2005#event.invited", $this->attendeeStatus->value);
    }

}
