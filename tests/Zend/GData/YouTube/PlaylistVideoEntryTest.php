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
class PlaylistVideoEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->V2entryText = file_get_contents(
                'Zend/GData/YouTube/_files/PlaylistVideoEntryDataSampleV2.xml',
                true);
        $this->entry = new YouTube\PlaylistVideoEntry();
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

    public function testSamplePropertiesAreCorrectV2 () {
        $this->entry->setMajorProtocolVersion(2);
        $this->entry->transferFromXML($this->V2entryText);
        $this->verifyAllSamplePropertiesAreCorrectV2($this->entry);
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
