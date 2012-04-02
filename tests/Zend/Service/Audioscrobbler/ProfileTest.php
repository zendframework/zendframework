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

use Zend\Service\Audioscrobbler;

/**
 * @category   Zend
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Audioscrobbler
 */
class ProfileTest extends AudioscrobblerTestCase
{
    public function testConstructValid()
    {
        $response = new Audioscrobbler\Audioscrobbler();
        $this->assertNotNull($response);
    }

    public function testGetProfileInfo()
    {
        $test_response = "HTTP/1.1 200 OK\r\n" .
                        "Content-type: text/xml\r\n" .
                        "\r\n" .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <profile id="1000002" cluster="2" username="RJ">
                        <url>http://www.last.fm/user/RJ/</url>

                            <realname>Richard Jones</realname>
                                    <mbox_sha1sum>1b374543545e01bc8d555a6a57c637f61f999fdf</mbox_sha1sum>
                                    <registered unixtime="1037793040">Nov 20, 2002</registered>
                                    <age>24</age>
                                    <gender>m</gender>
                                        <country>United Kingdom</country>
                                <playcount>45043</playcount>
                                    <avatar>http://static.last.fm/avatar/0f4bda3a8e49e714c26ef610e2893454.jpg</avatar>
                        </profile>';
        $this->setAudioscrobblerResponse($test_response);

        $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $response = $as->userGetProfileInformation();
        $this->assertNotNull($response);
    }

    public function testGetBadProfileInfo()
    {
        $this->markTestSkipped('Invalid test, communicating with the outside world!');

        $as = new Audioscrobbler();
        $as->set('user', 'kljadsfjllkj');

        $this->setExpectedException('Zend\Service\Audioscrobbler\Exception\RuntimeException', 'xxx');
        $response = $as->userGetProfileInformation();
    }

    public function testUserGetTopArtists( )
    {
        $test_response = "HTTP/1.1 200 OK\r\n" .
                        "Content-type: text/xml\r\n" .
                        "\r\n" .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <topartists user="RJ">
                        <artist>
                            <name>Dream Theater</name>
                            <mbid>28503ab7-8bf2-4666-a7bd-2644bfc7cb1d</mbid>
                            <playcount>854</playcount>
                            <rank>1</rank>
                            <url>http://www.last.fm/music/Dream+Theater</url>
                            <thumbnail>http://static.last.fm/proposedimages/thumbnail/6/4209/432600.jpg</thumbnail>
                            <image>http://static.last.fm/proposedimages/sidebar/6/4209/432600.jpg</image>
                        </artist>
                        </topartists>';

        $this->setAudioscrobblerResponse($test_response); $as = $this->getAudioscrobblerService();

        $as->set('user', 'RJ');
        $response = $as->userGetTopArtists();
        $artist = $response->artist[0];

        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertEquals((string)$artist->name, 'Dream Theater');
        $this->assertNotNull($artist->rank, 1);
    }

