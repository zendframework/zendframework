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

require_once 'Zend/Gdata/Calendar/Extension/WebContent.php';
require_once 'Zend/Gdata/Calendar.php';

/**
 * @package    Zend_Gdata_Calendar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Calendar_WebContentTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->webContentText = file_get_contents(
                'Zend/Gdata/Calendar/_files/WebContentElementSample1.xml',
                true);
        $this->webContent = new Zend_Gdata_Calendar_Extension_WebContent();
    }
    
    public function testEmptyWebContentShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->webContent->extensionElements));
        $this->assertTrue(count($this->webContent->extensionElements) == 0);
    }
    
    public function testEmptyWebContentShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->webContent->extensionAttributes));
        $this->assertTrue(count($this->webContent->extensionAttributes) == 0);
    }
    
    public function testSampleWebContentShouldHaveNoExtensionElements() {
        $this->webContent->transferFromXML($this->webContentText);
        $this->assertTrue(is_array($this->webContent->extensionElements));
        $this->assertTrue(count($this->webContent->extensionElements) == 0);
    }
    
    public function testSampleWebContentShouldHaveNoExtensionAttributes() {
        $this->webContent->transferFromXML($this->webContentText);
        $this->assertTrue(is_array($this->webContent->extensionAttributes));
        $this->assertTrue(count($this->webContent->extensionAttributes) == 0);
    }
    
    public function testNormalWebContentShouldHaveNoExtensionElements() {
        $this->webContent->url = "http://nowhere.invalid/";
        $this->webContent->height = "100";
        $this->webContent->width = "200";
        
        $this->assertEquals($this->webContent->url, "http://nowhere.invalid/");
        $this->assertEquals($this->webContent->height, "100");
        $this->assertEquals($this->webContent->width, "200");
        
        $this->assertEquals(count($this->webContent->extensionElements), 0);
        $newWebContent = new Zend_Gdata_Calendar_Extension_WebContent(); 
        $newWebContent->transferFromXML($this->webContent->saveXML());
        $this->assertEquals(count($newWebContent->extensionElements), 0);
        $newWebContent->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(count($newWebContent->extensionElements), 1);
        $this->assertEquals($newWebContent->url, "http://nowhere.invalid/");
        $this->assertEquals($newWebContent->height, "100");
        $this->assertEquals($newWebContent->width, "200");

        /* try constructing using magic factory */
        $cal = new Zend_Gdata_Calendar();
        $newWebContent2 = $cal->newWebContent();
        $newWebContent2->transferFromXML($newWebContent->saveXML());
        $this->assertEquals(count($newWebContent2->extensionElements), 1);
        $this->assertEquals($newWebContent2->url, "http://nowhere.invalid/");
        $this->assertEquals($newWebContent2->height, "100");
        $this->assertEquals($newWebContent2->width, "200");
    }

    public function testEmptyWebContentToAndFromStringShouldMatch() {
        $webContentXml = $this->webContent->saveXML();
        $newWebContent = new Zend_Gdata_Calendar_Extension_WebContent();
        $newWebContent->transferFromXML($webContentXml);
        $newWebContentXml = $newWebContent->saveXML();
        $this->assertTrue($webContentXml == $newWebContentXml);
    }

    public function testWebContentWithValueToAndFromStringShouldMatch() {
        $this->webContent->url = "http://nowhere.invalid/";
        $this->webContent->height = "100";
        $this->webContent->width = "200";
        $webContentXml = $this->webContent->saveXML();
        $newWebContent = new Zend_Gdata_Calendar_Extension_WebContent();
        $newWebContent->transferFromXML($webContentXml);
        $newWebContentXml = $newWebContent->saveXML();
        $this->assertTrue($webContentXml == $newWebContentXml);
        $this->assertEquals($this->webContent->url, "http://nowhere.invalid/");
        $this->assertEquals($this->webContent->height, "100");
        $this->assertEquals($this->webContent->width, "200");
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->webContent->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->webContent->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->webContent->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->webContent->extensionAttributes['foo2']['value']);
        $webContentXml = $this->webContent->saveXML();
        $newWebContent = new Zend_Gdata_Calendar_Extension_WebContent();
        $newWebContent->transferFromXML($webContentXml);
        $this->assertEquals('bar', $newWebContent->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newWebContent->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullWebContentToAndFromString() {
        $this->webContent->transferFromXML($this->webContentText);
        $this->assertEquals($this->webContent->url, "http://www.google.com/logos/july4th06.gif");
        $this->assertEquals($this->webContent->height, "120");
        $this->assertEquals($this->webContent->width, "276");
    }

}
