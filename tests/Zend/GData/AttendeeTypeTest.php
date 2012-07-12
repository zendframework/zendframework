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
class AttendeeTypeTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->attendeeTypeText = file_get_contents(
                'Zend/GData/_files/AttendeeTypeElementSample1.xml',
                true);
        $this->attendeeType = new Extension\AttendeeType();
    }

    public function testEmptyAttendeeTypeShouldHaveNoExtensionElements()
    {
        $this->assertTrue(is_array($this->attendeeType->extensionElements));
        $this->assertTrue(count($this->attendeeType->extensionElements) == 0);
    }

    public function testEmptyAttendeeTypeShouldHaveNoExtensionAttributes()
    {
        $this->assertTrue(is_array($this->attendeeType->extensionAttributes));
        $this->assertTrue(count($this->attendeeType->extensionAttributes) == 0);
    }

    public function testSampleAttendeeTypeShouldHaveNoExtensionElements()
    {
        $this->attendeeType->transferFromXML($this->attendeeTypeText);
        $this->assertTrue(is_array($this->attendeeType->extensionElements));
        $this->assertTrue(count($this->attendeeType->extensionElements) == 0);
    }

    public function testSampleAttendeeTypeShouldHaveNoExtensionAttributes()
    {
        $this->attendeeType->transferFromXML($this->attendeeTypeText);
        $this->assertTrue(is_array($this->attendeeType->extensionAttributes));
        $this->assertTrue(count($this->attendeeType->extensionAttributes) == 0);
    }

    public function testNormalAttendeeTypeShouldHaveNoExtensionElements()
    {
        $this->attendeeType->value = "http://schemas.google.com/g/2005#event.optional";

        $this->assertEquals("http://schemas.google.com/g/2005#event.optional", $this->attendeeType->value);

        $this->assertEquals(0, count($this->attendeeType->extensionElements));
        $newAttendeeType = new Extension\AttendeeType();
        $newAttendeeType->transferFromXML($this->attendeeType->saveXML());
        $this->assertEquals(0, count($newAttendeeType->extensionElements));
        $newAttendeeType->extensionElements = array(
                new \Zend\GData\App\Extension\Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newAttendeeType->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#event.optional", $newAttendeeType->value);

        /* try constructing using magic factory */
        $gdata = new \Zend\GData\GData();
        $newAttendeeType2 = $gdata->newAttendeeType();
        $newAttendeeType2->transferFromXML($newAttendeeType->saveXML());
        $this->assertEquals(1, count($newAttendeeType2->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#event.optional", $newAttendeeType2->value);
    }

    public function testEmptyAttendeeTypeToAndFromStringShouldMatch()
    {
        $attendeeTypeXml = $this->attendeeType->saveXML();
        $newAttendeeType = new Extension\AttendeeType();
        $newAttendeeType->transferFromXML($attendeeTypeXml);
        $newAttendeeTypeXml = $newAttendeeType->saveXML();
        $this->assertTrue($attendeeTypeXml == $newAttendeeTypeXml);
    }

    public function testAttendeeTypeWithValueToAndFromStringShouldMatch()
    {
        $this->attendeeType->value = "http://schemas.google.com/g/2005#event.optional";
        $attendeeTypeXml = $this->attendeeType->saveXML();
        $newAttendeeType = new Extension\AttendeeType();
        $newAttendeeType->transferFromXML($attendeeTypeXml);
        $newAttendeeTypeXml = $newAttendeeType->saveXML();
        $this->assertTrue($attendeeTypeXml == $newAttendeeTypeXml);
        $this->assertEquals("http://schemas.google.com/g/2005#event.optional", $this->attendeeType->value);
    }

    public function testExtensionAttributes()
    {
        $extensionAttributes = $this->attendeeType->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->attendeeType->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->attendeeType->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->attendeeType->extensionAttributes['foo2']['value']);
        $attendeeTypeXml = $this->attendeeType->saveXML();
        $newAttendeeType = new Extension\AttendeeType();
        $newAttendeeType->transferFromXML($attendeeTypeXml);
        $this->assertEquals('bar', $newAttendeeType->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newAttendeeType->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullAttendeeTypeToAndFromString()
    {
        $this->attendeeType->transferFromXML($this->attendeeTypeText);
        $this->assertEquals("http://schemas.google.com/g/2005#event.required", $this->attendeeType->value);
    }

}
