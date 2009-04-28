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

require_once 'Zend/Gdata/Calendar/Extension/Color.php';
require_once 'Zend/Gdata/Calendar.php';

/**
 * @package    Zend_Gdata_Calendar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Calendar_ColorTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->colorText = file_get_contents(
                'Zend/Gdata/Calendar/_files/ColorElementSample1.xml',
                true);
        $this->color = new Zend_Gdata_Calendar_Extension_Color();
    }
      
    public function testEmptyColorShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->color->extensionElements));
        $this->assertTrue(count($this->color->extensionElements) == 0);
    }

    public function testEmptyColorShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->color->extensionAttributes));
        $this->assertTrue(count($this->color->extensionAttributes) == 0);
    }

    public function testSampleColorShouldHaveNoExtensionElements() {
        $this->color->transferFromXML($this->colorText);
        $this->assertTrue(is_array($this->color->extensionElements));
        $this->assertTrue(count($this->color->extensionElements) == 0);
    }

    public function testSampleColorShouldHaveNoExtensionAttributes() {
        $this->color->transferFromXML($this->colorText);
        $this->assertTrue(is_array($this->color->extensionAttributes));
        $this->assertTrue(count($this->color->extensionAttributes) == 0);
    }
    
    public function testNormalColorShouldHaveNoExtensionElements() {
        $this->color->value = '#abcdef';
        $this->assertEquals($this->color->value, '#abcdef');
        $this->assertEquals(count($this->color->extensionElements), 0);
        $newColor = new Zend_Gdata_Calendar_Extension_Color(); 
        $newColor->transferFromXML($this->color->saveXML());
        $this->assertEquals(count($newColor->extensionElements), 0);
        $newColor->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(count($newColor->extensionElements), 1);
        $this->assertEquals($newColor->value, '#abcdef');

        /* try constructing using magic factory */
        $cal = new Zend_Gdata_Calendar();
        $newColor2 = $cal->newColor();
        $newColor2->transferFromXML($newColor->saveXML());
        $this->assertEquals(count($newColor2->extensionElements), 1);
        $this->assertEquals($newColor2->value, '#abcdef');
    }

    public function testEmptyColorToAndFromStringShouldMatch() {
        $colorXml = $this->color->saveXML();
        $newColor = new Zend_Gdata_Calendar_Extension_Color();
        $newColor->transferFromXML($colorXml);
        $newColorXml = $newColor->saveXML();
        $this->assertTrue($colorXml == $newColorXml);
    }

    public function testColorWithValueToAndFromStringShouldMatch() {
        $this->color->value = '#abcdef';
        $colorXml = $this->color->saveXML();
        $newColor = new Zend_Gdata_Calendar_Extension_Color();
        $newColor->transferFromXML($colorXml);
        $newColorXml = $newColor->saveXML();
        $this->assertTrue($colorXml == $newColorXml);
        $this->assertEquals('#abcdef', $newColor->value);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->color->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->color->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->color->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->color->extensionAttributes['foo2']['value']);
        $colorXml = $this->color->saveXML();
        $newColor = new Zend_Gdata_Calendar_Extension_Color();
        $newColor->transferFromXML($colorXml);
        $this->assertEquals('bar', $newColor->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newColor->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullColorToAndFromString() {
        $this->color->transferFromXML($this->colorText);
        $this->assertEquals($this->color->value, '#5A6986');
    }

}
