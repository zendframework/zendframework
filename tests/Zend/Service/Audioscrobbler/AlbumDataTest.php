<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Audioscrobbler;


/**
 * @category   Zend
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Audioscrobbler
 */
class AlbumDataTest extends AudioscrobblerTestCase
{
    public function testGetAlbumInfo()
    {
        $albumInfoResponse = "HTTP/1.1 200 OK\r\nContent-type: text/xml\r\n\r\n".
'<?xml version="1.0" encoding="UTF-8"?>
<album artist="Metallica" title="Metallica">
    <reach>85683</reach>
    <url>http://www.last.fm/music/Metallica/Metallica</url>
    <releasedate>    1 Jan 1994, 00:00</releasedate>
    <coverart>
        <small>http://static.last.fm/coverart/50x50/1411800.jpg</small>
        <medium>http://static.last.fm/coverart/130x130/1411800.jpg</medium>
        <large>http://static.last.fm/coverart/300x300/1411800.jpg</large>
    </coverart>
    <mbid>3750d9e2-59f5-471d-8916-463433069bd1</mbid>
    <tracks>
                <track title="Enter Sandman (LP Version)">
            <reach>26</reach>
            <url>http://www.last.fm/music/Metallica/_/Enter+Sandman+%28LP+Version%29</url>
                    </track>
                <track title="Sad But True (LP Version)">
            <reach>22</reach>
            <url>http://www.last.fm/music/Metallica/_/Sad+But+True+%28LP+Version%29</url>
                    </track>
                <track title="Holier Than Thou (LP Version)">
            <reach>2</reach>
            <url>http://www.last.fm/music/Metallica/_/Holier+Than+Thou+%28LP+Version%29</url>
                    </track>
                <track title="The Unforgiven (LP Version)">
            <reach>10</reach>
            <url>http://www.last.fm/music/Metallica/_/The+Unforgiven+%28LP+Version%29</url>
                    </track>
                <track title="Wherever I May Roam (LP Version)">
            <reach>4</reach>
            <url>http://www.last.fm/music/Metallica/_/Wherever+I+May+Roam+%28LP+Version%29</url>
                    </track>
                <track title="Don\'t Tread On Me (LP Version)">
            <reach>3</reach>
            <url>http://www.last.fm/music/Metallica/_/Don%27t+Tread+On+Me+%28LP+Version%29</url>
                    </track>
                <track title="Through The Never (LP Version)">
            <reach>3</reach>
            <url>http://www.last.fm/music/Metallica/_/Through+The+Never+%28LP+Version%29</url>
                    </track>
                <track title="Nothing Else Matters (LP Version)">
            <reach>26</reach>
            <url>http://www.last.fm/music/Metallica/_/Nothing+Else+Matters+%28LP+Version%29</url>
                    </track>
                <track title="Of Wolf And Man (LP Version)">
            <reach>3</reach>
            <url>http://www.last.fm/music/Metallica/_/Of+Wolf+And+Man+%28LP+Version%29</url>
                    </track>
                <track title="The God That Failed (LP Version)">
            <reach>2</reach>
            <url>http://www.last.fm/music/Metallica/_/The+God+That+Failed+%28LP+Version%29</url>
                    </track>
                <track title="My Friend Of Misery (LP Version)">
            <reach>3</reach>
            <url>http://www.last.fm/music/Metallica/_/My+Friend+Of+Misery+%28LP+Version%29</url>
                    </track>
                <track title="The Struggle Within (LP Version)">
            <reach>3</reach>
            <url>http://www.last.fm/music/Metallica/_/The+Struggle+Within+%28LP+Version%29</url>
                    </track>
            </tracks>
</album>
';

        $this->setAudioscrobblerResponse($albumInfoResponse);

        $as = $this->getAudioscrobblerService();
        $as->set('album', 'Metallica');
        $as->set('artist', 'Metallica');
        $response = $as->albumGetInfo();
        $track = $response->tracks->track[0];
        $this->assertEquals((string)$response['artist'], 'Metallica');
        $this->assertEquals((string)$response['title'], 'Metallica');
        $this->assertEquals((string)$track->url, 'http://www.last.fm/music/Metallica/_/Enter+Sandman+%28LP+Version%29');
        $this->assertEquals(count($response->tracks->track), 12);;
    }
}
