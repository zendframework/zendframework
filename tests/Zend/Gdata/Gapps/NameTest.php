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

require_once 'Zend/Gdata/Gapps/Extension/Name.php';
require_once 'Zend/Gdata.php';

/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Gapps_NameTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->theNameText = file_get_contents(
                'Zend/Gdata/Gapps/_files/NameElementSample1.xml',
                true);
        $this->theName = new Zend_Gdata_Gapps_Extension_Name();
    }

    public function testEmptyNameShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->theName->extensionElements));
        $this->assertTrue(count($this->theName->extensionElements) == 0);
    }

    public function testEmptyNameShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->theName->extensionAttributes));
        $this->assertTrue(count($this->theName->extensionAttributes) == 0);
    }

    public function testSampleNameShouldHaveNoExtensionElements() {
        $this->theName->transferFromXML($this->theNameText);
        $this->assertTrue(is_array($this->theName->extensionElements));
        $this->assertTrue(count($this->theName->extensionElements) == 0);
    }

    public function testSampleNameShouldHaveNoExtensionAttributes() {
        $this->theName->transferFromXML($this->theNameText);
        $this->assertTrue(is_array($this->theName->extensionAttributes));
        $this->assertTrue(count($this->theName->extensionAttributes) == 0);
    }

    public function testNormalNameShouldHaveNoExtensionElements() {
        $this->theName->givenName = "John";
        $this->theName->familyName = "Doe";

        $this->assertEquals("John", $this->theName->givenName);
        $this->assertEquals("Doe", $this->theName->familyName);

        $this->assertEquals(0, count($this->theName->extensionElements));
        $newName = new Zend_Gdata_Gapps_Extension_Name();
        $newName->transferFromXML($this->theName->saveXML());
        $this->assertEquals(0, count($newName->extensionElements));
        $newName->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newName->extensionElements));
        $this->assertEquals("John", $newName->givenName);
        $this->assertEquals("Doe", $newName->familyName);

        /* try constructing using magic factory */
        $gdata = new Zend_Gdata_Gapps();
        $newName2 = $gdata->newName();
        $newName2->transferFromXML($newName->saveXML());
        $this->assertEquals(1, count($newName2->extensionElements));
        $this->assertEquals("John", $newName2->givenName);
        $this->assertEquals("Doe", $newName2->familyName);
    }

    public function testEmptyNameToAndFromStringShouldMatch() {
        $nameXml = $this->theName->saveXML();
        $newName = new Zend_Gdata_Gapps_Extension_Name();
        $newName->transferFromXML($nameXml);
        $newNameXml = $newName->saveXML();
        $this->assertTrue($nameXml == $newNameXml);
    }

    public function testNameWithValueToAndFromStringShouldMatch() {
        $this->theName->givenName = "John";
        $this->theName->familyName = "Doe";
        $nameXml = $this->theName->saveXML();
        $newName = new Zend_Gdata_Gapps_Extension_Name();
        $newName->transferFromXML($nameXml);
        $newNameXml = $newName->saveXML();
        $this->assertTrue($nameXml == $newNameXml);
        $this->assertEquals("John", $this->theName->givenName);
        $this->assertEquals("Doe", $this->theName->familyName);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->theName->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->theName->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->theName->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->theName->extensionAttributes['foo2']['value']);
        $nameXml = $this->theName->saveXML();
        $newName = new Zend_Gdata_Gapps_Extension_Name();
        $newName->transferFromXML($nameXml);
        $this->assertEquals('bar', $newName->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newName->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullNameToAndFromString() {
        $this->theName->transferFromXML($this->theNameText);
        $this->assertEquals("Susan", $this->theName->givenName);
        $this->assertEquals("Jones", $this->theName->familyName);
    }

}
