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
use Zend\GData\Extension;
use Zend\GData\App;

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
class PlaylistListEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/GData/YouTube/_files/PlaylistListEntryDataSample1.xml',
                true);
        $this->v2entryText = file_get_contents(
                'Zend/GData/YouTube/_files/PlaylistListEntryDataSampleV2.xml',
                true);
        $this->entry = new YouTube\PlaylistListEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($playlistListEntry) {
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser/playlists/46A2F8C9B36B6FE7',
            $playlistListEntry->id->text);
        $this->assertEquals('2007-09-20T13:42:19.000-07:00', $playlistListEntry->updated->text);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/tags.cat', $playlistListEntry->category[0]->scheme);
        $this->assertEquals('music', $playlistListEntry->category[0]->term);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $playlistListEntry->category[1]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#playlistLink', $playlistListEntry->category[1]->term);
        $this->assertEquals('text', $playlistListEntry->title->type);
        $this->assertEquals('YouTube Musicians', $playlistListEntry->title->text);
        $this->assertEquals('text', $playlistListEntry->content->type);
        $this->assertEquals('Music from talented people on YouTube.', $playlistListEntry->content->text);
        $this->assertEquals('self', $playlistListEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $playlistListEntry->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser/playlists/46A2F8C9B36B6FE7', $playlistListEntry->getLink('self')->href);
        $this->assertEquals('testuser', $playlistListEntry->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser', $playlistListEntry->author[0]->uri->text);
        $this->assertEquals('Music from talented people on YouTube.', $playlistListEntry->description->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/playlists/46A2F8C9B36B6FE7', $playlistListEntry->getPlaylistVideoFeedUrl());
        $this->assertEquals('http://gdata.youtube.com/feeds/playlists/46A2F8C9B36B6FE7', $playlistListEntry->feedLink[0]->href);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#playlist', $playlistListEntry->feedLink[0]->rel);
    }

    private function verifyAllSamplePropertiesAreCorrectV2 ($playlistListEntry) {
        $this->assertEquals('tag:youtube.com,2008:user:googledevelopers:playlist:8E2186857EE27746',
            $playlistListEntry->id->text);
        $this->assertEquals('2008-12-10T09:56:03.000Z', $playlistListEntry->updated->text);
        $this->assertEquals('2007-08-23T21:48:43.000Z', $playlistListEntry->published->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $playlistListEntry->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#playlistLink', $playlistListEntry->category[0]->term);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $playlistListEntry->category[0]->scheme);
        $this->assertEquals('Non-google Interviews', $playlistListEntry->title->text);
        $this->assertEquals('This playlist contains interviews with people outside of Google.', $playlistListEntry->summary->text);

        $this->assertEquals('self', $playlistListEntry->getLink('self')->rel);
        $this->assertEquals('http://gdata.youtube.com/feeds/api/users/googledevelopers/playlists/8E2186857EE27746?v=2', $playlistListEntry->getLink('self')->href);
        $this->assertEquals('application/atom+xml', $playlistListEntry->getLink('self')->type);
        $this->assertEquals('alternate', $playlistListEntry->getLink('alternate')->rel);
        $this->assertEquals('http://www.youtube.com/view_play_list?p=8E2186857EE27746', $playlistListEntry->getLink('alternate')->href);
        $this->assertEquals('text/html', $playlistListEntry->getLink('alternate')->type);
        $this->assertEquals('related', $playlistListEntry->getLink('related')->rel);
        $this->assertEquals('http://gdata.youtube.com/feeds/api/users/googledevelopers?v=2', $playlistListEntry->getLink('related')->href);
        $this->assertEquals('application/atom+xml', $playlistListEntry->getLink('related')->type);
        $this->assertEquals('googledevelopers', $playlistListEntry->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/api/users/googledevelopers', $playlistListEntry->author[0]->uri->text);

        $this->assertEquals('8E2186857EE27746', $playlistListEntry->getPlaylistId()->text);
        $this->assertEquals('1', $playlistListEntry->getCountHint()->text);

        $this->assertEquals('application/atom+xml;type=feed', $playlistListEntry->getContent()->getType());
        $this->assertEquals('http://gdata.youtube.com/feeds/api/playlists/8E2186857EE27746?v=2', $playlistListEntry->getContent()->getSrc());
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

    public function testEmptyPlaylistListEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newPlaylistListEntry = new YouTube\PlaylistListEntry();
        $newPlaylistListEntry->transferFromXML($entryXml);
        $newPlaylistListEntryXml = $newPlaylistListEntry->saveXML();
        $this->assertTrue($entryXml == $newPlaylistListEntryXml);
    }

    public function testSampleEntryShouldHaveNoExtensionElementsV2() {
        $this->entry->transferFromXML($this->entryText);
        $this->entry->setMajorProtocolVersion(2);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertEquals(0, count($this->entry->extensionElements));
    }

    public function testSampleEntryShouldHaveNoExtensionAttributesV2() {
        $this->entry->transferFromXML($this->entryText);
        $this->entry->setMajorProtocolVersion(2);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertEquals(0, count($this->entry->extensionAttributes));
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
        $this->entry->transferFromXML($this->v2entryText);
        $this->entry->setMajorProtocolVersion(2);
        $this->verifyAllSamplePropertiesAreCorrectV2($this->entry);
    }

    public function testConvertPlaylistListEntryToAndFromStringV2() {
        $this->entry->transferFromXML($this->v2entryText);
        $entryXml = $this->entry->saveXML();
        $newPlaylistListEntry = new YouTube\PlaylistListEntry();
        $newPlaylistListEntry->transferFromXML($entryXml);
        $newPlaylistListEntry->setMajorProtocolVersion(2);
        $this->verifyAllSamplePropertiesAreCorrectV2($newPlaylistListEntry);
        $newPlaylistListEntryXml = $newPlaylistListEntry->saveXML();
        $this->assertEquals($entryXml, $newPlaylistListEntryXml);
    }

    public function testConvertPlaylistListEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newPlaylistListEntry = new YouTube\PlaylistListEntry();
        $newPlaylistListEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newPlaylistListEntry);
        $newPlaylistListEntryXml = $newPlaylistListEntry->saveXML();
        $this->assertEquals($entryXml, $newPlaylistListEntryXml);
    }

    public function testGettingCountHintOnV1EntryShouldThrowException() {
        $exceptionCaught = false;
        $this->entry->transferFromXML($this->entryText);
        try {
            $this->entry->getCountHint();
        } catch (App\VersionException $e) {
            $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught, 'Calling getCountHint on a v1 ' .
            'playlistListEntry should throw an exception');
    }

    public function testGettingPlaylistIdOnV1EntryShouldThrowException() {
        $exceptionCaught = false;
        $this->entry->transferFromXML($this->entryText);
        try {
            $this->entry->getPlaylistId();
        } catch (App\VersionException $e) {
            $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught, 'Calling getPlaylistId on a v1 ' .
            'playlistListEntry should throw an exception');
    }

    public function testGetPlaylistVideoFeedUrlWorksInV2() {
        $this->entry->transferFromXML($this->v2entryText);
        $this->entry->setMajorProtocolVersion(2);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/playlists/8E2186857EE27746?v=2',
            $this->entry->getPlaylistVideoFeedUrl());
    }
}
