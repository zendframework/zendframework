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
 * @category   Zend
 * @package    Zend_GData_Calendar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\Calendar;
use Zend\GData\Calendar\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_Calendar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Calendar
 */
class SelectedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->selectedText = file_get_contents(
                'Zend/GData/Calendar/_files/SelectedElementSample1.xml',
                true);
        $this->selected = new Extension\Selected();
    }

    public function testEmptySelectedShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->selected->extensionElements));
        $this->assertTrue(count($this->selected->extensionElements) == 0);
    }

    public function testEmptySelectedShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->selected->extensionAttributes));
        $this->assertTrue(count($this->selected->extensionAttributes) == 0);
    }

    public function testSampleSelectedShouldHaveNoExtensionElements() {
        $this->selected->transferFromXML($this->selectedText);
        $this->assertTrue(is_array($this->selected->extensionElements));
        $this->assertTrue(count($this->selected->extensionElements) == 0);
    }

    public function testSampleSelectedShouldHaveNoExtensionAttributes() {
        $this->selected->transferFromXML($this->selectedText);
        $this->assertTrue(is_array($this->selected->extensionAttributes));
        $this->assertTrue(count($this->selected->extensionAttributes) == 0);
    }

    public function testNormalSelectedShouldHaveNoExtensionElements() {
        $this->selected->value = true;
        $this->assertEquals($this->selected->value, true);
        $this->assertEquals(count($this->selected->extensionElements), 0);
        $newSelected = new Extension\Selected();
        $newSelected->transferFromXML($this->selected->saveXML());
        $this->assertEquals(count($newSelected->extensionElements), 0);
        $newSelected->extensionElements = array(
                new \Zend\GData\App\Extension\Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(count($newSelected->extensionElements), 1);
        $this->assertEquals($newSelected->value, true);

        /* try constructing using magic factory */
        $cal = new \Zend\GData\Calendar();
        $newSelected2 = $cal->newSelected();
        $newSelected2->transferFromXML($newSelected->saveXML());
        $this->assertEquals(count($newSelected2->extensionElements), 1);
        $this->assertEquals($newSelected2->value, true);
    }

    public function testEmptySelectedToAndFromStringShouldMatch() {
        $selectedXml = $this->selected->saveXML();
        $newSelected = new Extension\Selected();
        $newSelected->transferFromXML($selectedXml);
        $newSelectedXml = $newSelected->saveXML();
        $this->assertTrue($selectedXml == $newSelectedXml);
    }

    public function testSelectedWithValueToAndFromStringShouldMatch() {
        $this->selected->value = true;
        $selectedXml = $this->selected->saveXML();
        $newSelected = new Extension\Selected();
        $newSelected->transferFromXML($selectedXml);
        $newSelectedXml = $newSelected->saveXML();
        $this->assertTrue($selectedXml == $newSelectedXml);
        $this->assertEquals(true, $newSelected->value);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->selected->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->selected->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->selected->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->selected->extensionAttributes['foo2']['value']);
        $selectedXml = $this->selected->saveXML();
        $newSelected = new Extension\Selected();
        $newSelected->transferFromXML($selectedXml);
        $this->assertEquals('bar', $newSelected->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newSelected->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullSelectedToAndFromString() {
        $this->selected->transferFromXML($this->selectedText);
        $this->assertEquals($this->selected->value, false);
    }

}
