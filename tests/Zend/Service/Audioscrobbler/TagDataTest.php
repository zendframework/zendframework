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

/**
 * @namespace
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
class TagDataTest extends AudioscrobblerTestCase
{
    var $header = "HTTP/1.1 200 OK\r\nContent-type: text/xml\r\n\r\n";

    public function testGetTopTags()
    {
        $this->markTestSkipped('Invalid test, communicating with real-world service.');

        try {
            $as = $this->getAudioscrobblerService();
            $response = $as->tagGetTopTags();
            $this->assertNotNull(count($response->tag));
        } catch (Exception $e ) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }
    }

    public function testGetTopArtists()
    {
        $testing_response = $this->header .
'<?xml version="1.0" encoding="UTF-8"?>
<tag tag="rock" count="785147">
<artist name="Red Hot Chili Peppers" count="5097" streamable="yes">
    <mbid>8bfac288-ccc5-448d-9573-c33ea2aa5c30</mbid>
    <url>http://www.last.fm/music/Red+Hot+Chili+Peppers</url>
    <thumbnail>http://static.last.fm/proposedimages/thumbnail/6/1274/447958.jpg</thumbnail>
    <image>http://static.last.fm/proposedimages/sidebar/6/1274/447958.jpg</image>
</artist>
<artist name="Foo Fighters" count="3566" streamable="yes">
    <mbid>67f66c07-6e61-4026-ade5-7e782fad3a5d</mbid>
    <url>http://www.last.fm/music/Foo+Fighters</url>
    <thumbnail>http://static.last.fm/proposedimages/thumbnail/6/1000062/458.jpg</thumbnail>
    <image>http://static.last.fm/proposedimages/sidebar/6/1000062/458.jpg</image>
</artist>
<artist name="Radiohead" count="3457" streamable="yes">
    <mbid>a74b1b7f-71a5-4011-9441-d0b5e4122711</mbid>
    <url>http://www.last.fm/music/Radiohead</url>
    <thumbnail>http://static.last.fm/proposedimages/thumbnail/6/979/453678.jpg</thumbnail>
    <image>http://static.last.fm/proposedimages/sidebar/6/979/453678.jpg</image>
</artist>
</tag>
';
        $this->setAudioscrobblerResponse($testing_response);
        $as = $this->getAudioscrobblerService();

        $as->set('tag', 'Rock');
        $response = $as->tagGetTopArtists();
        $this->assertNotNull($response->artist);
        $this->assertEquals((string)$response['tag'], strtolower($as->get('tag')));
    }

    public function testGetTopAlbums()
    {
        $testing_response = $this->header .
'<?xml version="1.0" encoding="UTF-8"?>
<tag tag="rock" count="786251">
<album name="Fallen" count="79" streamable="yes">
        <artist name="Evanescence">
        <mbid>f4a31f0a-51dd-4fa7-986d-3095c40c5ed9</mbid>
        <url>http://www.last.fm/music/Evanescence</url>
    </artist>
    <url>http://www.last.fm/music/Evanescence/Fallen</url>
    <coverart>
        <small>http://images.amazon.com/images/P/B00008US8R.01._SCMZZZZZZZ_.jpg</small>
        <medium>http://images.amazon.com/images/P/B00008US8R.01._SCMZZZZZZZ_.jpg</medium>
        <large>http://images.amazon.com/images/P/B00008US8R.01._SCMZZZZZZZ_.jpg</large>
    </coverart>
</album>
<album name="Elephant" count="74" streamable="yes">
        <artist name="The White Stripes">
        <mbid>11ae9fbb-f3d7-4a47-936f-4c0a04d3b3b5</mbid>
        <url>http://www.last.fm/music/The+White+Stripes</url>
    </artist>
    <url>http://www.last.fm/music/The+White+Stripes/Elephant</url>
    <coverart>
        <small>http://images-eu.amazon.com/images/P/B00007KN36.02.THUMBZZZ.jpg</small>
        <medium>http://images-eu.amazon.com/images/P/B00007KN36.02.MZZZZZZZ.jpg</medium>
        <large>http://images-eu.amazon.com/images/P/B00007KN36.02.LZZZZZZZ.jpg</large>
    </coverart>
</album>
</tag>
';
        $this->setAudioscrobblerResponse($testing_response);
        $as = $this->getAudioscrobblerService();
        $as->set('tag', 'Rock');
        $response = $as->tagGetTopAlbums();
        $this->assertNotNull(count($response->album));
        $this->assertEquals((string)$response['tag'], strtolower($as->get('tag')));
    }

    public function testGetTopTracks()
    {
            $testing_response = $this->header .
'<?xml version="1.0" encoding="UTF-8"?>
<tag tag="rock" count="785836">
<track name="Dani California" count="295" streamable="yes">
        <artist name="Red Hot Chili Peppers">
        <mbid>8bfac288-ccc5-448d-9573-c33ea2aa5c30</mbid>
        <url>http://www.last.fm/music/Red+Hot+Chili+Peppers</url>
    </artist>
    <url>http://www.last.fm/music/Red+Hot+Chili+Peppers/_/Dani+California</url>
</track>
<track name="Wonderwall" count="290" streamable="yes">
        <artist name="Oasis">
        <mbid>39ab1aed-75e0-4140-bd47-540276886b60</mbid>
        <url>http://www.last.fm/music/Oasis</url>
    </artist>
    <url>http://www.last.fm/music/Oasis/_/Wonderwall</url>
</track>
<track name="Boulevard of Broken Dreams" count="271" streamable="yes">
        <artist name="Green Day">
        <mbid></mbid>
        <url>http://www.last.fm/music/Green+Day</url>
    </artist>
    <url>http://www.last.fm/music/Green+Day/_/Boulevard+of+Broken+Dreams</url>
</track>
</tag>
';
        $this->setAudioscrobblerResponse($testing_response);
        $as = $this->getAudioscrobblerService();
        $as->set('tag', 'Rock');
        $response = $as->tagGetTopTracks();
        $artist = $response->track[0];
        $this->assertNotNull(count($response->track));
        $this->assertNotNull((string)$artist->name);
        $this->assertEquals((string)$response['tag'], strtolower($as->get('tag')));
    }

}
