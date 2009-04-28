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

require_once 'Zend/Gdata/Extension/Recurrence.php';
require_once 'Zend/Gdata.php';

/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_RecurrenceTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->recurrenceText = file_get_contents(
                'Zend/Gdata/_files/RecurrenceElementSample1.xml',
                true);
        $this->recurrence = new Zend_Gdata_Extension_Recurrence();
    }
    
    public function testEmptyRecurrenceShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->recurrence->extensionElements));
        $this->assertTrue(count($this->recurrence->extensionElements) == 0);
    }

    public function testEmptyRecurrenceShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->recurrence->extensionAttributes));
        $this->assertTrue(count($this->recurrence->extensionAttributes) == 0);
    }

    public function testSampleRecurrenceShouldHaveNoExtensionElements() {
        $this->recurrence->transferFromXML($this->recurrenceText);
        $this->assertTrue(is_array($this->recurrence->extensionElements));
        $this->assertTrue(count($this->recurrence->extensionElements) == 0);
    }

    public function testSampleRecurrenceShouldHaveNoExtensionAttributes() {
        $this->recurrence->transferFromXML($this->recurrenceText);
        $this->assertTrue(is_array($this->recurrence->extensionAttributes));
        $this->assertTrue(count($this->recurrence->extensionAttributes) == 0);
    }
    
    public function testNormalRecurrenceShouldHaveNoExtensionElements() {
        $this->recurrence->text = "Foo";
        
        $this->assertEquals("Foo", $this->recurrence->text);
                
        $this->assertEquals(0, count($this->recurrence->extensionElements));
        $newRecurrence = new Zend_Gdata_Extension_Recurrence(); 
        $newRecurrence->transferFromXML($this->recurrence->saveXML());
        $this->assertEquals(0, count($newRecurrence->extensionElements));
        $newRecurrence->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newRecurrence->extensionElements));
        $this->assertEquals("Foo", $newRecurrence->text);

        /* try constructing using magic factory */
        $gdata = new Zend_Gdata();
        $newRecurrence2 = $gdata->newRecurrence();
        $newRecurrence2->transferFromXML($newRecurrence->saveXML());
        $this->assertEquals(1, count($newRecurrence2->extensionElements));
        $this->assertEquals("Foo", $newRecurrence2->text);
    }

    public function testEmptyRecurrenceToAndFromStringShouldMatch() {
        $recurrenceXml = $this->recurrence->saveXML();
        $newRecurrence = new Zend_Gdata_Extension_Recurrence();
        $newRecurrence->transferFromXML($recurrenceXml);
        $newRecurrenceXml = $newRecurrence->saveXML();
        $this->assertTrue($recurrenceXml == $newRecurrenceXml);
    }

    public function testRecurrenceWithValueToAndFromStringShouldMatch() {
        $this->recurrence->text = "Foo";
        $recurrenceXml = $this->recurrence->saveXML();
        $newRecurrence = new Zend_Gdata_Extension_Recurrence();
        $newRecurrence->transferFromXML($recurrenceXml);
        $newRecurrenceXml = $newRecurrence->saveXML();
        $this->assertTrue($recurrenceXml == $newRecurrenceXml);
        $this->assertEquals("Foo", $this->recurrence->text);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->recurrence->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->recurrence->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->recurrence->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->recurrence->extensionAttributes['foo2']['value']);
        $recurrenceXml = $this->recurrence->saveXML();
        $newRecurrence = new Zend_Gdata_Extension_Recurrence();
        $newRecurrence->transferFromXML($recurrenceXml);
        $this->assertEquals('bar', $newRecurrence->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newRecurrence->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullRecurrenceToAndFromString() {
        $this->recurrence->transferFromXML($this->recurrenceText);
        $this->assertEquals("DTSTART;VALUE=DATE:20070501\nDTEND;VALUE=DATE:20070502\nRRULE:FREQ=WEEKLY;BYDAY=Tu;UNTIL=20070904", $this->recurrence->text);
    }

}
