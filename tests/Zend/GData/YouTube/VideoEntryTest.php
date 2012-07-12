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
use Zend\GData\App\Extension;
use Zend\GData\App;

/**
 * @category   Zend
 * @package    Zend_GData_YouTube
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_YouTube
 */
class VideoEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->privateEntryText = file_get_contents(
                'Zend/GData/YouTube/_files/VideoEntryDataSamplePrivate.xml',
                true);
        $this->v2EntryText = file_get_contents(
                'Zend/GData/YouTube/_files/VideoEntryDataSampleV2.xml',
                true);
        $this->entry = new YouTube\VideoEntry();
    }

    private function createRandomString()
    {
        $randomString = '';
        for ($x = 0; $x < 10; $x++) {
            $randomCharacter = chr(rand(97,122));
            $randomString .= $randomCharacter;
        }
        return $randomString;
    }

    private function verifyAllPrivateSamplePropertiesAreCorrect ($videoEntry)
    {
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/UMFI1hdm96E',
            $videoEntry->id->text);
        $this->assertEquals('UMFI1hdm96E', $videoEntry->getVideoId());
        $this->assertEquals('2007-01-07T01:50:15.000Z', $videoEntry->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $videoEntry->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#video', $videoEntry->category[0]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[1]->scheme);
        $this->assertEquals('barkley', $videoEntry->category[1]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[2]->scheme);
        $this->assertEquals('singing', $videoEntry->category[2]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[3]->scheme);
        $this->assertEquals('acoustic', $videoEntry->category[3]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[4]->scheme);
        $this->assertEquals('cover', $videoEntry->category[4]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/categories.cat', $videoEntry->category[5]->scheme);
        $this->assertEquals('Music', $videoEntry->category[5]->term);
        $this->assertEquals('Music', $videoEntry->category[5]->label);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[6]->scheme);
        $this->assertEquals('gnarls', $videoEntry->category[6]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[7]->scheme);
        $this->assertEquals('music', $videoEntry->category[7]->term);

        $this->assertEquals('text', $videoEntry->title->type);
        $this->assertEquals('"Crazy (Gnarles Barkley)" - Acoustic Cover', $videoEntry->title->text);
        $this->assertEquals('html', $videoEntry->content->type);
        $this->assertEquals('self', $videoEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $videoEntry->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/UMFI1hdm96E', $videoEntry->getLink('self')->href);
        $this->assertEquals('text/html', $videoEntry->getLink('alternate')->type);
        $this->assertEquals('http://www.youtube.com/watch?v=UMFI1hdm96E', $videoEntry->getLink('alternate')->href);
        $this->assertEquals('application/atom+xml', $videoEntry->getLink('http://gdata.youtube.com/schemas/2007#video.responses')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/UMFI1hdm96E/responses', $videoEntry->getLink('http://gdata.youtube.com/schemas/2007#video.responses')->href);
        $this->assertEquals('application/atom+xml', $videoEntry->getLink('http://gdata.youtube.com/schemas/2007#video.related')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/UMFI1hdm96E/related', $videoEntry->getLink('http://gdata.youtube.com/schemas/2007#video.related')->href);
        $this->assertEquals('davidchoimusic', $videoEntry->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/davidchoimusic', $videoEntry->author[0]->uri->text);
        $mediaGroup = $videoEntry->mediaGroup;

        $this->assertEquals('plain', $mediaGroup->title->type);
        $this->assertEquals('"Crazy (Gnarles Barkley)" - Acoustic Cover', $mediaGroup->title->text);
        $this->assertEquals('plain', $mediaGroup->description->type);
        $this->assertEquals('Gnarles Barkley acoustic cover http://www.myspace.com/davidchoimusic', $mediaGroup->description->text);
        $this->assertEquals('music, singing, gnarls, barkley, acoustic, cover', $mediaGroup->keywords->text);
        $this->assertEquals(255, $mediaGroup->duration->seconds);

        $this->assertEquals('Music', $mediaGroup->category[0]->label);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/categories.cat', $mediaGroup->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/developertags.cat', $mediaGroup->category[1]->scheme);
        $this->assertEquals('DeveloperTag1', $mediaGroup->category[1]->text);
        $this->assertEquals('video', $mediaGroup->content[0]->medium);
        $this->assertEquals('http://www.youtube.com/v/UMFI1hdm96E', $mediaGroup->content[0]->url);
        $this->assertEquals('application/x-shockwave-flash', $mediaGroup->content[0]->type);
        $this->assertEquals('true', $mediaGroup->content[0]->isDefault);
        $this->assertEquals('full', $mediaGroup->content[0]->expression);
        $this->assertEquals(255, $mediaGroup->content[0]->duration);
        $this->assertEquals(5, $mediaGroup->content[0]->format);
        $this->assertEquals('http://www.youtube.com/watch?v=UMFI1hdm96E', $mediaGroup->player[0]->url);

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
        $this->assertTrue($mediaGroup->private instanceof \Zend\GData\YouTube\Extension\PrivateExtension);

        $this->assertEquals(113321, $videoEntry->statistics->viewCount);
        $this->assertEquals(1, $videoEntry->rating->min);
        $this->assertEquals(5, $videoEntry->rating->max);
        $this->assertEquals(1005, $videoEntry->rating->numRaters);
        $this->assertEquals(4.77, $videoEntry->rating->average);
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/UMFI1hdm96E/comments', $videoEntry->comments->feedLink->href);

        $this->assertEquals('37.398529052734375 -122.0635986328125', $videoEntry->where->point->pos->text);
        $this->assertEquals('2008-09-25', $videoEntry->getVideoRecorded());
    }

    public function verifyAllV2SamplePropertiesAreCorrect($videoEntry)
    {
         $this->assertEquals('tag:youtube.com,2008:video:UMFI1hdm96E',
            $videoEntry->id->text);
        $this->assertEquals('UMFI1hdm96E', $videoEntry->getVideoId());
        $this->assertEquals('2008-12-08T04:04:33.000Z', $videoEntry->updated->text);

        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[0]->scheme);
        $this->assertEquals('cover', $videoEntry->category[0]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[1]->scheme);
        $this->assertEquals('acoustic', $videoEntry->category[1]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[2]->scheme);
        $this->assertEquals('gnarls', $videoEntry->category[2]->term);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $videoEntry->category[3]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#video', $videoEntry->category[3]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[4]->scheme);
        $this->assertEquals('barkley', $videoEntry->category[4]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[5]->scheme);
        $this->assertEquals('music', $videoEntry->category[5]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[6]->scheme);
        $this->assertEquals('singing', $videoEntry->category[6]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/categories.cat', $videoEntry->category[7]->scheme);
        $this->assertEquals('Music', $videoEntry->category[7]->term);

        $this->assertEquals('text', $videoEntry->title->type);
        $this->assertEquals('"Crazy (Gnarles Barkley)" - Acoustic Cover', $videoEntry->title->text);
        $this->assertEquals('application/x-shockwave-flash', $videoEntry->content->type);
        $this->assertEquals('self', $videoEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $videoEntry->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/api/videos/UMFI1hdm96E?v=2', $videoEntry->getLink('self')->href);
        $this->assertEquals('text/html', $videoEntry->getLink('alternate')->type);
        $this->assertEquals('http://www.youtube.com/watch?v=UMFI1hdm96E', $videoEntry->getLink('alternate')->href);
        $this->assertEquals('application/atom+xml', $videoEntry->getLink('http://gdata.youtube.com/schemas/2007#video.responses')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/api/videos/UMFI1hdm96E/responses?v=2', $videoEntry->getLink('http://gdata.youtube.com/schemas/2007#video.responses')->href);
        $this->assertEquals('application/atom+xml', $videoEntry->getLink('http://gdata.youtube.com/schemas/2007#video.related')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/api/videos/UMFI1hdm96E/related?v=2', $videoEntry->getLink('http://gdata.youtube.com/schemas/2007#video.related')->href);
        $this->assertEquals('davidchoimusic', $videoEntry->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/api/users/davidchoimusic', $videoEntry->author[0]->uri->text);

        $mediaGroup = $videoEntry->mediaGroup;

        $this->assertEquals('UMFI1hdm96E', $mediaGroup->getVideoId()->text);
        $this->assertEquals('plain', $mediaGroup->title->type);
        $this->assertEquals('"Crazy (Gnarles Barkley)" - Acoustic Cover', $mediaGroup->title->text);
        $this->assertEquals('plain', $mediaGroup->description->type);
        $this->assertEquals('Gnarles Barkley acoustic cover http://www.myspace.com/davidchoimusic', $mediaGroup->description->text);
        $this->assertEquals('acoustic, barkley, cover, gnarls, music, singing', $mediaGroup->keywords->text);
        $this->assertEquals(255, $mediaGroup->duration->seconds);

        $this->assertEquals('http://gdata.youtube.com/schemas/2007/developertags.cat', $mediaGroup->category[0]->scheme);
        $this->assertEquals('DeveloperTag1', $mediaGroup->category[0]->text);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/categories.cat', $mediaGroup->category[1]->scheme);
        $this->assertEquals('Music', $mediaGroup->category[1]->text);
        $this->assertEquals('video', $mediaGroup->content[0]->medium);
        $this->assertEquals('http://www.youtube.com/v/UMFI1hdm96E&f=gdata_videos', $mediaGroup->content[0]->url);
        $this->assertEquals('application/x-shockwave-flash', $mediaGroup->content[0]->type);
        $this->assertEquals('true', $mediaGroup->content[0]->isDefault);
        $this->assertEquals('full', $mediaGroup->content[0]->expression);
        $this->assertEquals(255, $mediaGroup->content[0]->duration);
        $this->assertEquals(5, $mediaGroup->content[0]->format);
        $this->assertEquals('http://www.youtube.com/watch?v=UMFI1hdm96E', $mediaGroup->player[0]->url);

        $this->assertEquals('HK,TW', $mediaGroup->getMediaRating()->getCountry());
        $this->assertEquals(1, $mediaGroup->getMediaRating()->text);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#mediarating',
            $mediaGroup->getMediaRating()->getScheme());

        $this->assertEquals('http://i.ytimg.com/vi/UMFI1hdm96E/2.jpg', $mediaGroup->thumbnail[0]->url);
        $this->assertEquals(97, $mediaGroup->thumbnail[0]->height);
        $this->assertEquals(130, $mediaGroup->thumbnail[0]->width);
        $this->assertEquals('00:02:07.500', $mediaGroup->thumbnail[0]->time);
        $this->assertEquals('http://i.ytimg.com/vi/UMFI1hdm96E/1.jpg', $mediaGroup->thumbnail[1]->url);
        $this->assertEquals(97, $mediaGroup->thumbnail[1]->height);
        $this->assertEquals(130, $mediaGroup->thumbnail[1]->width);
        $this->assertEquals('00:01:03.750', $mediaGroup->thumbnail[1]->time);
        $this->assertEquals('http://i.ytimg.com/vi/UMFI1hdm96E/3.jpg', $mediaGroup->thumbnail[2]->url);
        $this->assertEquals(97, $mediaGroup->thumbnail[2]->height);
        $this->assertEquals(130, $mediaGroup->thumbnail[2]->width);
        $this->assertEquals('00:03:11.250', $mediaGroup->thumbnail[2]->time);
        $this->assertEquals('http://i.ytimg.com/vi/UMFI1hdm96E/0.jpg', $mediaGroup->thumbnail[3]->url);
        $this->assertEquals(240, $mediaGroup->thumbnail[3]->height);
        $this->assertEquals(320, $mediaGroup->thumbnail[3]->width);
        $this->assertEquals('00:02:07.500', $mediaGroup->thumbnail[3]->time);

        $this->assertEquals(267971, $videoEntry->statistics->viewCount);
        $this->assertEquals(1, $videoEntry->rating->min);
        $this->assertEquals(5, $videoEntry->rating->max);
        $this->assertEquals(2062, $videoEntry->rating->numRaters);
        $this->assertEquals(4.74, $videoEntry->rating->average);
        $this->assertEquals('http://gdata.youtube.com/feeds/api/videos/UMFI1hdm96E/comments?v=2', $videoEntry->comments->feedLink->href);

        $this->assertEquals('37.398529052734375 -122.0635986328125', $videoEntry->where->point->pos->text);
        $this->assertEquals('2008-09-25', $videoEntry->getVideoRecorded());



    }

    public function testGetVideoId()
    {
        $videoEntry = new YouTube\VideoEntry();

        // assert valid ID
        $videoEntry->id = new Extension\Id('http://gdata.youtube.com/feeds/videos/ABCDEFG12AB');
        $this->assertEquals('ABCDEFG12AB', $videoEntry->getVideoId());
    }

    public function testGetVideoIdV2()
    {
        $v2VideoEntry = new YouTube\VideoEntry();
        $v2VideoEntry->setMajorProtocolVersion(2);

        $v2MediaGroup = new \Zend\GData\YouTube\Extension\MediaGroup();
        $v2MediaGroup->setVideoId(
            new \Zend\GData\YouTube\Extension\VideoId('UMFI1hdm96E'));

        $v2VideoEntry->setMediaGroup($v2MediaGroup);

        $this->assertEquals('UMFI1hdm96E', $v2VideoEntry->getVideoId());
    }

    public function testGetVideoIdException()
    {
        $exceptionCaught = false;
        $videoEntry = new YouTube\VideoEntry();

        // use invalid ID
        $videoEntry->id = new Extension\Id('adfadfasf');

        try {
            $videoEntry->getVideoId();

        } catch (App\Exception $e) {
            $exceptionCaught = true;
        }

        $this->assertTrue($exceptionCaught, 'Expected exception not caught: ' .
            'Zend_GData_AppException');
    }

    public function testEmptyEntryShouldHaveNoExtensionElements()
    {
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes()
    {
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionElementsV2()
    {
        $this->entry->transferFromXML($this->v2EntryText);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionAttributesV2()
    {
        $this->entry->transferFromXML($this->v2EntryText);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testEmptyVideoEntryToAndFromStringShouldMatch()
    {
        $entryXml = $this->entry->saveXML();
        $newVideoEntry = new YouTube\VideoEntry();
        $newVideoEntry->transferFromXML($entryXml);
        $newVideoEntryXml = $newVideoEntry->saveXML();
        $this->assertTrue($entryXml == $newVideoEntryXml);
    }

    public function testPrivateSamplePropertiesAreCorrect ()
    {
        $this->entry->transferFromXML($this->privateEntryText);
        $this->verifyAllPrivateSamplePropertiesAreCorrect($this->entry);
    }

    public function testV2SamplePropertiesAreCorrect()
    {
        $this->entry->transferFromXML($this->v2EntryText);
        $this->entry->setMajorProtocolVersion(2);
        $this->verifyAllV2SamplePropertiesAreCorrect($this->entry);
    }

    public function testVideoPrivate()
    {
        $this->entry->transferFromXml($this->privateEntryText);
        $videoEntry = $this->entry;

        $this->assertTrue($videoEntry->isVideoPrivate());
    }

    public function testSetVideoPublic()
    {
        $this->entry->transferFromXml($this->privateEntryText);
        $videoEntry = $this->entry;
        $videoEntry->setVideoPublic();

        $this->assertFalse($videoEntry->isVideoPrivate());
    }
}
