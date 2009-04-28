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

require_once 'Zend/Gdata/Calendar/Extension/AccessLevel.php';
require_once 'Zend/Gdata/Calendar.php';

/**
 * @package    Zend_Gdata_Calendar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Calendar_AccessLevelTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->accessLevelText = file_get_contents(
                'Zend/Gdata/Calendar/_files/AccessLevelElementSample1.xml',
                true);
        $this->accessLevel = new Zend_Gdata_Calendar_Extension_AccessLevel();
    }
      
    public function testEmptyAccessLevelShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->accessLevel->extensionElements));
        $this->assertTrue(count($this->accessLevel->extensionElements) == 0);
    }

    public function testEmptyAccessLevelShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->accessLevel->extensionAttributes));
        $this->assertTrue(count($this->accessLevel->extensionAttributes) == 0);
    }

    public function testSampleAccessLevelShouldHaveNoExtensionElements() {
        $this->accessLevel->transferFromXML($this->accessLevelText);
        $this->assertTrue(is_array($this->accessLevel->extensionElements));
        $this->assertTrue(count($this->accessLevel->extensionElements) == 0);
    }

    public function testSampleAccessLevelShouldHaveNoExtensionAttributes() {
        $this->accessLevel->transferFromXML($this->accessLevelText);
        $this->assertTrue(is_array($this->accessLevel->extensionAttributes));
        $this->assertTrue(count($this->accessLevel->extensionAttributes) == 0);
    }
    
    public function testNormalAccessLevelShouldHaveNoExtensionElements() {
        $this->accessLevel->value = 'freebusy';
        $this->assertEquals($this->accessLevel->value, 'freebusy');
        $this->assertEquals(count($this->accessLevel->extensionElements), 0);
        $newAccessLevel = new Zend_Gdata_Calendar_Extension_AccessLevel(); 
        $newAccessLevel->transferFromXML($this->accessLevel->saveXML());
        $this->assertEquals(count($newAccessLevel->extensionElements), 0);
        $newAccessLevel->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(count($newAccessLevel->extensionElements), 1);
        $this->assertEquals($newAccessLevel->value, 'freebusy');

        /* try constructing using magic factory */
        $cal = new Zend_Gdata_Calendar();
        $newAccessLevel2 = $cal->newAccessLevel();
        $newAccessLevel2->transferFromXML($newAccessLevel->saveXML());
        $this->assertEquals(count($newAccessLevel2->extensionElements), 1);
        $this->assertEquals($newAccessLevel2->value, 'freebusy');
    }

    public function testEmptyAccessLevelToAndFromStringShouldMatch() {
        $accessLevelXml = $this->accessLevel->saveXML();
        $newAccessLevel = new Zend_Gdata_Calendar_Extension_AccessLevel();
        $newAccessLevel->transferFromXML($accessLevelXml);
        $newAccessLevelXml = $newAccessLevel->saveXML();
        $this->assertTrue($accessLevelXml == $newAccessLevelXml);
    }

    public function testAccessLevelWithValueToAndFromStringShouldMatch() {
        $this->accessLevel->value = 'freebusy';
        $accessLevelXml = $this->accessLevel->saveXML();
        $newAccessLevel = new Zend_Gdata_Calendar_Extension_AccessLevel();
        $newAccessLevel->transferFromXML($accessLevelXml);
        $newAccessLevelXml = $newAccessLevel->saveXML();
        $this->assertTrue($accessLevelXml == $newAccessLevelXml);
        $this->assertEquals('freebusy', $newAccessLevel->value);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->accessLevel->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->accessLevel->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->accessLevel->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->accessLevel->extensionAttributes['foo2']['value']);
        $accessLevelXml = $this->accessLevel->saveXML();
        $newAccessLevel = new Zend_Gdata_Calendar_Extension_AccessLevel();
        $newAccessLevel->transferFromXML($accessLevelXml);
        $this->assertEquals('bar', $newAccessLevel->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newAccessLevel->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullAccessLevelToAndFromString() {
        $this->accessLevel->transferFromXML($this->accessLevelText);
        $this->assertEquals($this->accessLevel->value, 'owner');
    }

}
