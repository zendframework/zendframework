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
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData;
use Zend\GData\Extension;

/**
 * @category   Zend
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 */
class TransparencyTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->transparencyText = file_get_contents(
                'Zend/GData/_files/TransparencyElementSample1.xml',
                true);
        $this->transparency = new Extension\Transparency();
    }

    public function testEmptyTransparencyShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->transparency->extensionElements));
        $this->assertTrue(count($this->transparency->extensionElements) == 0);
    }

    public function testEmptyTransparencyShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->transparency->extensionAttributes));
        $this->assertTrue(count($this->transparency->extensionAttributes) == 0);
    }

    public function testSampleTransparencyShouldHaveNoExtensionElements() {
        $this->transparency->transferFromXML($this->transparencyText);
        $this->assertTrue(is_array($this->transparency->extensionElements));
        $this->assertTrue(count($this->transparency->extensionElements) == 0);
    }

    public function testSampleTransparencyShouldHaveNoExtensionAttributes() {
        $this->transparency->transferFromXML($this->transparencyText);
        $this->assertTrue(is_array($this->transparency->extensionAttributes));
        $this->assertTrue(count($this->transparency->extensionAttributes) == 0);
    }

    public function testNormalTransparencyShouldHaveNoExtensionElements() {
        $this->transparency->value = "http://schemas.google.com/g/2005#event.opaque";

        $this->assertEquals("http://schemas.google.com/g/2005#event.opaque", $this->transparency->value);

        $this->assertEquals(0, count($this->transparency->extensionElements));
        $newTransparency = new Extension\Transparency();
        $newTransparency->transferFromXML($this->transparency->saveXML());
        $this->assertEquals(0, count($newTransparency->extensionElements));
        $newTransparency->extensionElements = array(
                new \Zend\GData\App\Extension\Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newTransparency->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#event.opaque", $newTransparency->value);

        /* try constructing using magic factory */
        $gdata = new \Zend\GData\GData();
        $newTransparency2 = $gdata->newTransparency();
        $newTransparency2->transferFromXML($newTransparency->saveXML());
        $this->assertEquals(1, count($newTransparency2->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#event.opaque", $newTransparency2->value);
    }

    public function testEmptyTransparencyToAndFromStringShouldMatch() {
        $transparencyXml = $this->transparency->saveXML();
        $newTransparency = new Extension\Transparency();
        $newTransparency->transferFromXML($transparencyXml);
        $newTransparencyXml = $newTransparency->saveXML();
        $this->assertTrue($transparencyXml == $newTransparencyXml);
    }

    public function testTransparencyWithValueToAndFromStringShouldMatch() {
        $this->transparency->value = "http://schemas.google.com/g/2005#event.opaque";
        $transparencyXml = $this->transparency->saveXML();
        $newTransparency = new Extension\Transparency();
        $newTransparency->transferFromXML($transparencyXml);
        $newTransparencyXml = $newTransparency->saveXML();
        $this->assertTrue($transparencyXml == $newTransparencyXml);
        $this->assertEquals("http://schemas.google.com/g/2005#event.opaque", $this->transparency->value);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->transparency->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->transparency->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->transparency->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->transparency->extensionAttributes['foo2']['value']);
        $transparencyXml = $this->transparency->saveXML();
        $newTransparency = new Extension\Transparency();
        $newTransparency->transferFromXML($transparencyXml);
        $this->assertEquals('bar', $newTransparency->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newTransparency->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullTransparencyToAndFromString() {
        $this->transparency->transferFromXML($this->transparencyText);
        $this->assertEquals("http://schemas.google.com/g/2005#event.transparent", $this->transparency->value);
    }

}
