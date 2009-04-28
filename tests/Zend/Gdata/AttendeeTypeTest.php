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

require_once 'Zend/Gdata/Extension/AttendeeType.php';
require_once 'Zend/Gdata.php';

/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_AttendeeTypeTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->attendeeTypeText = file_get_contents(
                'Zend/Gdata/_files/AttendeeTypeElementSample1.xml',
                true);
        $this->attendeeType = new Zend_Gdata_Extension_AttendeeType();
    }
    
    public function testEmptyAttendeeTypeShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->attendeeType->extensionElements));
        $this->assertTrue(count($this->attendeeType->extensionElements) == 0);
    }

    public function testEmptyAttendeeTypeShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->attendeeType->extensionAttributes));
        $this->assertTrue(count($this->attendeeType->extensionAttributes) == 0);
    }

    public function testSampleAttendeeTypeShouldHaveNoExtensionElements() {
        $this->attendeeType->transferFromXML($this->attendeeTypeText);
        $this->assertTrue(is_array($this->attendeeType->extensionElements));
        $this->assertTrue(count($this->attendeeType->extensionElements) == 0);
    }

    public function testSampleAttendeeTypeShouldHaveNoExtensionAttributes() {
        $this->attendeeType->transferFromXML($this->attendeeTypeText);
        $this->assertTrue(is_array($this->attendeeType->extensionAttributes));
        $this->assertTrue(count($this->attendeeType->extensionAttributes) == 0);
    }
    
    public function testNormalAttendeeTypeShouldHaveNoExtensionElements() {
        $this->attendeeType->value = "http://schemas.google.com/g/2005#event.optional";
        
        $this->assertEquals("http://schemas.google.com/g/2005#event.optional", $this->attendeeType->value);
                
        $this->assertEquals(0, count($this->attendeeType->extensionElements));
        $newAttendeeType = new Zend_Gdata_Extension_AttendeeType(); 
        $newAttendeeType->transferFromXML($this->attendeeType->saveXML());
        $this->assertEquals(0, count($newAttendeeType->extensionElements));
        $newAttendeeType->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newAttendeeType->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#event.optional", $newAttendeeType->value);

        /* try constructing using magic factory */
        $gdata = new Zend_Gdata();
        $newAttendeeType2 = $gdata->newAttendeeType();
        $newAttendeeType2->transferFromXML($newAttendeeType->saveXML());
        $this->assertEquals(1, count($newAttendeeType2->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#event.optional", $newAttendeeType2->value);
    }

    public function testEmptyAttendeeTypeToAndFromStringShouldMatch() {
        $attendeeTypeXml = $this->attendeeType->saveXML();
        $newAttendeeType = new Zend_Gdata_Extension_AttendeeType();
        $newAttendeeType->transferFromXML($attendeeTypeXml);
        $newAttendeeTypeXml = $newAttendeeType->saveXML();
        $this->assertTrue($attendeeTypeXml == $newAttendeeTypeXml);
    }

    public function testAttendeeTypeWithValueToAndFromStringShouldMatch() {
        $this->attendeeType->value = "http://schemas.google.com/g/2005#event.optional";
        $attendeeTypeXml = $this->attendeeType->saveXML();
        $newAttendeeType = new Zend_Gdata_Extension_AttendeeType();
        $newAttendeeType->transferFromXML($attendeeTypeXml);
        $newAttendeeTypeXml = $newAttendeeType->saveXML();
        $this->assertTrue($attendeeTypeXml == $newAttendeeTypeXml);
        $this->assertEquals("http://schemas.google.com/g/2005#event.optional", $this->attendeeType->value);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->attendeeType->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->attendeeType->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->attendeeType->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->attendeeType->extensionAttributes['foo2']['value']);
        $attendeeTypeXml = $this->attendeeType->saveXML();
        $newAttendeeType = new Zend_Gdata_Extension_AttendeeType();
        $newAttendeeType->transferFromXML($attendeeTypeXml);
        $this->assertEquals('bar', $newAttendeeType->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newAttendeeType->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullAttendeeTypeToAndFromString() {
        $this->attendeeType->transferFromXML($this->attendeeTypeText);
        $this->assertEquals("http://schemas.google.com/g/2005#event.required", $this->attendeeType->value);
    }

}
