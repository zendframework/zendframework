<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\GApps;

use Zend\GData\GApps;
use Zend\GData\App\Extension\Element;
use Zend\GData\GApps\Extension\Property;

/**
 * @category   Zend
 * @package    Zend_Gdata_Gapps
 * @subpackage UnitTests
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Gapps
 */
class PropertyTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->thePropertyText = file_get_contents(
                'Zend/GData/GApps/_files/PropertyElementSample1.xml',
                true);
        $this->theProperty = new Property();
    }

    public function testEmptyPropertyShouldHaveNoExtensionElements()
    {
        $this->assertTrue(is_array($this->theProperty->extensionElements));
        $this->assertTrue(count($this->theProperty->extensionElements) == 0);
    }

    public function testEmptyPropertyShouldHaveNoExtensionAttributes()
    {
        $this->assertTrue(is_array($this->theProperty->extensionAttributes));
        $this->assertTrue(count($this->theProperty->extensionAttributes) == 0);
    }

    public function testSamplePropertyShouldHaveNoExtensionElements()
    {
        $this->theProperty->transferFromXML($this->thePropertyText);
        $this->assertTrue(is_array($this->theProperty->extensionElements));
        $this->assertTrue(count($this->theProperty->extensionElements) == 0);
    }

    public function testSamplePropertyShouldHaveNoExtensionAttributes()
    {
        $this->theProperty->transferFromXML($this->thePropertyText);
        $this->assertTrue(is_array($this->theProperty->extensionAttributes));
        $this->assertTrue(count($this->theProperty->extensionAttributes) == 0);
    }

    public function testNormalPropertyShouldHaveNoExtensionElements()
    {
        $this->theProperty->name = "foo";
        $this->theProperty->value = "bar";

        $this->assertEquals("foo", $this->theProperty->name);
        $this->assertEquals("bar", $this->theProperty->value);

        $this->assertEquals(0, count($this->theProperty->extensionElements));
        $newProperty = new Property();
        $newProperty->transferFromXML($this->theProperty->saveXML());
        $this->assertEquals(0, count($newProperty->extensionElements));
        $newProperty->extensionElements = array(
                new Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newProperty->extensionElements));
        $this->assertEquals("foo", $newProperty->name);
        $this->assertEquals("bar", $newProperty->value);

        /* try constructing using magic factory */
        $gdata = new GApps();
        $newProperty2 = $gdata->newProperty();
        $newProperty2->transferFromXML($newProperty->saveXML());
        $this->assertEquals(1, count($newProperty2->extensionElements));
        $this->assertEquals("foo", $newProperty2->name);
        $this->assertEquals("bar", $newProperty2->value);
    }

    public function testEmptyPropertyToAndFromStringShouldMatch()
    {
        $propertyXml = $this->theProperty->saveXML();
        $newProperty = new Property();
        $newProperty->transferFromXML($propertyXml);
        $newPropertyXml = $newProperty->saveXML();
        $this->assertTrue($propertyXml == $newPropertyXml);
    }

    public function testPropertyWithValueToAndFromStringShouldMatch()
    {
        $this->theProperty->name = "foo2";
        $this->theProperty->value = "bar2";
        $propertyXml = $this->theProperty->saveXML();
        $newProperty = new Property();
        $newProperty->transferFromXML($propertyXml);
        $newPropertyXml = $newProperty->saveXML();
        $this->assertTrue($propertyXml == $newPropertyXml);
        $this->assertEquals("foo2", $this->theProperty->name);
        $this->assertEquals("bar2", $this->theProperty->value);
    }

    public function testExtensionAttributes()
    {
        $extensionAttributes = $this->theProperty->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->theProperty->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->theProperty->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->theProperty->extensionAttributes['foo2']['value']);
        $propertyXml = $this->theProperty->saveXML();
        $newProperty = new Property();
        $newProperty->transferFromXML($propertyXml);
        $this->assertEquals('bar', $newProperty->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newProperty->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullNameToAndFromString()
    {
        $this->theProperty->transferFromXML($this->thePropertyText);
        $this->assertEquals("Some Name", $this->theProperty->name);
        $this->assertEquals("Some Value", $this->theProperty->value);
    }

}
