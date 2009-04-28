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
 * @package      Zend_Gdata_Calendar
 * @subpackage   UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Calendar/Extension/Timezone.php';
require_once 'Zend/Gdata/Calendar.php';

/**
 * @package    Zend_Gdata_Calendar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Calendar_TimezoneTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->timezoneText = file_get_contents(
                'Zend/Gdata/Calendar/_files/TimezoneElementSample1.xml',
                true);
        $this->timezone = new Zend_Gdata_Calendar_Extension_Timezone();
    }
      
    public function testEmptyTimezoneShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->timezone->extensionElements));
        $this->assertTrue(count($this->timezone->extensionElements) == 0);
    }

    public function testEmptyTimezoneShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->timezone->extensionAttributes));
        $this->assertTrue(count($this->timezone->extensionAttributes) == 0);
    }

    public function testSampleTimezoneShouldHaveNoExtensionElements() {
        $this->timezone->transferFromXML($this->timezoneText);
        $this->assertTrue(is_array($this->timezone->extensionElements));
        $this->assertTrue(count($this->timezone->extensionElements) == 0);
    }

    public function testSampleTimezoneShouldHaveNoExtensionAttributes() {
        $this->timezone->transferFromXML($this->timezoneText);
        $this->assertTrue(is_array($this->timezone->extensionAttributes));
        $this->assertTrue(count($this->timezone->extensionAttributes) == 0);
    }
    
    public function testNormalTimezoneShouldHaveNoExtensionElements() {
        $this->timezone->value = "America/Chicago";
        $this->assertEquals($this->timezone->value, "America/Chicago");
        $this->assertEquals(count($this->timezone->extensionElements), 0);
        $newTimezone = new Zend_Gdata_Calendar_Extension_Timezone(); 
        $newTimezone->transferFromXML($this->timezone->saveXML());
        $this->assertEquals(count($newTimezone->extensionElements), 0);
        $newTimezone->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(count($newTimezone->extensionElements), 1);
        $this->assertEquals($newTimezone->value, "America/Chicago");

        /* try constructing using magic factory */
        $cal = new Zend_Gdata_Calendar();
        $newTimezone2 = $cal->newTimezone();
        $newTimezone2->transferFromXML($newTimezone->saveXML());
        $this->assertEquals(count($newTimezone2->extensionElements), 1);
        $this->assertEquals($newTimezone2->value, "America/Chicago");
    }

    public function testEmptyTimezoneToAndFromStringShouldMatch() {
        $timezoneXml = $this->timezone->saveXML();
        $newTimezone = new Zend_Gdata_Calendar_Extension_Timezone();
        $newTimezone->transferFromXML($timezoneXml);
        $newTimezoneXml = $newTimezone->saveXML();
        $this->assertTrue($timezoneXml == $newTimezoneXml);
    }

    public function testTimezoneWithValueToAndFromStringShouldMatch() {
        $this->timezone->value = "America/Chicago";
        $timezoneXml = $this->timezone->saveXML();
        $newTimezone = new Zend_Gdata_Calendar_Extension_Timezone();
        $newTimezone->transferFromXML($timezoneXml);
        $newTimezoneXml = $newTimezone->saveXML();
        $this->assertTrue($timezoneXml == $newTimezoneXml);
        $this->assertEquals("America/Chicago", $newTimezone->value);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->timezone->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->timezone->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->timezone->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->timezone->extensionAttributes['foo2']['value']);
        $timezoneXml = $this->timezone->saveXML();
        $newTimezone = new Zend_Gdata_Calendar_Extension_Timezone();
        $newTimezone->transferFromXML($timezoneXml);
        $this->assertEquals('bar', $newTimezone->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newTimezone->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullTimezoneToAndFromString() {
        $this->timezone->transferFromXML($this->timezoneText);
        $this->assertEquals($this->timezone->value, "America/Los_Angeles");
    }

}
