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
use Zend\GData\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_YouTube
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_YouTube
 */
class UserProfileEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->V2entryText = file_get_contents(
                'Zend/GData/YouTube/_files/UserProfileEntryDataSampleV2.xml',
                true);
        $this->entry = new YouTube\UserProfileEntry();
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

    public function testSamplePropertiesAreCorrectV2 () {
        $this->entry->transferFromXML($this->V2entryText);
        $this->entry->setMajorProtocolVersion(2);
        $this->verifyAllSamplePropertiesAreCorrectV2($this->entry);
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
