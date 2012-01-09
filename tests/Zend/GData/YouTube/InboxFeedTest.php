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
 * @package    Zend_GData_YouTube
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\YouTube;
use Zend\GData\YouTube;

/**
 * Test helper
 */


/**
 * @category   Zend
 * @package    Zend_GData_YouTube
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_YouTube
 */
class InboxFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->feedText = file_get_contents(
            'Zend/GData/YouTube/_files/InboxFeedDataSample1.xml',
            true);
        $this->V2feedText = file_get_contents(
            'Zend/GData/YouTube/_files/InboxFeedDataSampleV2.xml',
            true);
        $this->feed = new YouTube\InboxFeed();
    }

    private function verifyAllSamplePropertiesAreCorrect ($inboxFeed) {
        $this->assertEquals('http://gdata.youtube.com/feeds/api/users/' .
            'default/inbox',
            $inboxFeed->id->text);
        $this->assertEquals('2008-06-10T20:55:40.271Z',
            $inboxFeed->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind',
            $inboxFeed->category[0]->scheme);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007#videoMessage',
            $inboxFeed->category[0]->term);
        $this->assertEquals(
            'http://www.youtube.com/img/pic_youtubelogo_123x63.gif',
            $inboxFeed->logo->text);
        $this->assertEquals('text', $inboxFeed->title->type);
        $this->assertEquals('Inbox of andyland74',
            $inboxFeed->title->text);
        $this->assertEquals('self', $inboxFeed->getLink('self')->rel);
        $this->assertEquals('application/atom+xml',
            $inboxFeed->getLink('self')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/andyland74/inbox?...',
            $inboxFeed->getLink('self')->href);
        $this->assertEquals('andyland74', $inboxFeed->author[0]->name->text);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/andyland74',
            $inboxFeed->author[0]->uri->text);
        $this->assertEquals(1, $inboxFeed->totalResults->text);
    }

    private function verifyAllSamplePropertiesAreCorrectV2 ($inboxFeed) {
        $this->assertEquals('tag:youtube,2008:user:andyland74:inbox',
            $inboxFeed->id->text);
        $this->assertEquals('2008-07-21T17:54:30.236Z',
            $inboxFeed->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind',
            $inboxFeed->category[0]->scheme);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007#videoMessage',
            $inboxFeed->category[0]->term);
        $this->assertEquals(
            'http://www.youtube.com/img/pic_youtubelogo_123x63.gif',
            $inboxFeed->logo->text);
        $this->assertEquals('Inbox of andyland74',
            $inboxFeed->title->text);
        $this->assertEquals('andyland74',
            $inboxFeed->author[0]->name->text);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/andyland74',
            $inboxFeed->author[0]->uri->text);
        $this->assertEquals('self', $inboxFeed->getLink('self')->rel);
        $this->assertEquals('application/atom+xml',
            $inboxFeed->getLink('self')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/andyland74/inbox?...',
            $inboxFeed->getLink('self')->href);
        $this->assertEquals('alternate', $inboxFeed->getLink('alternate')->rel);
        $this->assertEquals('text/html',
            $inboxFeed->getLink('alternate')->type);
        $this->assertEquals(
            'http://www.youtube.com/my_messages?folder=inbox&filter=videos',
            $inboxFeed->getLink('alternate')->href);
        $this->assertEquals('service', $inboxFeed->getLink('service')->rel);
        $this->assertEquals('application/atomsvc+xml',
            $inboxFeed->getLink('service')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/andyland74/inbox?' .
            'alt=...',
            $inboxFeed->getLink('service')->href);

    }

    public function testEmptyEntryShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertEquals(0, count($this->feed->extensionElements));
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->feed->extensionAttributes));
        $this->assertEquals(0, count($this->feed->extensionAttributes));
    }

    public function testSampleEntryShouldHaveNoExtensionElements() {
        $this->feed->transferFromXML($this->feedText);
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertEquals(0, count($this->feed->extensionElements));
    }

    public function testSampleEntryShouldHaveNoExtensionAttributes() {
        $this->feed->transferFromXML($this->feedText);
        $this->assertTrue(is_array($this->feed->extensionAttributes));
        $this->assertEquals(0, count($this->feed->extensionAttributes));
    }

    public function testEmptyInboxFeedToAndFromStringShouldMatch() {
        $feedXml = $this->feed->saveXML();
        $newInboxFeed = new YouTube\InboxFeed();
        $newInboxFeed->transferFromXML($feedXml);
        $newInboxFeedXml = $newInboxFeed->saveXML();
        $this->assertTrue($feedXml == $newInboxFeedXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->feed->transferFromXML($this->feedText);
        $this->verifyAllSamplePropertiesAreCorrect($this->feed);
    }

    public function testSamplePropertiesAreCorrectV2 () {
        $this->feed->transferFromXML($this->V2feedText);
        $this->verifyAllSamplePropertiesAreCorrectV2($this->feed);
    }

    public function testConvertInboxFeedToAndFromString() {
        $this->feed->transferFromXML($this->feedText);
        $feedXml = $this->feed->saveXML();
        $newInboxFeed = new YouTube\InboxFeed();
        $newInboxFeed->transferFromXML($feedXml);
        $this->verifyAllSamplePropertiesAreCorrect($newInboxFeed);
        $newInboxFeedXml = $newInboxFeed->saveXML();
        $this->assertEquals($feedXml, $newInboxFeedXml);
    }

    public function testConvertInboxFeedToAndFromStringV2() {
        $this->feed->setMajorProtocolVersion(2);
        $this->feed->transferFromXML($this->V2feedText);
        $feedXml = $this->feed->saveXML();
        $newInboxFeed = new YouTube\InboxFeed();
        $newInboxFeed->transferFromXML($feedXml);
        $newInboxFeed->setMajorProtocolVersion(2);
        $this->verifyAllSamplePropertiesAreCorrectV2($newInboxFeed);
        $newInboxFeedXml = $newInboxFeed->saveXML();
        $this->assertEquals($feedXml, $newInboxFeedXml);
    }

}
