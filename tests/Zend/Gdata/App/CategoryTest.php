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
 * @package      Zend_Gdata_App
 * @subpackage UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/App/Extension/Category.php';
require_once 'Zend/Gdata/App.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_App_CategoryTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->categoryText = file_get_contents(
                'Zend/Gdata/App/_files/CategoryElementSample1.xml',
                true);
        $this->category = new Zend_Gdata_App_Extension_Category();
    }
      
    public function testEmptyCategoryShouldHaveEmptyExtensionsList() {
        $this->assertTrue(is_array($this->category->extensionElements));
        $this->assertTrue(count($this->category->extensionElements) == 0);
    }
      
    public function testNormalCategoryShouldHaveNoExtensionElements() {
        
        $this->category->scheme = 'http://schemas.google.com/g/2005#kind';
        $this->assertEquals($this->category->scheme, 'http://schemas.google.com/g/2005#kind');
        $this->assertEquals(count($this->category->extensionElements), 0);
        $newCategory = new Zend_Gdata_App_Extension_Category(); 
        $newCategory->transferFromXML($this->category->saveXML());
        $this->assertEquals(0, count($newCategory->extensionElements));
        $newCategory->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(count($newCategory->extensionElements), 1);
        $this->assertEquals($newCategory->scheme, 'http://schemas.google.com/g/2005#kind');

        /* try constructing using magic factory */
        $app = new Zend_Gdata_App();
        $newCategory2 = $app->newCategory();
        $newCategory2->transferFromXML($newCategory->saveXML());
        $this->assertEquals(count($newCategory2->extensionElements), 1);
        $this->assertEquals($newCategory2->scheme, 'http://schemas.google.com/g/2005#kind');
    }

    public function testEmptyCategoryToAndFromStringShouldMatch() {
        $categoryXml = $this->category->saveXML();
        $newCategory = new Zend_Gdata_App_Extension_Category();
        $newCategory->transferFromXML($categoryXml);
        $newCategoryXml = $newCategory->saveXML();
        $this->assertTrue($categoryXml == $newCategoryXml);
    }

    public function testCategoryWithSchemeAndTermToAndFromStringShouldMatch() {
        $this->category->scheme = 'http://schemas.google.com/g/2005#kind';
        $this->category->term = 'http://schemas.google.com/g/2005#event';
        $this->category->label = 'event kind';
        $categoryXml = $this->category->saveXML();
        $newCategory = new Zend_Gdata_App_Extension_Category();
        $newCategory->transferFromXML($categoryXml);
        $newCategoryXml = $newCategory->saveXML();
        $this->assertTrue($categoryXml == $newCategoryXml);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $newCategory->scheme);
        $this->assertEquals('http://schemas.google.com/g/2005#event', $newCategory->term);
        $this->assertEquals('event kind', $newCategory->label);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->category->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->category->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->category->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->category->extensionAttributes['foo2']['value']);
        $categoryXml = $this->category->saveXML();
        $newCategory = new Zend_Gdata_App_Extension_Category();
        $newCategory->transferFromXML($categoryXml);
        $this->assertEquals('bar', $newCategory->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newCategory->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullCategoryToAndFromString() {
        $this->category->transferFromXML($this->categoryText);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $this->category->scheme);
        $this->assertEquals('http://schemas.google.com/g/2005#event', $this->category->term);
        $this->assertEquals('event kind', $this->category->label);
    }

}
