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

require_once 'Zend/Gdata/Calendar/Extension/Link.php';
require_once 'Zend/Gdata/Calendar/Extension/WebContent.php';
require_once 'Zend/Gdata/Calendar.php';

/**
 * @package    Zend_Gdata_Calendar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Calendar_LinkTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->linkText = file_get_contents(
                'Zend/Gdata/Calendar/_files/LinkElementSample1.xml',
                true);
        $this->link = new Zend_Gdata_Calendar_Extension_Link();
    }
      
    public function testEmptyLinkShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->link->extensionElements));
        $this->assertTrue(count($this->link->extensionElements) == 0);
    }

    public function testEmptyLinkShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->link->extensionAttributes));
        $this->assertTrue(count($this->link->extensionAttributes) == 0);
    }

    public function testSampleLinkShouldHaveNoExtensionElements() {
        $this->link->transferFromXML($this->linkText);
        $this->assertTrue(is_array($this->link->extensionElements));
        $this->assertTrue(count($this->link->extensionElements) == 0);
    }

    public function testSampleLinkShouldHaveNoExtensionAttributes() {
        $this->link->transferFromXML($this->linkText);
        $this->assertTrue(is_array($this->link->extensionAttributes));
        $this->assertTrue(count($this->link->extensionAttributes) == 0);
    }
    
    public function testNormalLinkShouldHaveNoExtensionElements() {
        $this->link->rel = "http://nowhere.invalid/";
        $this->link->title = "Somewhere";
        $this->link->href = "http://somewhere.invalid/";
        $this->link->type = "text/plain";
        $this->link->webContent = new Zend_Gdata_Calendar_Extension_WebContent("a", "1", "2");
        
        $this->assertEquals($this->link->rel, "http://nowhere.invalid/");
        $this->assertEquals($this->link->title, "Somewhere");
        $this->assertEquals($this->link->href, "http://somewhere.invalid/");
        $this->assertEquals($this->link->type, "text/plain");
        $this->assertEquals($this->link->webcontent->url, "a");
        $this->assertEquals($this->link->webcontent->height, "1");
        $this->assertEquals($this->link->webcontent->width, "2");
        
        $this->assertEquals(count($this->link->extensionElements), 0);
        $newLink = new Zend_Gdata_Calendar_Extension_Link(); 
        $newLink->transferFromXML($this->link->saveXML());
        $this->assertEquals(count($newLink->extensionElements), 0);
        $newLink->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(count($newLink->extensionElements), 1);
        $this->assertEquals($newLink->rel, "http://nowhere.invalid/");
        $this->assertEquals($newLink->title, "Somewhere");
        $this->assertEquals($newLink->href, "http://somewhere.invalid/");
        $this->assertEquals($newLink->type, "text/plain");
        $this->assertEquals($newLink->webcontent->url, "a");
        $this->assertEquals($newLink->webcontent->height, "1");
        $this->assertEquals($newLink->webcontent->width, "2");

        /* try constructing using magic factory */
        $cal = new Zend_Gdata_Calendar();
        $newLink2 = $cal->newLink();
        $newLink2->transferFromXML($newLink->saveXML());
        $this->assertEquals(count($newLink2->extensionElements), 1);
        $this->assertEquals($newLink2->rel, "http://nowhere.invalid/");
        $this->assertEquals($newLink2->title, "Somewhere");
        $this->assertEquals($newLink2->href, "http://somewhere.invalid/");
        $this->assertEquals($newLink2->type, "text/plain");
        $this->assertEquals($newLink2->webcontent->url, "a");
        $this->assertEquals($newLink2->webcontent->height, "1");
        $this->assertEquals($newLink2->webcontent->width, "2");
    }

    public function testEmptyLinkToAndFromStringShouldMatch() {
        $linkXml = $this->link->saveXML();
        $newLink = new Zend_Gdata_Calendar_Extension_Link();
        $newLink->transferFromXML($linkXml);
        $newLinkXml = $newLink->saveXML();
        $this->assertTrue($linkXml == $newLinkXml);
    }

    public function testLinkWithValueToAndFromStringShouldMatch() {
        $this->link->rel = "http://nowhere.invalid/";
        $this->link->title = "Somewhere";
        $this->link->href = "http://somewhere.invalid/";
        $this->link->type = "text/plain";
        $this->link->webContent = new Zend_Gdata_Calendar_Extension_WebContent("a", "1", "2");
        $linkXml = $this->link->saveXML();
        $newLink = new Zend_Gdata_Calendar_Extension_Link();
        $newLink->transferFromXML($linkXml);
        $newLinkXml = $newLink->saveXML();
        $this->assertTrue($linkXml == $newLinkXml);
        $this->assertEquals($this->link->rel, "http://nowhere.invalid/");
        $this->assertEquals($this->link->title, "Somewhere");
        $this->assertEquals($this->link->href, "http://somewhere.invalid/");
        $this->assertEquals($this->link->type, "text/plain");
        $this->assertEquals($this->link->webcontent->url, "a");
        $this->assertEquals($this->link->webcontent->height, "1");
        $this->assertEquals($this->link->webcontent->width, "2");
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->link->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->link->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->link->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->link->extensionAttributes['foo2']['value']);
        $linkXml = $this->link->saveXML();
        $newLink = new Zend_Gdata_Calendar_Extension_Link();
        $newLink->transferFromXML($linkXml);
        $this->assertEquals('bar', $newLink->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newLink->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullLinkToAndFromString() {
        $this->link->transferFromXML($this->linkText);
        $this->assertEquals($this->link->rel, "http://schemas.google.com/gCal/2005/webContent");
        $this->assertEquals($this->link->title, "Independence Day");
        $this->assertEquals($this->link->href, "http://www.google.com/calendar/images/google-holiday.gif");
        $this->assertEquals($this->link->type, "image/gif");
        $this->assertEquals($this->link->webcontent->url, "http://www.google.com/logos/july4th06.gif");
        $this->assertEquals($this->link->webcontent->height, "120");
        $this->assertEquals($this->link->webcontent->width, "276");
    }

}
