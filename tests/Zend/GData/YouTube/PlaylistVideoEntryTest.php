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
class PlaylistVideoEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/GData/YouTube/_files/PlaylistVideoEntryDataSample1.xml',
                true);
        $this->V2entryText = file_get_contents(
                'Zend/GData/YouTube/_files/PlaylistVideoEntryDataSampleV2.xml',
                true);
        $this->entry = new YouTube\PlaylistVideoEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($playlistVideoEntry) {
        $this->assertEquals('http://gdata.youtube.com/feeds/playlists/46A2F8C9B36B6FE7/efb9b9a8dd4c2b21',
            $playlistVideoEntry->id->text);
        $this->assertEquals('2007-09-20T22:56:57.061Z', $playlistVideoEntry->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $playlistVideoEntry->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#playlist', $playlistVideoEntry->category[0]->term);
        $this->assertEquals('text', $playlistVideoEntry->title->type);
        $this->assertEquals('"Crazy (Gnarles Barkley)" - Acoustic Cover', $playlistVideoEntry->title->text);
        $this->assertEquals('html', $playlistVideoEntry->content->type);
        $this->assertEquals('self', $playlistVideoEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $playlistVideoEntry->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/playlists/46A2F8C9B36B6FE7/efb9b9a8dd4c2b21', $playlistVideoEntry->getLink('self')->href);
        $this->assertEquals('davidchoimusic', $playlistVideoEntry->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/davidchoimusic', $playlistVideoEntry->author[0]->uri->text);
        $mediaGroup = $playlistVideoEntry->mediaGroup;

        $this->assertEquals('plain', $mediaGroup->title->type);
        $this->assertEquals('"Crazy (Gnarles Barkley)" - Acoustic Cover', $mediaGroup->title->text);
        $this->assertEquals('plain', $mediaGroup->description->type);
        $this->assertEquals('Gnarles Barkley acoustic cover http://www.myspace.com/davidchoimusic', $mediaGroup->description->text);
        $this->assertEquals('music, singing, gnarls, barkley, acoustic, cover', $mediaGroup->keywords->text);
        $this->assertEquals(255, $mediaGroup->duration->seconds);
        $this->assertEquals('Music', $mediaGroup->category[0]->label);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/categories.cat', $mediaGroup->category[0]->scheme);
        $this->assertEquals('video', $mediaGroup->content[0]->medium);
        $this->assertEquals('http://www.youtube.com/v/UMFI1hdm96E', $mediaGroup->content[0]->url);
        $this->assertEquals('application/x-shockwave-flash', $mediaGroup->content[0]->type);
        $this->assertEquals('true', $mediaGroup->content[0]->isDefault);
        $this->assertEquals('full', $mediaGroup->content[0]->expression);
        $this->assertEquals(255, $mediaGroup->content[0]->duration);
        $this->assertEquals(5, $mediaGroup->content[0]->format);

        $this->assertEquals('http://img.youtube.com/vi/UMFI1hdm96E/2.jpg', $mediaGroup->thumbnail[0]->url);
        $this->assertEquals(97, $mediaGroup->thumbnail[0]->height);
        $this->assertEquals(130, $mediaGroup->thumbnail[0]->width);
        $this->assertEquals('00:02:07.500', $mediaGroup->thumbnail[0]->time);
        $this->assertEquals('http://img.youtube.com/vi/UMFI1hdm96E/1.jpg', $mediaGroup->thumbnail[1]->url);
        $this->assertEquals(97, $mediaGroup->thumbnail[1]->height);
        $this->assertEquals(130, $mediaGroup->thumbnail[1]->width);
        $this->assertEquals('00:01:03.750', $mediaGroup->thumbnail[1]->time);
        $this->assertEquals('http://img.youtube.com/vi/UMFI1hdm96E/3.jpg', $mediaGroup->thumbnail[2]->url);
        $this->assertEquals(97, $mediaGroup->thumbnail[2]->height);
        $this->assertEquals(130, $mediaGroup->thumbnail[2]->width);
        $this->assertEquals('00:03:11.250', $mediaGroup->thumbnail[2]->time);
        $this->assertEquals('http://img.youtube.com/vi/UMFI1hdm96E/0.jpg', $mediaGroup->thumbnail[3]->url);
        $this->assertEquals(240, $mediaGroup->thumbnail[3]->height);
        $this->assertEquals(320, $mediaGroup->thumbnail[3]->width);
        $this->assertEquals('00:02:07.500', $mediaGroup->thumbnail[3]->time);

        $this->assertEquals(113321, $playlistVideoEntry->statistics->viewCount);
        $this->assertEquals(1, $playlistVideoEntry->rating->min);
        $this->assertEquals(5, $playlistVideoEntry->rating->max);
        $this->assertEquals(1005, $playlistVideoEntry->rating->numRaters);
        $this->assertEquals(4.77, $playlistVideoEntry->rating->average);
        $this->assertEquals(1, $playlistVideoEntry->position->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/UMFI1hdm96E/comments', $playlistVideoEntry->comments->feedLink->href);
    }

      private function verifyAllSamplePropertiesAreCorrectV2 ($playlistVideoEntry) {
        $this->assertEquals(
            'tag:youtube.com,2008:playlist:4E6265CEF8BAA793:579617126485907C',
            $playlistVideoEntry->id->text);
        $this->assertEquals('2008-12-16T18:32:03.434Z',
            $playlistVideoEntry->updated->text);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007/keywords.cat',
            $playlistVideoEntry->category[0]->scheme);
        $this->assertEquals('dynamite', $playlistVideoEntry->category[0]->term);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007/categories.cat',
            $playlistVideoEntry->category[1]->scheme);
        $this->assertEquals('News', $playlistVideoEntry->category[1]->term);
        $this->assertEquals('News & Politics',
            $playlistVideoEntry->category[1]->getLabel());
        $this->assertEquals(
            'http://schemas.google.com/g/2005#kind',
            $playlistVideoEntry->category[2]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#playlist',
            $playlistVideoEntry->category[2]->term);
        $this->assertEquals('Paris Police Find Dynamite in Department Store',
            $playlistVideoEntry->title->text);

        $this->assertEquals('alternate',
            $playlistVideoEntry->getLink('alternate')->rel);
        $this->assertEquals('text/html',
            $playlistVideoEntry->getLink('alternate')->type);
        $this->assertEquals(
            'http://www.youtube.com/watch?v=Lur391T5ApY',
            $playlistVideoEntry->getLink('alternate')->href);

        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007#video.responses',
            $playlistVideoEntry->getLink(
                'http://gdata.youtube.com/schemas/2007#video.responses')->rel);
        $this->assertEquals('application/atom+xml',
            $playlistVideoEntry->getLink(
                'http://gdata.youtube.com/schemas/2007#video.responses')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/videos/Lur391T5ApY/' .
            'responses?v=2',
            $playlistVideoEntry->getLink(
                'http://gdata.youtube.com/schemas/2007#video.responses')->href);

        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007#mobile',
            $playlistVideoEntry->getLink(
                'http://gdata.youtube.com/schemas/2007#mobile')->rel);
        $this->assertEquals('text/html',
            $playlistVideoEntry->getLink(
                'http://gdata.youtube.com/schemas/2007#mobile')->type);
        $this->assertEquals(
            'http://m.youtube.com/details?v=Lur391T5ApY',
            $playlistVideoEntry->getLink(
                'http://gdata.youtube.com/schemas/2007#mobile')->href);

        $this->assertEquals('related',
            $playlistVideoEntry->getLink('related')->rel);
        $this->assertEquals('application/atom+xml',
            $playlistVideoEntry->getLink('related')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/videos/Lur391T5ApY?v=2',
            $playlistVideoEntry->getLink('related')->href);

        $this->assertEquals('self',
            $playlistVideoEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml',
            $playlistVideoEntry->getLink('self')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/playlists/4E6265CEF8BAA793/' .
            '579617126485907C?v=2',
            $playlistVideoEntry->getLink('self')->href);

        $this->assertEquals('zfgdata',
            $playlistVideoEntry->author[0]->name->text);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/zfgdata',
            $playlistVideoEntry->author[0]->uri->text);

        $mediaGroup = $playlistVideoEntry->mediaGroup;

        $this->assertEquals('plain', $mediaGroup->title->type);
        $this->assertEquals('Paris Police Find',
            $mediaGroup->title->text);
        $this->assertEquals('plain', $mediaGroup->description->type);
        $this->assertEquals('French police found.',
            $mediaGroup->description->text);
        $this->assertEquals(
            'department, dynamite, explosives, find',
            $mediaGroup->keywords->text);
        $this->assertEquals(67, $mediaGroup->duration->seconds);
        $this->assertEquals('News & Politics',
            $mediaGroup->category[0]->label);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007/categories.cat',
            $mediaGroup->category[0]->scheme);
        $this->assertEquals('video', $mediaGroup->content[0]->medium);
        $this->assertEquals(
            'http://www.youtube.com/v/Lur391T5ApY&f=gdata_playlists',
            $mediaGroup->content[0]->url);
        $this->assertEquals('application/x-shockwave-flash',
            $mediaGroup->content[0]->type);
        $this->assertEquals('video',
            $mediaGroup->content[0]->medium);
        $this->assertEquals('true', $mediaGroup->content[0]->isDefault);
        $this->assertEquals('full', $mediaGroup->content[0]->expression);
        $this->assertEquals(67, $mediaGroup->content[0]->duration);
        $this->assertEquals(5, $mediaGroup->content[0]->format);

        $this->assertEquals('http://i.ytimg.com/vi/Lur391T5ApY/2.jpg',
            $mediaGroup->thumbnail[0]->url);
        $this->assertEquals(97, $mediaGroup->thumbnail[0]->height);
        $this->assertEquals(130, $mediaGroup->thumbnail[0]->width);
        $this->assertEquals('00:00:33.500', $mediaGroup->thumbnail[0]->time);
        $this->assertEquals('http://i.ytimg.com/vi/Lur391T5ApY/1.jpg',
            $mediaGroup->thumbnail[1]->url);
        $this->assertEquals(97, $mediaGroup->thumbnail[1]->height);
        $this->assertEquals(130, $mediaGroup->thumbnail[1]->width);
        $this->assertEquals('00:00:16.750', $mediaGroup->thumbnail[1]->time);
        $this->assertEquals('http://i.ytimg.com/vi/Lur391T5ApY/3.jpg',
            $mediaGroup->thumbnail[2]->url);
        $this->assertEquals(97, $mediaGroup->thumbnail[2]->height);
        $this->assertEquals(130, $mediaGroup->thumbnail[2]->width);
        $this->assertEquals('00:00:50.250', $mediaGroup->thumbnail[2]->time);
        $this->assertEquals('http://i.ytimg.com/vi/Lur391T5ApY/hqdefault.jpg',
            $mediaGroup->thumbnail[3]->url);
        $this->assertEquals(360, $mediaGroup->thumbnail[3]->height);
        $this->assertEquals(480, $mediaGroup->thumbnail[3]->width);
        $this->assertEquals('2008-12-16T17:01:42.000Z',
            $mediaGroup->getUploaded()->text);
        $this->assertEquals('AssociatedPress',
            $mediaGroup->getMediaCredit()->text);
        $this->assertEquals('uploader',
            $mediaGroup->getMediaCredit()->role);
        $this->assertEquals('urn:youtube',
            $mediaGroup->getMediaCredit()->scheme);
        $this->assertEquals('partner',
            $mediaGroup->getMediaCredit()->getYTtype());
        $players = $mediaGroup->getPlayer();
        $this->assertEquals('http://www.youtube.com/watch?v=Lur391T5ApY',
            $players[0]->url);

        $this->assertEquals(271, $playlistVideoEntry->statistics->viewCount);
        $this->assertEquals(1, $playlistVideoEntry->rating->min);
        $this->assertEquals(5, $playlistVideoEntry->rating->max);
        $this->assertEquals(5, $playlistVideoEntry->rating->numRaters);
        $this->assertEquals(4.20, $playlistVideoEntry->rating->average);
        $this->assertEquals(1, $playlistVideoEntry->position->text);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/videos/Lur391T5ApY/comments?v=2',
            $playlistVideoEntry->comments->feedLink->href);
        $this->assertEquals(4,
            $playlistVideoEntry->comments->feedLink->countHint);
        $this->assertEquals('New York, NY',
            $playlistVideoEntry->getLocation()->text);
        $this->assertEquals('2008-12-16',
            $playlistVideoEntry->getRecorded()->text);
        $this->assertEquals('Lur391T5ApY',
            $playlistVideoEntry->getVideoId());

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
        $this->entry->setMajorProtocolVersion(2);
        $this->entry->transferFromXML($this->V2entryText);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionAttributesV2() {
        $this->entry->setMajorProtocolVersion(2);
        $this->entry->transferFromXML($this->V2entryText);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testEmptyPlaylistVideoEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newPlaylistVideoEntry = new YouTube\PlaylistVideoEntry();
        $newPlaylistVideoEntry->transferFromXML($entryXml);
        $newPlaylistVideoEntryXml = $newPlaylistVideoEntry->saveXML();
        $this->assertTrue($entryXml == $newPlaylistVideoEntryXml);
    }

    public function testEmptyPlaylistVideoEntryToAndFromStringShouldMatchV2() {
        $this->entry->setMajorProtocolVersion(2);
        $entryXml = $this->entry->saveXML();
        $newPlaylistVideoEntry = new YouTube\PlaylistVideoEntry();
        $newPlaylistVideoEntry->setMajorProtocolVersion(2);
        $newPlaylistVideoEntry->transferFromXML($entryXml);
        $newPlaylistVideoEntryXml = $newPlaylistVideoEntry->saveXML();
        $this->assertTrue($entryXml == $newPlaylistVideoEntryXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->entry->transferFromXML($this->entryText);
        $this->verifyAllSamplePropertiesAreCorrect($this->entry);
    }

    public function testSamplePropertiesAreCorrectV2 () {
        $this->entry->setMajorProtocolVersion(2);
        $this->entry->transferFromXML($this->V2entryText);
        $this->verifyAllSamplePropertiesAreCorrectV2($this->entry);
    }

    public function testConvertPlaylistVideoEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newPlaylistVideoEntry = new YouTube\PlaylistVideoEntry();
        $newPlaylistVideoEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newPlaylistVideoEntry);
        $newPlaylistVideoEntryXml = $newPlaylistVideoEntry->saveXML();
        $this->assertEquals($entryXml, $newPlaylistVideoEntryXml);
    }

    public function testConvertPlaylistVideoEntryToAndFromStringV2() {
        $this->entry->setMajorProtocolVersion(2);
        $this->entry->transferFromXML($this->V2entryText);
        $entryXml = $this->entry->saveXML();
        $newPlaylistVideoEntry = new YouTube\PlaylistVideoEntry();
        $newPlaylistVideoEntry->setMajorProtocolVersion(2);
        $newPlaylistVideoEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrectV2($newPlaylistVideoEntry);
        $newPlaylistVideoEntryXml = $newPlaylistVideoEntry->saveXML();
        $this->assertEquals($entryXml, $newPlaylistVideoEntryXml);
    }

}
