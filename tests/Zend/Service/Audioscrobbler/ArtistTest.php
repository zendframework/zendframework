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
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Audioscrobbler;

/**
 * @category   Zend
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Audioscrobbler
 */
class ArtistTest extends AudioscrobblerTestCase
{
    private $header = "HTTP/1.1 200 OK\r\nContent-type: text/xml\r\n\r\n";

    public function testGetRelatedArtists()
    {
        $getRelatedArtistsResponse = $this->header .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <similarartists artist="Metallica" streamable="1" picture="http://static.last.fm/proposedimages/sidebar/6/1000024/1285.jpg" mbid="65f4f0c5-ef9e-490c-aee3-909e7ae6b2ab">
                        <artist>
                            <name>Iron Maiden</name>
                            <mbid>ca891d65-d9b0-4258-89f7-e6ba29d83767</mbid>
                            <match>100</match>
                            <url>http://www.last.fm/music/Iron+Maiden</url>
                            <image_small>http://static.last.fm/proposedimages/thumbnail/6/1000107/459375.jpg</image_small>
                            <image>http://static.last.fm/proposedimages/sidebar/6/1000107/459375.jpg</image>
                            <streamable>1</streamable>
                        </artist>
                        <artist>
                            <name>System of a Down</name>
                            <mbid>cc0b7089-c08d-4c10-b6b0-873582c17fd6</mbid>
                            <match>96</match>
                            <url>http://www.last.fm/music/System+of+a+Down</url>
                            <image_small>http://static.last.fm/proposedimages/thumbnail/6/4468/52383.jpg</image_small>
                            <image>http://static.last.fm/proposedimages/sidebar/6/4468/52383.jpg</image>
                            <streamable>1</streamable>
                        </artist>
                        </similarartists>';
        $this->setAudioscrobblerResponse($getRelatedArtistsResponse);

        $as = $this->getAudioscrobblerService();
        $as->set('artist', 'Metallica');
        $response = $as->artistGetRelatedArtists();
        $artist = $response->artist[0];

        $this->assertEquals(count($response->artist), 2);
        $this->assertEquals((string)$artist->name, 'Iron Maiden');
        $this->assertEquals((string)$response['artist'], 'Metallica');
    }

    public function testGetFans()
    {
        $getFansResponse = $this->header .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <fans artist="Metallica">
                        <user username="Liquid_Fire">
                            <url>http://www.last.fm/user/Liquid_Fire/</url>
                            <image>http://static.last.fm/avatar/d8d9af8246e537078a57d5dc826cb34a.gif</image>
                            <weight>617010250</weight>
                        </user>
                        <user username="CeciltheDark">
                            <url>http://www.last.fm/user/CeciltheDark/</url>
                            <image>http://static.last.fm/avatar/30f0417393b696ac2ea06213bc5777d9.png</image>
                            <weight>382812500</weight>
                        </user>
                        </fans>
                        ';
        $this->setAudioscrobblerResponse($getFansResponse);

        $as = $this->getAudioscrobblerService();
        $as->set('artist', 'Metallica');
        $response = $as->artistGetTopFans();
        $user = $response->user[0];

        $this->assertEquals((string)$response['artist'], 'Metallica');
        $this->assertEquals((string)$user->url, 'http://www.last.fm/user/Liquid_Fire/');
    }

    public function testTopTracks()
    {
        $getTopTracksResponse = $this->header .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <mostknowntracks artist="Metallica">
                        <track>
                            <name>Nothing Else Matters</name>
                            <mbid></mbid>
                            <reach>7481</reach>
                            <url>http://www.last.fm/music/Metallica/_/Nothing+Else+Matters</url>
                        </track>
                        <track>
                            <name>Enter Sandman</name>
                            <mbid></mbid>
                            <reach>7139</reach>
                            <url>http://www.last.fm/music/Metallica/_/Enter+Sandman</url>
                        </track>
                        </mostknowntracks>';
        $this->setAudioscrobblerResponse($getTopTracksResponse);

        $as = $this->getAudioscrobblerService();
        $as->set('artist', 'Metallica');
        $response = $as->artistGetTopTracks();
        $track = $response->track[0];

        $this->assertEquals((string)$response['artist'], 'Metallica');
        $this->assertEquals((string)$track->name, 'Nothing Else Matters');
        $this->assertEquals((int)$track->reach, 7481);
    }

    public function testTopAlbums()
    {
        $getTopAlbumsResponse = $this->header .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <topalbums artist="Metallica">
                        <album>
                            <name>Master of Puppets</name>
                            <mbid>fed37cfc-2a6d-4569-9ac0-501a7c7598eb</mbid>
                            <reach>28940</reach>
                            <url>http://www.last.fm/music/Metallica/Master+of+Puppets</url>
                            <releasedate>7 Oct 1986, 00:00</releasedate>
                            <coverart>
                                <small>http://static.last.fm/coverart/50x50/1411810.jpg</small>
                                <medium>http://static.last.fm/coverart/130x130/1411810.jpg</medium>
                                <large>http://static.last.fm/coverart/300x300/1411810.jpg</large>
                            </coverart>
                        </album>
                        <album>
                            <name>Reload</name>
                            <mbid>b05cf8e3-67ad-4d40-9dc1-3a697e3a1bf8</mbid>
                            <reach>27226</reach>
                            <url>http://www.last.fm/music/Metallica/Reload</url>
                            <releasedate>18 Nov 1997, 00:00</releasedate>
                            <coverart>
                                <small>http://images.amazon.com/images/P/B000002HRE.01._SCMZZZZZZZ_.jpg</small>
                                <medium>http://images.amazon.com/images/P/B000002HRE.01._SCMZZZZZZZ_.jpg</medium>
                                <large>http://images.amazon.com/images/P/B000002HRE.01._SCMZZZZZZZ_.jpg</large>
                            </coverart>
                        </album>
                        </topalbums>
                        ';
        $this->setAudioscrobblerResponse($getTopAlbumsResponse);

        $as = $this->getAudioscrobblerService();
        $as->set('artist', 'Metallica');
        $response = $as->artistGetTopAlbums();
        $album = $response->album[0];

        $this->assertEquals((string)$response['artist'], 'Metallica');
        $this->assertEquals((string)$album->name, 'Master of Puppets');
        $this->assertEquals((string)$album->coverart->small, 'http://static.last.fm/coverart/50x50/1411810.jpg');
    }

    public function testTopTags()
    {
        $test_response = $this->header .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <toptags artist="Metallica">
                        <tag>
                            <name>metal</name>
                            <count>100</count>
                            <url>http://www.last.fm/tag/metal</url>
                        </tag>
                        <tag>
                            <name>heavy metal</name>
                            <count>24</count>
                            <url>http://www.last.fm/tag/heavy%20metal</url>
                        </tag>
                        <tag>
                            <name>thrash metal</name>
                            <count>18</count>
                            <url>http://www.last.fm/tag/thrash%20metal</url>
                        </tag>
                        </toptags>
                        ';

        $this->setAudioscrobblerResponse($test_response);

        $as = $this->getAudioscrobblerService();
        $as->set('artist', 'Metallica');
        $response = $as->artistGetTopTags();
        $tag = $response->tag[0];

        $this->assertEquals((string)$response['artist'], 'Metallica');
        $this->assertEquals((string)$tag->name, 'metal');
        $this->assertEquals((int)$tag->count, 100);
    }
}
