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
class QuickAddTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->quickAddText = file_get_contents(
                'Zend/GData/Calendar/_files/QuickAddElementSample1.xml',
                true);
        $this->quickAdd = new Extension\QuickAdd();
    }

    public function testEmptyQuickAddShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->quickAdd->extensionElements));
        $this->assertTrue(count($this->quickAdd->extensionElements) == 0);
    }

    public function testEmptyQuickAddShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->quickAdd->extensionAttributes));
        $this->assertTrue(count($this->quickAdd->extensionAttributes) == 0);
    }

    public function testSampleQuickAddShouldHaveNoExtensionElements() {
        $this->quickAdd->transferFromXML($this->quickAddText);
        $this->assertTrue(is_array($this->quickAdd->extensionElements));
        $this->assertTrue(count($this->quickAdd->extensionElements) == 0);
    }

    public function testSampleQuickAddShouldHaveNoExtensionAttributes() {
        $this->quickAdd->transferFromXML($this->quickAddText);
        $this->assertTrue(is_array($this->quickAdd->extensionAttributes));
        $this->assertTrue(count($this->quickAdd->extensionAttributes) == 0);
    }

    public function testNormalQuickAddShouldHaveNoExtensionElements() {
        $this->quickAdd->value = false;
        $this->assertEquals($this->quickAdd->value, false);
        $this->assertEquals(count($this->quickAdd->extensionElements), 0);
        $newQuickAdd = new Extension\QuickAdd();
        $newQuickAdd->transferFromXML($this->quickAdd->saveXML());
        $this->assertEquals(count($newQuickAdd->extensionElements), 0);
        $newQuickAdd->extensionElements = array(
                new \Zend\GData\App\Extension\Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(count($newQuickAdd->extensionElements), 1);
        $this->assertEquals($newQuickAdd->value, false);

        /* try constructing using magic factory */
        $cal = new \Zend\GData\Calendar();
        $newQuickAdd2 = $cal->newQuickAdd();
        $newQuickAdd2->transferFromXML($newQuickAdd->saveXML());
        $this->assertEquals(count($newQuickAdd2->extensionElements), 1);
        $this->assertEquals($newQuickAdd2->value, false);
    }

    public function testEmptyQuickAddToAndFromStringShouldMatch() {
        $quickAddXml = $this->quickAdd->saveXML();
        $newQuickAdd = new Extension\QuickAdd();
        $newQuickAdd->transferFromXML($quickAddXml);
        $newQuickAddXml = $newQuickAdd->saveXML();
        $this->assertTrue($quickAddXml == $newQuickAddXml);
    }

    public function testQuickAddWithValueToAndFromStringShouldMatch() {
        $this->quickAdd->value = false;
        $quickAddXml = $this->quickAdd->saveXML();
        $newQuickAdd = new Extension\QuickAdd();
        $newQuickAdd->transferFromXML($quickAddXml);
        $newQuickAddXml = $newQuickAdd->saveXML();
        $this->assertTrue($quickAddXml == $newQuickAddXml);
        $this->assertEquals(false, $newQuickAdd->value);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->quickAdd->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->quickAdd->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->quickAdd->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->quickAdd->extensionAttributes['foo2']['value']);
        $quickAddXml = $this->quickAdd->saveXML();
        $newQuickAdd = new Extension\QuickAdd();
        $newQuickAdd->transferFromXML($quickAddXml);
        $this->assertEquals('bar', $newQuickAdd->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newQuickAdd->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullQuickAddToAndFromString() {
        $this->quickAdd->transferFromXML($this->quickAddText);
        $this->assertEquals($this->quickAdd->value, true);
    }

}
