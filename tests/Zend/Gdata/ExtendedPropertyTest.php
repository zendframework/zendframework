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

require_once 'Zend/Gdata/Extension/ExtendedProperty.php';
require_once 'Zend/Gdata.php';

/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_ExtendedPropertyTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->extendedPropertyText = file_get_contents(
                'Zend/Gdata/_files/ExtendedPropertyElementSample1.xml',
                true);
        $this->extendedProperty = new Zend_Gdata_Extension_ExtendedProperty();
    }
    
    public function testEmptyExtendedPropertyShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->extendedProperty->extensionElements));
        $this->assertTrue(count($this->extendedProperty->extensionElements) == 0);
    }

    public function testEmptyExtendedPropertyShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->extendedProperty->extensionAttributes));
        $this->assertTrue(count($this->extendedProperty->extensionAttributes) == 0);
    }

    public function testSampleExtendedPropertyShouldHaveNoExtensionElements() {
        $this->extendedProperty->transferFromXML($this->extendedPropertyText);
        $this->assertTrue(is_array($this->extendedProperty->extensionElements));
        $this->assertTrue(count($this->extendedProperty->extensionElements) == 0);
    }

    public function testSampleExtendedPropertyShouldHaveNoExtensionAttributes() {
        $this->extendedProperty->transferFromXML($this->extendedPropertyText);
        $this->assertTrue(is_array($this->extendedProperty->extensionAttributes));
        $this->assertTrue(count($this->extendedProperty->extensionAttributes) == 0);
    }
    
    public function testNormalExtendedPropertyShouldHaveNoExtensionElements() {
        $this->extendedProperty->name = "http://www.example.com/schemas/2007#mycal.foo";
        $this->extendedProperty->value = "5678";
        
        $this->assertEquals("http://www.example.com/schemas/2007#mycal.foo", $this->extendedProperty->name);
        $this->assertEquals("5678", $this->extendedProperty->value);
                
        $this->assertEquals(0, count($this->extendedProperty->extensionElements));
        $newExtendedProperty = new Zend_Gdata_Extension_ExtendedProperty(); 
        $newExtendedProperty->transferFromXML($this->extendedProperty->saveXML());
        $this->assertEquals(0, count($newExtendedProperty->extensionElements));
        $newExtendedProperty->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newExtendedProperty->extensionElements));
        $this->assertEquals("http://www.example.com/schemas/2007#mycal.foo", $newExtendedProperty->name);
        $this->assertEquals("5678", $newExtendedProperty->value);

        /* try constructing using magic factory */
        $gdata = new Zend_Gdata();
        $newExtendedProperty2 = $gdata->newExtendedProperty();
        $newExtendedProperty2->transferFromXML($newExtendedProperty->saveXML());
        $this->assertEquals(1, count($newExtendedProperty2->extensionElements));
        $this->assertEquals("http://www.example.com/schemas/2007#mycal.foo", $newExtendedProperty2->name);
        $this->assertEquals("5678", $newExtendedProperty2->value);
    }

    public function testEmptyExtendedPropertyToAndFromStringShouldMatch() {
        $extendedPropertyXml = $this->extendedProperty->saveXML();
        $newExtendedProperty = new Zend_Gdata_Extension_ExtendedProperty();
        $newExtendedProperty->transferFromXML($extendedPropertyXml);
        $newExtendedPropertyXml = $newExtendedProperty->saveXML();
        $this->assertTrue($extendedPropertyXml == $newExtendedPropertyXml);
    }

    public function testExtendedPropertyWithValueToAndFromStringShouldMatch() {
        $this->extendedProperty->name = "http://www.example.com/schemas/2007#mycal.foo";
        $this->extendedProperty->value = "5678";
        $extendedPropertyXml = $this->extendedProperty->saveXML();
        $newExtendedProperty = new Zend_Gdata_Extension_ExtendedProperty();
        $newExtendedProperty->transferFromXML($extendedPropertyXml);
        $newExtendedPropertyXml = $newExtendedProperty->saveXML();
        $this->assertTrue($extendedPropertyXml == $newExtendedPropertyXml);
        $this->assertEquals("http://www.example.com/schemas/2007#mycal.foo", $this->extendedProperty->name);
        $this->assertEquals("5678", $this->extendedProperty->value);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->extendedProperty->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->extendedProperty->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->extendedProperty->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->extendedProperty->extensionAttributes['foo2']['value']);
        $extendedPropertyXml = $this->extendedProperty->saveXML();
        $newExtendedProperty = new Zend_Gdata_Extension_ExtendedProperty();
        $newExtendedProperty->transferFromXML($extendedPropertyXml);
        $this->assertEquals('bar', $newExtendedProperty->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newExtendedProperty->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullExtendedPropertyToAndFromString() {
        $this->extendedProperty->transferFromXML($this->extendedPropertyText);
        $this->assertEquals("http://www.example.com/schemas/2007#mycal.id", $this->extendedProperty->name);
        $this->assertEquals("1234", $this->extendedProperty->value);
    }

}
