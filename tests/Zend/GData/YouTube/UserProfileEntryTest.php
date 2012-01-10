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
class UserProfileEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/GData/YouTube/_files/UserProfileEntryDataSample1.xml',
                true);
        $this->V2entryText = file_get_contents(
                'Zend/GData/YouTube/_files/UserProfileEntryDataSampleV2.xml',
                true);
        $this->entry = new YouTube\UserProfileEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($userProfileEntry) {
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy',
            $userProfileEntry->id->text);
        $this->assertEquals('2007-08-13T12:37:03.000-07:00', $userProfileEntry->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $userProfileEntry->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#userProfile', $userProfileEntry->category[0]->term);
        $this->assertEquals('text', $userProfileEntry->title->type);
        $this->assertEquals('Darcy', $userProfileEntry->title->text);
        $this->assertEquals('self', $userProfileEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $userProfileEntry->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy', $userProfileEntry->getLink('self')->href);
        $this->assertEquals('Fitzwilliam Darcy', $userProfileEntry->author[0]->name->text);
        $this->assertEquals(32, $userProfileEntry->age->text);
        $this->assertEquals('darcy', $userProfileEntry->username->text);
        $this->assertEquals('A person of great interest', $userProfileEntry->description->text);
        $this->assertEquals('Pride and Prejudice', $userProfileEntry->books->text);
        $this->assertEquals('Self employed', $userProfileEntry->company->text);
        $this->assertEquals('Reading, arguing with Liz', $userProfileEntry->hobbies->text);
        $this->assertEquals('Steventon', $userProfileEntry->hometown->text);
        $this->assertEquals('Longbourn in Hertfordshire, Pemberley in Derbyshire', $userProfileEntry->location->text);
        $this->assertEquals('Pride and Prejudice, 2005', $userProfileEntry->movies->text);
        $this->assertEquals('Air Con Varizzioni, The Pleasure of the Town', $userProfileEntry->music->text);
        $this->assertEquals('Gentleman', $userProfileEntry->occupation->text);
        $this->assertEquals('Home schooling', $userProfileEntry->school->text);
        $this->assertEquals('m', $userProfileEntry->gender->text);
        $this->assertEquals('taken', $userProfileEntry->relationship->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy', $userProfileEntry->author[0]->uri->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy/favorites', $userProfileEntry->feedLink[0]->href);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#user.favorites', $userProfileEntry->feedLink[0]->rel);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy/contacts', $userProfileEntry->feedLink[1]->href);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#user.contacts', $userProfileEntry->feedLink[1]->rel);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy/inbox', $userProfileEntry->feedLink[2]->href);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#user.inbox', $userProfileEntry->feedLink[2]->rel);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy/playlists', $userProfileEntry->feedLink[3]->href);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#user.playlists', $userProfileEntry->feedLink[3]->rel);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy/subscriptions', $userProfileEntry->feedLink[4]->href);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#user.subscriptions', $userProfileEntry->feedLink[4]->rel);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy/uploads', $userProfileEntry->feedLink[5]->href);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#user.uploads', $userProfileEntry->feedLink[5]->rel);
    }

    private function verifyAllSamplePropertiesAreCorrectV2 ($userProfileEntry) {
        $this->assertEquals('tag:youtube.com,2008:user:zfgdata',
            $userProfileEntry->id->text);
        $this->assertEquals('2008-12-15T13:30:56.000-08:00',
            $userProfileEntry->updated->text);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007/channeltypes.cat',
            $userProfileEntry->category[0]->scheme);
        $this->assertEquals(
            'Standard',
            $userProfileEntry->category[0]->term);
        $this->assertEquals('zfgdata Channel',
            $userProfileEntry->title->text);
        $this->assertEquals('self', $userProfileEntry->getLink('self')->rel);
        $this->assertEquals("I'm a lonely test account, with little to do " .
            "but sit\naround and wait for people to use me. I get bored in " .
            "between\nreleases and often sleep to pass the time. Please use " .
            "me more\noften, as I love to show off my talent in breaking " .
            "your\ncode.", $userProfileEntry->getAboutMe()->text);
        $this->assertEquals('88',
            $userProfileEntry->getStatistics()->getViewCount());
        $thumbnail = $userProfileEntry->getThumbnail();
        $this->assertTrue(
            $thumbnail instanceof \Zend\GData\Media\Extension\MediaThumbnail);
        $this->assertTrue($thumbnail->getUrl() != null);
        $this->assertEquals('TestAccount',
            $userProfileEntry->getLastName()->text);
        $this->assertEquals('Lonely',
            $userProfileEntry->getFirstName()->text);
    }

    public function testEmptyEntryShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertEquals(0, count($this->entry->extensionElements));
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertEquals(0, count($this->entry->extensionAttributes));
    }

    public function testSampleEntryShouldHaveNoExtensionElements() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertEquals(0, count($this->entry->extensionElements));
    }

    public function testSampleEntryShouldHaveNoExtensionAttributes() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertEquals(0, count($this->entry->extensionAttributes));
    }

    public function testSampleEntryShouldHaveNoExtensionElementsV2() {
        $this->entry->transferFromXML($this->V2entryText);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertEquals(0, count($this->entry->extensionElements));
    }

    public function testSampleEntryShouldHaveNoExtensionAttributesV2() {
        $this->entry->transferFromXML($this->V2entryText);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertEquals(0, count($this->entry->extensionAttributes));
    }

    public function testEmptyUserProfileEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newUserProfileEntry = new YouTube\UserProfileEntry();
        $newUserProfileEntry->transferFromXML($entryXml);
        $newUserProfileEntryXml = $newUserProfileEntry->saveXML();
        $this->assertTrue($entryXml == $newUserProfileEntryXml);
    }

    public function testEmptyUserProfileEntryToAndFromStringShouldMatchV2() {
        $this->entry->setMajorProtocolVersion(2);
        $entryXml = $this->entry->saveXML();
        $newUserProfileEntry = new YouTube\UserProfileEntry();
        $newUserProfileEntry->setMajorProtocolVersion(2);
        $newUserProfileEntry->transferFromXML($entryXml);
        $newUserProfileEntryXml = $newUserProfileEntry->saveXML();
        $this->assertTrue($entryXml == $newUserProfileEntryXml);
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
        $this->entry->transferFromXML($this->V2entryText);
        $this->entry->setMajorProtocolVersion(2);
        $this->verifyAllSamplePropertiesAreCorrectV2($this->entry);
    }

    public function testConvertUserProfileEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newUserProfileEntry = new YouTube\UserProfileEntry();
        $newUserProfileEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newUserProfileEntry);
        $newUserProfileEntryXml = $newUserProfileEntry->saveXML();
        $this->assertEquals($entryXml, $newUserProfileEntryXml);
    }

    public function testConvertUserProfileEntryToAndFromStringV2() {
        $this->entry->transferFromXML($this->V2entryText);
        $entryXml = $this->entry->saveXML();
        $newUserProfileEntry = new YouTube\UserProfileEntry();
        $newUserProfileEntry->setMajorProtocolVersion(2);
        $newUserProfileEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrectV2($newUserProfileEntry);
        $newUserProfileEntryXml = $newUserProfileEntry->saveXML();
        $this->assertEquals($entryXml, $newUserProfileEntryXml);
    }

    public function testYTStatisticsInUserProfileEntryV2() {
        $this->entry->transferFromXML($this->V2entryText);
        $this->entry->setMajorProtocolVersion(2);
        $statistics = $this->entry->getStatistics();
        $this->assertEquals(14, $statistics->getVideoWatchCount());
        $this->assertEquals(88, $statistics->getViewCount());
        $this->assertEquals(12, $statistics->getSubscriberCount());
        $this->assertEquals('2008-12-15T14:56:57.000-08:00',
            $statistics->getLastWebAccess());

        // test __toString()
        $this->assertEquals('View Count=88 VideoWatchCount=14 ' .
            'SubscriberCount=12 LastWebAccess=2008-12-15T14:56:57.000-08:00 ' .
            'FavoriteCount=',
            sprintf("%s", $statistics));

    }

}