    public function testUserGetTopAlbums( )
    {
        $testing_response = "HTTP/1.1 200 OK\r\n" .
                        "Content-type: text/xml\r\n" .
                        "\r\n" .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <topalbums user="Frith">
                        <album>
                            <artist mbid="d8915e13-d67a-4aa0-9c0b-1f126af951af">Hot Chip</artist>
                            <name>The Warning</name>
                            <mbid></mbid>
                            <playcount>227</playcount>
                            <rank>1</rank>
                            <url>http://www.last.fm/music/Hot+Chip/The+Warning</url>
                            <image>
                                <large>http://images.amazon.com/images/P/B000FBFSVU.01._SCMZZZZZZZ_.jpg</large>
                                <medium>http://images.amazon.com/images/P/B000FBFSVU.01._SCMZZZZZZZ_.jpg</medium>
                                <small>http://images.amazon.com/images/P/B000FBFSVU.01._SCMZZZZZZZ_.jpg</small>
                            </image>
                        </album>
                        </topalbums>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'Frith');
        $response = $as->userGetTopAlbums();
        $album = $response->album[0];
        $this->assertEquals((string)$response['user'], 'Frith');
        $this->assertNotNull($album);
        $this->assertEquals((string)$album->name, 'The Warning');
    }

    public function testUserGetTopTracks( )
    {
        $testing_response = "HTTP/1.1 200 OK\r\n" .
                        "Content-type: text/xml\r\n" .
                        "\r\n" .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <toptracks user="RJ">
                        <track>
                                <artist mbid="12ff8858-bfcb-4812-a8dd-7e9debf0cbee">Steppenwolf</artist>
                            <name>The Pusher</name>
                            <mbid></mbid>
                            <playcount>31</playcount>
                            <rank>1</rank>
                            <url>http://www.last.fm/music/Steppenwolf/_/The+Pusher</url>
                        </track>
                        <track>
                                <artist mbid="8f6bd1e4-fbe1-4f50-aa9b-94c450ec0f11">Portishead</artist>
                            <name>Cowboys</name>
                            <mbid></mbid>
                            <playcount>28</playcount>
                            <rank>2</rank>
                            <url>http://www.last.fm/music/Portishead/_/Cowboys</url>
                        </track>
                        </toptracks>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $response = $as->userGetTopTracks();
        $track = $response->track[0];
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertNotNull($track);
        $this->assertEquals((string)$track->artist, 'Steppenwolf');
        $this->assertEquals((int)$track->playcount, 31);
    }

    public function testUserGetTopTags( )
    {
        $testing_response = "HTTP/1.1 200 OK\r\n" .
                        "Content-type: text/xml\r\n" .
                        "\r\n" .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <toptags user="RJ">
                        <tag>
                            <name>rock</name>
                            <count>9</count>
                            <url>http://www.last.fm/tag/rock</url>
                        </tag>
                        <tag>
                            <name>metal</name>
                            <count>8</count>
                            <url>http://www.last.fm/tag/metal</url>
                        </tag>
                        <tag>
                            <name>mellow</name>
                            <count>5</count>
                            <url>http://www.last.fm/tag/mellow</url>
                        </tag>
                        </toptags>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $response = $as->userGetTopTags();
        $tag = $response->tag[1];
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertNotNull($tag);
        $this->assertEquals((string)$tag->name, 'metal');
        $this->assertEquals((int)$tag->count, 8);
    }

    public function testUserGetTopTagsForArtist()
    {
        $testing_response = "HTTP/1.1 200 OK\r\n" .
                        "Content-type: text/xml\r\n" .
                        "\r\n" .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <artisttags user="RJ" artist="Metallica">
                        <tag>
                            <name>metal</name>
                            <count>1</count>
                            <url>http://www.last.fm/tag/metal</url>
                        </tag>
                        <tag>
                            <name>80s</name>
                            <count>1</count>
                            <url>http://www.last.fm/tag/80s</url>
                        </tag>
                        </artisttags>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $as->set('artist', 'Metallica');
        $response = $as->userGetTopTagsForArtist();
        $tag = $response->tag[0];
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertEquals((string)$response['artist'], 'Metallica');
        $this->assertNotNull($tag);
    }

    /**
     * Ensures that userGetTopTagsForArtist() throws an exception when based on bad parameters
     *
     * @return void
     */
    public function testBadUserGetTopTagsForArtist()
    {
        $this->setExpectedException('Zend\Service\Audioscrobbler\Exception\RuntimeException', 'SimpleXML');

        $testingResponse = "HTTP/1.1 200 OK\r\n"
                         . "Content-type: text/xml\r\n"
                         . "\r\n"
                         . "ERROR: Missing 'subject' parameter in querystring";
        $this->setAudioscrobblerResponse($testingResponse);
        $as = $this->getAudioscrobblerService();

        $response = $as->userGetTopTagsForArtist();
    }

    public function testUserGetTopTagsForAlbum()
    {
        $testing_response = "HTTP/1.1 200 OK\r\n" .
                        "Content-type: text/xml\r\n" .
                        "\r\n" .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <albumtags user="RJ" album="Ride the Lightning" artist="Metallica">
                        </albumtags>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $as->set('artist', 'Metallica');
        $as->set('album', 'Ride The Lightning');
        $response = $as->userGetTopTagsForAlbum();
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertEquals((string)strtolower($response['artist']), strtolower('Metallica'));
        $this->assertEquals((string)strtolower($response['album']), strtolower('Ride The Lightning'));
    }

    public function testUserGetTopTagsForTrack()
    {
        $testing_response = "HTTP/1.1 200 OK\r\n" .
                        "Content-type: text/xml\r\n" .
                        "\r\n" .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <tracktags user="RJ" artist="Metallica" track="Nothing Else Matters">
                        </tracktags>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $as->set('artist', 'Metallica');
        $as->set('track', 'Nothing Else Matters');
        $response = $as->userGetTopTagsForTrack();
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertEquals((string)$response['artist'], 'Metallica');
        $this->assertEquals((string)$response['track'], 'Nothing Else Matters');
    }

    public function testUserGetFriends()
    {
        $testing_response = "HTTP/1.1 200 OK\r\n" .
                        "Content-type: text/xml\r\n" .
                        "\r\n" .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <friends user="RJ">
                        <user username="julians">
                            <url>http://www.last.fm/user/julians/</url>
                            <image>http://static.last.fm/avatar/9ca899b8f20b7173d47983cc0533be8c.gif</image>
                            <connections>
                                </connections>

                            </user>
                        <user username="Korean_Cowboy">
                            <url>http://www.last.fm/user/Korean_Cowboy/</url>
                            <image>http://static.last.fm/avatar/091614ec2288764362c94f047d207336.jpg</image>
                            <connections>
                                </connections>

                            </user>
                        </friends>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $response = $as->userGetFriends();
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertEquals(count($response->user), 2);
    }

    public function testUserGetNeighbours()
    {
        $testing_response = "HTTP/1.1 200 OK\r\n" .
                        "Content-type: text/xml\r\n" .
                        "\r\n" .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <neighbours user="RJ">
                        <user username="count-bassy">
                            <url>http://www.last.fm/user/count-bassy/</url>
                            <image>http://static.last.fm/avatar/3da65e2f347f64c033c9eced171e7a21.gif</image>
                            <match>100</match>
                        </user>
                        <user username="arcymarcy">
                            <url>http://www.last.fm/user/arcymarcy/</url>
                            <image>http://static.last.fm/avatar/eed7d6afea225f85cfcd6ee61eac19aa.jpg</image>
                            <match>93.12</match>
                        </user>
                        </neighbours>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $response = $as->userGetNeighbours();
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertEquals(count($response->user), 2);
        $user = $response->user[1];
        $this->assertEquals((string)$user['username'], 'arcymarcy');
    }

    public function testUserRecentTracks()
    {
        $testing_response = "HTTP/1.1 200 OK\r\n" .
                        "Content-type: text/xml\r\n" .
                        "\r\n" .
                        '<?xml version="1.0" encoding="UTF-8"?>
                        <recenttracks user="RJ">
                        <track>
                                <artist mbid="97d9060d-2cd5-4acd-b44f-c39ea2da4753">Tok Tok Tok</artist>
                            <name>Always An Excuse</name>
                            <mbid></mbid>
                            <url>http://www.last.fm/music/Tok+Tok+Tok/_/Always+An+Excuse</url>
                                    <date uts="1173203133">6 Mar 2007, 17:45</date>
                        </track>
                        <track>
                                <artist mbid="97d9060d-2cd5-4acd-b44f-c39ea2da4753">Tok Tok Tok</artist>
                            <name>What Has Roots</name>
                            <mbid></mbid>
                            <url>http://www.last.fm/music/Tok+Tok+Tok/_/What+Has+Roots</url>
                                    <date uts="1173202787">6 Mar 2007, 17:39</date>
                        </track>
                        </recenttracks>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $response = $as->userGetRecentTracks();
        $track = $response->track[0];
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertEquals(count($response->track), 2);
        $this->assertEquals((string)$track->name, 'Always An Excuse');
    }

    public function testUserRecentBannedTracks()
    {
        $testing_response = "HTTP/1.1 200 OK\r\nContent-type: text/xml\r\n\r\n" .
                            '<?xml version="1.0" encoding="UTF-8"?>
                            <recentbannedtracks user="RJ">
                            <track>
                                    <artist mbid="27613b78-1b9d-4ec3-9db5-fa0743465fdd">Herbie Hancock</artist>
                                <name>Rockit</name>
                                <mbid></mbid>
                                <url>http://www.last.fm/music/Herbie+Hancock/_/Rockit</url>
                                <date uts="1171126557">10 Feb 2007, 16:55</date>
                            </track>
                            <track>
                                    <artist mbid="7e54d133-2525-4bc0-ae94-65584145a386">Plaid</artist>
                                <name>Plaid Rmx</name>
                                <mbid></mbid>
                                <url>http://www.last.fm/music/Plaid/_/Plaid+Rmx</url>
                                <date uts="1161129235">17 Oct 2006, 23:53</date>
                            </track>
                            </recentbannedtracks>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $response = $as->userGetRecentBannedTracks();
        $track = $response->track[0];
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertEquals(count($response->track), 2);
        $this->assertEquals((string)$track->artist, 'Herbie Hancock');
        $this->assertEquals((string)$track->name, 'Rockit');
    }

    public function testUserRecentLovedTracks()
    {
        $testing_response = "HTTP/1.1 200 OK\r\nContent-type: text/xml\r\n\r\n" .
                            '<?xml version="1.0" encoding="UTF-8"?>
                            <recentlovedtracks user="RJ">
                            <track>
                                    <artist mbid="9a7c8025-1af8-42cd-8df8-857220610bc5">Spyro Gyra</artist>
                                <name>Morning Dance</name>
                                <mbid></mbid>
                                <url>http://www.last.fm/music/Spyro+Gyra/_/Morning+Dance</url>
                                <date uts="1163006139">8 Nov 2006, 17:15</date>
                            </track>
                            <track>
                                    <artist mbid="149e6720-4e4a-41a4-afca-6d29083fc091">Bad Religion</artist>
                                <name>I Love My Computer</name>
                                <mbid></mbid>
                                <url>http://www.last.fm/music/Bad+Religion/_/I+Love+My+Computer</url>
                                <date uts="1162310037">31 Oct 2006, 15:53</date>
                            </track>
                            </recentlovedtracks>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $response = $as->userGetRecentLovedTracks();
        $track = $response->track[1];
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertNotNull((string)$track->name, 'Morning Dance');
        $this->assertNotNull((string)$track->date, '31 Oct 2006, 15:53');
        $this->assertNotNull($response->track);
    }

    public function testUserGetWeeklyChartList()
    {
        $testing_response = "HTTP/1.1 200 OK\r\nContent-type: text/xml\r\n\r\n" .
                            '<?xml version="1.0" encoding="UTF-8"?>
                            <weeklychartlist user="RJ">
                                <chart from="1108296002" to="1108900802"/>
                                <chart from="1108900801" to="1109505601"/>
                                <chart from="1109505601" to="1110110401"/>
                                <chart from="1110715201" to="1111320001"/>
                                <chart from="1111320001" to="1111924801"/>
                                <chart from="1111924801" to="1112529601"/>
                                <chart from="1112529601" to="1113134401"/>
                            </weeklychartlist>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $response = $as->userGetWeeklyChartList();
        $chart = $response->chart[0];
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertEquals(count($response->chart), 7);
        $this->assertEquals((string)$chart['from'], '1108296002');
        $this->assertEquals((string)$chart['to'], '1108900802');
    }

    public function testUserGetRecentWeeklyArtistChart()
    {
        $testing_response = "HTTP/1.1 200 OK\r\nContent-type: text/xml\r\n\r\n" .
                            '<?xml version="1.0" encoding="UTF-8"?>
                            <weeklyartistchart user="RJ" from="1172404800" to="1173009600">
                            <artist>
                                <name>Miles Davis</name>
                                <mbid>561d854a-6a28-4aa7-8c99-323e6ce46c2a</mbid>
                                <chartposition>1</chartposition>
                                <playcount>30</playcount>
                                    <url>http://www.last.fm/music/Miles+Davis</url>
                            </artist>
                            <artist>
                                <name>Guano Apes</name>
                                <mbid>66da25f9-1534-4dd1-b88c-718bc24e1ccd</mbid>
                                <chartposition>2</chartposition>
                                <playcount>28</playcount>
                                    <url>http://www.last.fm/music/Guano+Apes</url>
                            </artist>
                            </weeklyartistchart>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $response = $as->userGetWeeklyArtistChart();
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertNotNull($response->weeklyartistchart);
        $this->assertNotNull($response->artist);
    }

    public function testUserGetWeeklyAlbumChart()
    {
        $testing_response = "HTTP/1.1 200 OK\r\nContent-type: text/xml\r\n\r\n" .
                            '<?xml version="1.0" encoding="UTF-8"?>
                            <weeklyalbumchart user="RJ" from="1172404800" to="1173009600">
                            <album>
                                <artist mbid="6da0515e-a27d-449d-84cc-00713c38a140">Skid Row</artist>
                                <name>Slave To The Grid</name>
                                <mbid></mbid>
                                <chartposition>1</chartposition>
                                <playcount>12</playcount>
                                <url>http://www.last.fm/music/Skid+Row/Slave+To+The+Grid</url>
                            </album>
                            <album>
                                <artist mbid="66da25f9-1534-4dd1-b88c-718bc24e1ccd">Guano Apes</artist>
                                <name>Walking on a Thin Line</name>
                                <mbid>769a46de-52e2-4322-9db0-cbd6b789e3f8</mbid>
                                <chartposition>1</chartposition>
                                <playcount>12</playcount>
                                <url>http://www.last.fm/music/Guano+Apes/Walking+on+a+Thin+Line</url>
                            </album>
                            </weeklyalbumchart>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $response = $as->userGetWeeklyAlbumChart();
        $album = $response->album[0];
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertEquals(count($response->album), 2);
        $this->assertEquals((string)$album->artist, 'Skid Row');
        $this->assertEquals((string)$album->name, 'Slave To The Grid');
    }

    public function testUserGetPreviousWeeklyArtistChart()
    {
        $testing_response = "HTTP/1.1 200 OK\r\nContent-type: text/xml\r\n\r\n" .
                            '<?xml version="1.0" encoding="UTF-8"?>
                            <weeklyartistchart user="RJ" from="1114965332" to="1115570132">
                            <artist>
                                <name>Nine Inch Nails</name>
                                <mbid>b7ffd2af-418f-4be2-bdd1-22f8b48613da</mbid>
                                <chartposition>1</chartposition>
                                <playcount>23</playcount>
                                    <url>http://www.last.fm/music/Nine+Inch+Nails</url>
                            </artist>
                            <artist>
                                <name>The Doors</name>
                                <mbid>9efff43b-3b29-4082-824e-bc82f646f93d</mbid>
                                <chartposition>2</chartposition>
                                <playcount>3</playcount>
                                    <url>http://www.last.fm/music/The+Doors</url>
                            </artist>
                            </weeklyartistchart>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $from = 1114965332;
        $to = 1115570132;
        $response = $as->userGetWeeklyArtistChart($from, $to);
        $artist = $response->artist[0];
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertEquals((int)$response['from'], $from);
        $this->assertEquals((int)$response['to'], $to);
        $this->assertEquals((string)$artist->name, 'Nine Inch Nails');
        $this->assertEquals(count($response->artist), 2);
    }

    public function testUserGetPreviousWeeklyAlbumChart()
    {
        $testing_response = "HTTP/1.1 200 OK\r\nContent-type: text/xml\r\n\r\n" .
                            '<?xml version="1.0" encoding="UTF-8"?>
                            <weeklyalbumchart user="RJ" from="1114965332" to="1115570132">
                            <album>
                                <artist mbid="9efff43b-3b29-4082-824e-bc82f646f93d">The Doors</artist>
                                <name>The Doors Box Set</name>
                                <mbid></mbid>
                                <chartposition>1</chartposition>
                                <playcount>2</playcount>
                                <url>http://www.last.fm/music/The+Doors/The+Doors+Box+Set</url>
                            </album>
                            <album>
                                <artist mbid="5b11f4ce-a62d-471e-81fc-a69a8278c7da">Nirvana</artist>
                                <name>Nirvana</name>
                                <mbid>d8f9547d-5e46-45f0-b694-0d9af9e2de63</mbid>
                                <chartposition>1</chartposition>
                                <playcount>2</playcount>
                                <url>http://www.last.fm/music/Nirvana/Nirvana</url>
                            </album>
                            </weeklyalbumchart>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $from = 1114965332;
        $to = 1115570132;
        $response = $as->userGetWeeklyAlbumChart($from, $to);
        $album = $response->album[0];
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertEquals((int)$response['from'], 1114965332);
        $this->assertEquals((int)$response['to'], 1115570132);
        $this->assertEquals(count($response->album), 2);
    }

    public function testUserGetPreviousWeeklyTrackChart()
    {
        $testing_response = "HTTP/1.1 200 OK\r\nContent-type: text/xml\r\n\r\n" .
                            '<?xml version="1.0" encoding="UTF-8"?>
                            <weeklytrackchart user="RJ" from="1114965332" to="1115570132">
                                <track>
                                        <artist mbid="f73b2b70-33d5-4118-923b-05ba8ad7e702">The Kleptones</artist>
                                    <name>Question</name>
                                    <mbid></mbid>
                                    <chartposition>1</chartposition>
                                    <playcount>3</playcount>
                                            <url>http://www.last.fm/music/The+Kleptones/_/Question</url>
                                </track>
                                <track>
                                        <artist mbid="b7ffd2af-418f-4be2-bdd1-22f8b48613da">Nine Inch Nails</artist>
                                    <name>All the Love in the World</name>
                                    <mbid></mbid>
                                    <chartposition>2</chartposition>
                                    <playcount>2</playcount>
                                            <url>http://www.last.fm/music/Nine+Inch+Nails/_/All+the+Love+in+the+World</url>
                                </track>
                            </weeklytrackchart>';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();
        $as->set('user', 'RJ');
        $from = 1114965332;
        $to = 1115570132;
        $response = $as->userGetWeeklyTrackChart($from, $to);
        $track = $response->track[0];
        $this->assertEquals((string)$response['user'], 'RJ');
        $this->assertEquals((int)$response['from'], $from);
        $this->assertEquals((int)$response['to'], $to);
        $this->assertEquals((string)$track->artist, 'The Kleptones');
        $this->assertEquals(count($response->track), 2);
    }
}
