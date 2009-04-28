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

require_once 'Zend/Gdata/Calendar/Extension/SendEventNotifications.php';
require_once 'Zend/Gdata/Calendar.php';

/**
 * @package    Zend_Gdata_Calendar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Calendar_SendEventNotificationsTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->sendEventNotificationsText = file_get_contents(
                'Zend/Gdata/Calendar/_files/SendEventNotificationsElementSample1.xml',
                true);
        $this->sendEventNotifications = new Zend_Gdata_Calendar_Extension_SendEventNotifications();
    }
      
    public function testEmptySendEventNotificationsShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->sendEventNotifications->extensionElements));
        $this->assertTrue(count($this->sendEventNotifications->extensionElements) == 0);
    }

    public function testEmptySendEventNotificationsShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->sendEventNotifications->extensionAttributes));
        $this->assertTrue(count($this->sendEventNotifications->extensionAttributes) == 0);
    }

    public function testSampleSendEventNotificationsShouldHaveNoExtensionElements() {
        $this->sendEventNotifications->transferFromXML($this->sendEventNotificationsText);
        $this->assertTrue(is_array($this->sendEventNotifications->extensionElements));
        $this->assertTrue(count($this->sendEventNotifications->extensionElements) == 0);
    }

    public function testSampleSendEventNotificationsShouldHaveNoExtensionAttributes() {
        $this->sendEventNotifications->transferFromXML($this->sendEventNotificationsText);
        $this->assertTrue(is_array($this->sendEventNotifications->extensionAttributes));
        $this->assertTrue(count($this->sendEventNotifications->extensionAttributes) == 0);
    }
    
    public function testNormalSendEventNotificationsShouldHaveNoExtensionElements() {
        $this->sendEventNotifications->value = true;
        $this->assertEquals($this->sendEventNotifications->value, true);
        $this->assertEquals(count($this->sendEventNotifications->extensionElements), 0);
        $newSendEventNotifications = new Zend_Gdata_Calendar_Extension_SendEventNotifications(); 
        $newSendEventNotifications->transferFromXML($this->sendEventNotifications->saveXML());
        $this->assertEquals(count($newSendEventNotifications->extensionElements), 0);
        $newSendEventNotifications->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(count($newSendEventNotifications->extensionElements), 1);
        $this->assertEquals($newSendEventNotifications->value, true);

        /* try constructing using magic factory */
        $cal = new Zend_Gdata_Calendar();
        $newSendEventNotifications2 = $cal->newSendEventNotifications();
        $newSendEventNotifications2->transferFromXML($newSendEventNotifications->saveXML());
        $this->assertEquals(count($newSendEventNotifications2->extensionElements), 1);
        $this->assertEquals($newSendEventNotifications2->value, true);
    }

    public function testEmptySendEventNotificationsToAndFromStringShouldMatch() {
        $sendEventNotificationsXml = $this->sendEventNotifications->saveXML();
        $newSendEventNotifications = new Zend_Gdata_Calendar_Extension_SendEventNotifications();
        $newSendEventNotifications->transferFromXML($sendEventNotificationsXml);
        $newSendEventNotificationsXml = $newSendEventNotifications->saveXML();
        $this->assertTrue($sendEventNotificationsXml == $newSendEventNotificationsXml);
    }

    public function testSendEventNotificationsWithValueToAndFromStringShouldMatch() {
        $this->sendEventNotifications->value = true;
        $sendEventNotificationsXml = $this->sendEventNotifications->saveXML();
        $newSendEventNotifications = new Zend_Gdata_Calendar_Extension_SendEventNotifications();
        $newSendEventNotifications->transferFromXML($sendEventNotificationsXml);
        $newSendEventNotificationsXml = $newSendEventNotifications->saveXML();
        $this->assertTrue($sendEventNotificationsXml == $newSendEventNotificationsXml);
        $this->assertEquals(true, $newSendEventNotifications->value);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->sendEventNotifications->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->sendEventNotifications->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->sendEventNotifications->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->sendEventNotifications->extensionAttributes['foo2']['value']);
        $sendEventNotificationsXml = $this->sendEventNotifications->saveXML();
        $newSendEventNotifications = new Zend_Gdata_Calendar_Extension_SendEventNotifications();
        $newSendEventNotifications->transferFromXML($sendEventNotificationsXml);
        $this->assertEquals('bar', $newSendEventNotifications->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newSendEventNotifications->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullSendEventNotificationsToAndFromString() {
        $this->sendEventNotifications->transferFromXML($this->sendEventNotificationsText);
        $this->assertEquals($this->sendEventNotifications->value, false);
    }

}
