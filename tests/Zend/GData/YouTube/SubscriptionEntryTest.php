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
use Zend\GData\Extension;

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
class SubscriptionEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/GData/YouTube/_files/SubscriptionEntryDataSample1.xml',
                true);
        $this->v2entryText_channel = file_get_contents(
                'Zend/GData/YouTube/_files/' .
                'SubscriptionEntryDataSample_channelV2.xml',
                true);
        $this->v2entryText_playlist = file_get_contents(
                'Zend/GData/YouTube/_files/' .
                'SubscriptionEntryDataSample_playlistV2.xml',
                true);
        $this->v2entryText_favorites = file_get_contents(
                'Zend/GData/YouTube/_files/' .
                'SubscriptionEntryDataSample_favoritesV2.xml',
                true);
        $this->v2entryText_query = file_get_contents(
                'Zend/GData/YouTube/_files/' .
                'SubscriptionEntryDataSample_queryV2.xml',
                true);

        $this->entry = new YouTube\SubscriptionEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($subscriptionListEntry) {
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/users/testuser/' .
            'subscriptions/35bbde297dba88db',
            $subscriptionListEntry->id->text);
        $this->assertEquals('2007-03-02T11:58:22.000-08:00',
            $subscriptionListEntry->updated->text);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007/subscriptiontypes.cat',
            $subscriptionListEntry->category[1]->scheme);
        $this->assertEquals('publisher',
            $subscriptionListEntry->category[1]->term);
        $this->assertEquals('http://schemas.google.com/g/2005#kind',
            $subscriptionListEntry->category[0]->scheme);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007#subscription',
            $subscriptionListEntry->category[0]->term);
        $this->assertEquals('text', $subscriptionListEntry->title->type);
        $this->assertEquals('Videos published by : BBC',
            $subscriptionListEntry->title->text);
        $this->assertEquals('self',
            $subscriptionListEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml',
            $subscriptionListEntry->getLink('self')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/users/testuser/' .
            'subscriptions/35bbde297dba88db',
            $subscriptionListEntry->getLink('self')->href);
        $this->assertEquals('testuser',
            $subscriptionListEntry->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser',
            $subscriptionListEntry->author[0]->uri->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/BBC/uploads',
            $subscriptionListEntry->feedLink[0]->href);
        $this->assertEquals('697',
            $subscriptionListEntry->feedLink[0]->countHint);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007#user.uploads',
            $subscriptionListEntry->feedLink[0]->rel);
    }

    public function verifyAllSamplePropertiesAreCorrectV2(
        $subscriptionListEntry) {
        $this->assertEquals(
            'tag:youtube.com,2008:user:googledevelopers:subscription:' .
            'Z1Lm-S9gkRQ',
            $subscriptionListEntry->id->text);
        $this->assertEquals('2007-11-16T15:15:17.000-08:00',
            $subscriptionListEntry->published->text);
        $this->assertEquals('2007-11-16T15:15:17.000-08:00',
            $subscriptionListEntry->updated->text);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007/subscriptiontypes.cat',
            $subscriptionListEntry->category[0]->scheme);
        $this->assertEquals('channel',
            $subscriptionListEntry->category[0]->term);
        $this->assertEquals('http://schemas.google.com/g/2005#kind',
            $subscriptionListEntry->category[1]->scheme);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007#subscription',
            $subscriptionListEntry->category[1]->term);

        $this->assertEquals('self',
            $subscriptionListEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml',
            $subscriptionListEntry->getLink('self')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/googledevelopers/' .
            'subscriptions/Z1Lm-S9gkRQ?v=2',
            $subscriptionListEntry->getLink('self')->href);
        $this->assertEquals('related',
            $subscriptionListEntry->getLink('related')->rel);
        $this->assertEquals('application/atom+xml',
            $subscriptionListEntry->getLink('related')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/googledevelopers?v=2',
            $subscriptionListEntry->getLink('related')->href);
        $this->assertEquals('alternate',
            $subscriptionListEntry->getLink('alternate')->rel);
        $this->assertEquals('text/html',
            $subscriptionListEntry->getLink('alternate')->type);
        $this->assertEquals(
            'http://www.youtube.com/profile_videos?user=androiddevelopers',
            $subscriptionListEntry->getLink('alternate')->href);
        $this->assertEquals('GoogleDevelopers',
            $subscriptionListEntry->author[0]->name->text);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/googledevelopers',
            $subscriptionListEntry->author[0]->uri->text);
        $this->assertEquals('androiddevelopers',
            $subscriptionListEntry->getUsername()->text);
        $this->assertEquals('50',
            $subscriptionListEntry->getCountHint()->text);
        $thumbnail = $subscriptionListEntry->getMediaThumbnail();
        $this->assertTrue(
            $thumbnail instanceof \Zend\GData\Media\Extension\MediaThumbnail);
        $this->assertTrue($thumbnail->getUrl() != null);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/' .
            'androiddevelopers/uploads?v=2',
            $subscriptionListEntry->getContent()->getSrc());
    }

    public function testEmptyEntryShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionElements() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionAttributes() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionElementsV2() {
        $this->entry->transferFromXML($this->v2entryText_channel);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionAttributesV2() {
        $this->entry->transferFromXML($this->v2entryText_channel);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testEmptySubscriptionEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newSubscriptionEntry = new YouTube\SubscriptionEntry();
        $newSubscriptionEntry->transferFromXML($entryXml);
        $newSubscriptionEntryXml = $newSubscriptionEntry->saveXML();
        $this->assertTrue($entryXml == $newSubscriptionEntryXml);
    }

    public function testEmptySubscriptionEntryToAndFromStringShouldMatchV2() {
        $this->entry->transferFromXML($this->v2entryText_channel);
        $entryXml = $this->entry->saveXML();
        $newSubscriptionEntry = new YouTube\SubscriptionEntry();
        $newSubscriptionEntry->transferFromXML($entryXml);
        $newSubscriptionEntry->setMajorProtocolVersion(2);
        $newSubscriptionEntryXml = $newSubscriptionEntry->saveXML();
        $this->assertTrue($entryXml == $newSubscriptionEntryXml);
    }

    public function testGetFeedLinkReturnsAllStoredEntriesWhenUsedWithNoParameters() {
        // Prepare test data
        $entry1 = new Extension\FeedLink();
        $entry1->rel = "first";
        $entry1->href= "foo";
        $entry2 = new Extension\FeedLink();
        $entry2->rel = "second";
        $entry2->href= "bar";
        $data = array($entry1, $entry2);

        // Load test data and run test
        $this->entry->feedLink = $data;
        $this->assertEquals(2, count($this->entry->feedLink));
    }

    public function testGetFeedLinkCanReturnEntriesByRelValue() {
        // Prepare test data
        $entry1 = new Extension\FeedLink();
        $entry1->rel = "first";
        $entry1->href= "foo";
        $entry2 = new Extension\FeedLink();
        $entry2->rel = "second";
        $entry2->href= "bar";
        $data = array($entry1, $entry2);

        // Load test data and run test
        $this->entry->feedLink = $data;
        $this->assertEquals($entry1, $this->entry->getFeedLink('first'));
        $this->assertEquals($entry2, $this->entry->getFeedLink('second'));
    }

    public function testSamplePropertiesAreCorrect () {
        $this->entry->transferFromXML($this->entryText);
        $this->verifyAllSamplePropertiesAreCorrect($this->entry);
    }

    public function testSamplePropertiesAreCorrectV2 () {
        $this->entry->transferFromXML($this->v2entryText_channel);
        $this->entry->setMajorProtocolVersion(2);
        $this->verifyAllSamplePropertiesAreCorrectV2($this->entry);
    }

    public function testConvertSubscriptionEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newSubscriptionEntry = new YouTube\SubscriptionEntry();
        $newSubscriptionEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newSubscriptionEntry);
        $newSubscriptionEntryXml = $newSubscriptionEntry->saveXML();
        $this->assertEquals($entryXml, $newSubscriptionEntryXml);
    }

    public function testExceptionThrownInChannelSubscription() {
        $this->entry->transferFromXML($this->entryText);
        $exceptionCaught = false;
        try {
            $this->entry->getCountHint();
        } catch (\Zend\GData\App\VersionException $e) {
            $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught, 'Expected a VersionException on ' .
            'calling getCountHint() on a v1 subscription entry.');
    }

    public function testChannelSubscriptionFunctionalityV2() {
        $this->entry->transferFromXML($this->v2entryText_channel);
        $this->assertEquals('androiddevelopers',
            $this->entry->getUsername()->text);
        $categories = $this->entry->getCategory();
        foreach($categories as $category) {
            if ($category->getScheme() ==
                'http://gdata.youtube.com/schemas/2007/subscriptiontypes.cat') {
                    $this->assertEquals('channel', $category->getTerm());
            }
        }
    }

    public function testPlaylistSubscriptionFunctionalityV2() {
        $this->entry->transferFromXML($this->v2entryText_playlist);
        $this->entry->setMajorProtocolVersion(2);
        $this->assertEquals('From Google Engineers',
            $this->entry->getPlaylistTitle()->text);
        $this->assertEquals('4AE5C0D23C2EB82D',
            $this->entry->getPlaylistId()->text);
        $categories = $this->entry->getCategory();
        foreach($categories as $category) {
            if ($category->getScheme() ==
                'http://gdata.youtube.com/schemas/2007/subscriptiontypes.cat') {
                    $this->assertEquals('playlist', $category->getTerm());
            }
        }
    }

    public function testFavoritesSubscriptionFunctionalityV2() {
        $this->entry->transferFromXML($this->v2entryText_favorites);
        $categories = $this->entry->getCategory();
        foreach($categories as $category) {
            if ($category->getScheme() ==
                'http://gdata.youtube.com/schemas/2007/subscriptiontypes.cat') {
                    $this->assertEquals('favorites', $category->getTerm());
            }
        }
    }

    public function testQuerySubscriptionFunctionalityV2() {
        $this->entry->transferFromXML($this->v2entryText_query);
        $categories = $this->entry->getCategory();
        foreach($categories as $category) {
            if ($category->getScheme() ==
                'http://gdata.youtube.com/schemas/2007/subscriptiontypes.cat') {
                    $this->assertEquals('query', $category->getTerm());
            }
        }
    }

}
