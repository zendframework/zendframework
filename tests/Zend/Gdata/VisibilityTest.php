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

require_once 'Zend/Gdata/Extension/Visibility.php';
require_once 'Zend/Gdata.php';

/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_VisibilityTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->visibilityText = file_get_contents(
                'Zend/Gdata/_files/VisibilityElementSample1.xml',
                true);
        $this->visibility = new Zend_Gdata_Extension_Visibility();
    }
    
    public function testEmptyVisibilityShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->visibility->extensionElements));
        $this->assertTrue(count($this->visibility->extensionElements) == 0);
    }

    public function testEmptyVisibilityShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->visibility->extensionAttributes));
        $this->assertTrue(count($this->visibility->extensionAttributes) == 0);
    }

    public function testSampleVisibilityShouldHaveNoExtensionElements() {
        $this->visibility->transferFromXML($this->visibilityText);
        $this->assertTrue(is_array($this->visibility->extensionElements));
        $this->assertTrue(count($this->visibility->extensionElements) == 0);
    }

    public function testSampleVisibilityShouldHaveNoExtensionAttributes() {
        $this->visibility->transferFromXML($this->visibilityText);
        $this->assertTrue(is_array($this->visibility->extensionAttributes));
        $this->assertTrue(count($this->visibility->extensionAttributes) == 0);
    }
    
    public function testNormalVisibilityShouldHaveNoExtensionElements() {
        $this->visibility->value = "http://schemas.google.com/g/2005#event.private";
        
        $this->assertEquals("http://schemas.google.com/g/2005#event.private", $this->visibility->value);
                
        $this->assertEquals(0, count($this->visibility->extensionElements));
        $newVisibility = new Zend_Gdata_Extension_Visibility(); 
        $newVisibility->transferFromXML($this->visibility->saveXML());
        $this->assertEquals(0, count($newVisibility->extensionElements));
        $newVisibility->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newVisibility->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#event.private", $newVisibility->value);

        /* try constructing using magic factory */
        $gdata = new Zend_Gdata();
        $newVisibility2 = $gdata->newVisibility();
        $newVisibility2->transferFromXML($newVisibility->saveXML());
        $this->assertEquals(1, count($newVisibility2->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#event.private", $newVisibility2->value);
    }

    public function testEmptyVisibilityToAndFromStringShouldMatch() {
        $visibilityXml = $this->visibility->saveXML();
        $newVisibility = new Zend_Gdata_Extension_Visibility();
        $newVisibility->transferFromXML($visibilityXml);
        $newVisibilityXml = $newVisibility->saveXML();
        $this->assertTrue($visibilityXml == $newVisibilityXml);
    }

    public function testVisibilityWithValueToAndFromStringShouldMatch() {
        $this->visibility->value = "http://schemas.google.com/g/2005#event.private";
        $visibilityXml = $this->visibility->saveXML();
        $newVisibility = new Zend_Gdata_Extension_Visibility();
        $newVisibility->transferFromXML($visibilityXml);
        $newVisibilityXml = $newVisibility->saveXML();
        $this->assertTrue($visibilityXml == $newVisibilityXml);
        $this->assertEquals("http://schemas.google.com/g/2005#event.private", $this->visibility->value);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->visibility->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->visibility->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->visibility->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->visibility->extensionAttributes['foo2']['value']);
        $visibilityXml = $this->visibility->saveXML();
        $newVisibility = new Zend_Gdata_Extension_Visibility();
        $newVisibility->transferFromXML($visibilityXml);
        $this->assertEquals('bar', $newVisibility->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newVisibility->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullVisibilityToAndFromString() {
        $this->visibility->transferFromXML($this->visibilityText);
        $this->assertEquals("http://schemas.google.com/g/2005#event.confidential", $this->visibility->value);
    }

}
