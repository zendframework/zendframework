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
class SubscriptionFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->feedText = file_get_contents(
                'Zend/GData/YouTube/_files/SubscriptionFeedDataSample1.xml',
                true);
        $this->V2feedText = file_get_contents(
                'Zend/GData/YouTube/_files/SubscriptionFeedDataSampleV2.xml',
                true);
        $this->feed = new YouTube\SubscriptionFeed();
    }

    private function verifyAllSamplePropertiesAreCorrect ($subscriptionFeed) {
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser/subscriptions',
            $subscriptionFeed->id->text);
        $this->assertEquals('2007-09-20T22:12:45.193Z', $subscriptionFeed->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $subscriptionFeed->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#subscription', $subscriptionFeed->category[0]->term);
        $this->assertEquals('http://www.youtube.com/img/pic_youtubelogo_123x63.gif', $subscriptionFeed->logo->text);
        $this->assertEquals('text', $subscriptionFeed->title->type);
        $this->assertEquals('testuser\'s Subscriptions', $subscriptionFeed->title->text);
        $this->assertEquals('self', $subscriptionFeed->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $subscriptionFeed->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser/subscriptions?start-index=1&max-results=25', $subscriptionFeed->getLink('self')->href);
        $this->assertEquals('testuser', $subscriptionFeed->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser', $subscriptionFeed->author[0]->uri->text);
        $this->assertEquals(3, $subscriptionFeed->totalResults->text);
    }

    private function verifyAllSamplePropertiesAreCorrectV2 ($subscriptionFeed) {
        $this->assertEquals('tag:youtube.com,2008:user:zfgdata:subscriptions',
            $subscriptionFeed->id->text);
        $this->assertEquals('2007-09-20T21:01:13.000-07:00',
            $subscriptionFeed->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind',
            $subscriptionFeed->category[0]->scheme);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007#subscription',
            $subscriptionFeed->category[0]->term);
        $this->assertEquals(
            'http://www.youtube.com/img/pic_youtubelogo_123x63.gif',
            $subscriptionFeed->logo->text);
        $this->assertEquals('Subscriptions of zfgdata',
            $subscriptionFeed->title->text);
        $this->assertEquals('zfgdata',
            $subscriptionFeed->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/api/users/zfgdata',
            $subscriptionFeed->author[0]->uri->text);
        // fail because of opensearch issue TODO jhartman -> fix once trevor commits his fix
        //$this->assertEquals(3, $subscriptionFeed->totalResults->text);

        $this->assertEquals('self', $subscriptionFeed->getLink('self')->rel);
        $this->assertEquals('application/atom+xml',
            $subscriptionFeed->getLink('self')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/zfgdata/subscriptions' .
            '?start-index=1&max-results=25&v=2',
            $subscriptionFeed->getLink('self')->href);
        $this->assertEquals('related', $subscriptionFeed->getLink('related')->rel);
        $this->assertEquals('application/atom+xml',
            $subscriptionFeed->getLink('related')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/zfgdata?v=2',
            $subscriptionFeed->getLink('related')->href);
        $this->assertEquals('alternate', $subscriptionFeed->getLink('alternate')->rel);
        $this->assertEquals('text/html',
            $subscriptionFeed->getLink('alternate')->type);
        $this->assertEquals(
            'http://www.youtube.com/profile_subscriptions?user=zfgdata',
            $subscriptionFeed->getLink('alternate')->href);
        $this->assertEquals('service', $subscriptionFeed->getLink('service')->rel);
        $this->assertEquals('application/atomsvc+xml',
            $subscriptionFeed->getLink('service')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/zfgdata/subscriptions?' .
            'alt=atom-service&v=2',
            $subscriptionFeed->getLink('service')->href);

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

    public function testEmptySubscriptionFeedToAndFromStringShouldMatch() {
        $feedXml = $this->feed->saveXML();
        $newSubscriptionFeed = new YouTube\SubscriptionFeed();
        $newSubscriptionFeed->transferFromXML($feedXml);
        $newSubscriptionFeedXml = $newSubscriptionFeed->saveXML();
        $this->assertTrue($feedXml == $newSubscriptionFeedXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->feed->transferFromXML($this->feedText);
        $this->verifyAllSamplePropertiesAreCorrect($this->feed);
    }

    public function testSamplePropertiesAreCorrectV2 () {
        $this->feed->transferFromXML($this->V2feedText);
        $this->verifyAllSamplePropertiesAreCorrectV2($this->feed);
    }

    public function testConvertSubscriptionFeedToAndFromString() {
        $this->feed->transferFromXML($this->feedText);
        $feedXml = $this->feed->saveXML();
        $newSubscriptionFeed = new YouTube\SubscriptionFeed();
        $newSubscriptionFeed->transferFromXML($feedXml);
        $this->verifyAllSamplePropertiesAreCorrect($newSubscriptionFeed);
        $newSubscriptionFeedXml = $newSubscriptionFeed->saveXML();
        $this->assertEquals($feedXml, $newSubscriptionFeedXml);
    }

    public function testConvertSubscriptionFeedToAndFromStringV2() {
        $this->feed->transferFromXML($this->V2feedText);
        $this->feed->setMajorProtocolVersion(2);
        $feedXml = $this->feed->saveXML();
        $newSubscriptionFeed = new YouTube\SubscriptionFeed();
        $newSubscriptionFeed->transferFromXML($feedXml);
        $newSubscriptionFeed->setMajorProtocolVersion(2);
        $this->verifyAllSamplePropertiesAreCorrectV2($newSubscriptionFeed);
        $newSubscriptionFeedXml = $newSubscriptionFeed->saveXML();
        $this->assertEquals($feedXml, $newSubscriptionFeedXml);
    }

}
