<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\YouTube;

use Zend\GData\YouTube;

/**
 * @category   Zend
 * @package    Zend_GData_YouTube
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_YouTube
 */
class SubscriptionFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->V2feedText = file_get_contents(
                'Zend/GData/YouTube/_files/SubscriptionFeedDataSampleV2.xml',
                true);
        $this->feed = new YouTube\SubscriptionFeed();
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

    public function testEmptySubscriptionFeedToAndFromStringShouldMatch() {
        $feedXml = $this->feed->saveXML();
        $newSubscriptionFeed = new YouTube\SubscriptionFeed();
        $newSubscriptionFeed->transferFromXML($feedXml);
        $newSubscriptionFeedXml = $newSubscriptionFeed->saveXML();
        $this->assertTrue($feedXml == $newSubscriptionFeedXml);
    }

    public function testSamplePropertiesAreCorrectV2 () {
        $this->feed->transferFromXML($this->V2feedText);
        $this->verifyAllSamplePropertiesAreCorrectV2($this->feed);
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
