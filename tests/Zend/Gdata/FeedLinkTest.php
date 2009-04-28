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

require_once 'Zend/Gdata/Extension/FeedLink.php';
require_once 'Zend/Gdata.php';

/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_FeedLinkTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->feedLinkText = file_get_contents(
                'Zend/Gdata/_files/FeedLinkElementSample1.xml',
                true);
        $this->feedLink = new Zend_Gdata_Extension_FeedLink();
    }
    
    public function testEmptyFeedLinkShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->feedLink->extensionElements));
        $this->assertTrue(count($this->feedLink->extensionElements) == 0);
    }

    public function testEmptyFeedLinkShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->feedLink->extensionAttributes));
        $this->assertTrue(count($this->feedLink->extensionAttributes) == 0);
    }

    public function testSampleFeedLinkShouldHaveNoExtensionElements() {
        $this->feedLink->transferFromXML($this->feedLinkText);
        $this->assertTrue(is_array($this->feedLink->extensionElements));
        $this->assertTrue(count($this->feedLink->extensionElements) == 0);
    }

    public function testSampleFeedLinkShouldHaveNoExtensionAttributes() {
        $this->feedLink->transferFromXML($this->feedLinkText);
        $this->assertTrue(is_array($this->feedLink->extensionAttributes));
        $this->assertTrue(count($this->feedLink->extensionAttributes) == 0);
    }
    
    public function testNormalFeedLinkShouldHaveNoExtensionElements() {
        $this->feedLink->href = "http://www.google.com/calendar/feeds/default/private/full";
        $this->feedLink->rel = "via";
        $this->feedLink->countHint = "5";
        $this->feedLink->readOnly = "false";
        
        $this->assertEquals("http://www.google.com/calendar/feeds/default/private/full", $this->feedLink->href);
        $this->assertEquals("via", $this->feedLink->rel);
        $this->assertEquals("5", $this->feedLink->countHint);
        $this->assertEquals("false", $this->feedLink->readOnly);
                
        $this->assertEquals(0, count($this->feedLink->extensionElements));
        $newFeedLink = new Zend_Gdata_Extension_FeedLink(); 
        $newFeedLink->transferFromXML($this->feedLink->saveXML());
        $this->assertEquals(0, count($newFeedLink->extensionElements));
        $newFeedLink->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newFeedLink->extensionElements));
        $this->assertEquals("http://www.google.com/calendar/feeds/default/private/full", $newFeedLink->href);
        $this->assertEquals("via", $newFeedLink->rel);
        $this->assertEquals("5", $newFeedLink->countHint);
        $this->assertEquals("false", $newFeedLink->readOnly);

        /* try constructing using magic factory */
        $gdata = new Zend_Gdata();
        $newFeedLink2 = $gdata->newFeedLink();
        $newFeedLink2->transferFromXML($newFeedLink->saveXML());
        $this->assertEquals(1, count($newFeedLink2->extensionElements));
        $this->assertEquals("http://www.google.com/calendar/feeds/default/private/full", $newFeedLink2->href);
        $this->assertEquals("via", $newFeedLink2->rel);
        $this->assertEquals("5", $newFeedLink2->countHint);
        $this->assertEquals("false", $newFeedLink2->readOnly);
    }

    public function testEmptyFeedLinkToAndFromStringShouldMatch() {
        $feedLinkXml = $this->feedLink->saveXML();
        $newFeedLink = new Zend_Gdata_Extension_FeedLink();
        $newFeedLink->transferFromXML($feedLinkXml);
        $newFeedLinkXml = $newFeedLink->saveXML();
        $this->assertTrue($feedLinkXml == $newFeedLinkXml);
    }

    public function testFeedLinkWithValueToAndFromStringShouldMatch() {
        $this->feedLink->href = "http://www.google.com/calendar/feeds/default/private/full";
        $this->feedLink->rel = "via";
        $this->feedLink->countHint = "5";
        $this->feedLink->readOnly = "false";
        $feedLinkXml = $this->feedLink->saveXML();
        $newFeedLink = new Zend_Gdata_Extension_FeedLink();
        $newFeedLink->transferFromXML($feedLinkXml);
        $newFeedLinkXml = $newFeedLink->saveXML();
        $this->assertTrue($feedLinkXml == $newFeedLinkXml);
        $this->assertEquals("http://www.google.com/calendar/feeds/default/private/full", $this->feedLink->href);
        $this->assertEquals("via", $this->feedLink->rel);
        $this->assertEquals("5", $this->feedLink->countHint);
        $this->assertEquals("false", $this->feedLink->readOnly);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->feedLink->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->feedLink->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->feedLink->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->feedLink->extensionAttributes['foo2']['value']);
        $feedLinkXml = $this->feedLink->saveXML();
        $newFeedLink = new Zend_Gdata_Extension_FeedLink();
        $newFeedLink->transferFromXML($feedLinkXml);
        $this->assertEquals('bar', $newFeedLink->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newFeedLink->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullFeedLinkToAndFromString() {
        $this->feedLink->transferFromXML($this->feedLinkText);
        $this->assertEquals("http://www.google.com/calendar/feeds/default/private/full/3tsi3ag1q40bnsik88k25sgpss/comments", $this->feedLink->href);
        $this->assertEquals("http://schemas.google.com/g/2005#feed", $this->feedLink->rel);
        $this->assertEquals("0", $this->feedLink->countHint);
        $this->assertEquals("true", $this->feedLink->readOnly);
		$this->assertTrue($this->feedLink->feed instanceof Zend_Gdata_App_Feed);
		$this->assertEquals("Comments for: Sample Event", $this->feedLink->feed->title->text);
    }

}
