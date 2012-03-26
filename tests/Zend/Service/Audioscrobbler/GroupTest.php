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
class GroupTest extends AudioscrobblerTestCase
{
    private $header = "HTTP/1.1 200 OK\r\nContent-type: text/xml\r\n\r\n";

    public function testWeeklyChartList()
    {
        $testing_response = $this->header .
'<?xml version="1.0" encoding="UTF-8"?>
<weeklychartlist group="Jazz Club">
    <chart from="1159099200" to="1159704000"/>
    <chart from="1159704000" to="1160308800"/>
    <chart from="1160308800" to="1160913600"/>
    <chart from="1160913600" to="1161518400"/>
    <chart from="1161518400" to="1162123200"/>
    <chart from="1162123200" to="1162728000"/>
    <chart from="1162728000" to="1163332800"/>
    <chart from="1163332800" to="1163937600"/>
    <chart from="1163937600" to="1164542400"/>
    <chart from="1164542400" to="1165147200"/>
    <chart from="1165147200" to="1165752000"/>
    <chart from="1165752000" to="1166356800"/>
    <chart from="1166356800" to="1166961600"/>
    <chart from="1166961600" to="1167566400"/>
    <chart from="1167566400" to="1168171200"/>
    <chart from="1168171200" to="1168776000"/>
    <chart from="1168776000" to="1169380800"/>
    <chart from="1169380800" to="1169985600"/>
    <chart from="1169985600" to="1170590400"/>
    <chart from="1170590400" to="1171195200"/>
    <chart from="1171195200" to="1171800000"/>
    <chart from="1171800000" to="1172404800"/>
    <chart from="1172404800" to="1173009600"/>
</weeklychartlist>
';
        $this->setAudioscrobblerResponse($testing_response);

        $as = $this->getAudioscrobblerService();
        $as->set('group', urlencode('Jazz Club'));
        $response = $as->groupGetWeeklyChartList();
        $chart = $response->chart[0];

        $this->assertEquals((int)$chart['from'], 1159099200);
        $this->assertEquals((string)$response['group'], 'Jazz Club');
    }

    public function testWeeklyArtistChartList()
    {
        $testing_response = $this->header .
'<?xml version="1.0" encoding="UTF-8"?>
<weeklyartistchart group="Jazz Club" from="1172404800" to="1173009600">
<artist>
    <name>Miles Davis</name>
    <mbid>561d854a-6a28-4aa7-8c99-323e6ce46c2a</mbid>
    <chartposition>1</chartposition>
    <reach>194</reach>
    <url>http://www.last.fm/music/Miles+Davis</url>
</artist>
<artist>
    <name>The Beatles</name>
    <mbid>b10bbbfc-cf9e-42e0-be17-e2c3e1d2600d</mbid>
    <chartposition>2</chartposition>
    <reach>156</reach>
    <url>http://www.last.fm/music/The+Beatles</url>
</artist>
<artist>
    <name>Pink Floyd</name>
    <mbid>83d91898-7763-47d7-b03b-b92132375c47</mbid>
    <chartposition>3</chartposition>
    <reach>132</reach>
    <url>http://www.last.fm/music/Pink+Floyd</url>
</artist>
<artist>
    <name>John Coltrane</name>
    <mbid>b625448e-bf4a-41c3-a421-72ad46cdb831</mbid>
    <chartposition>4</chartposition>
    <reach>124</reach>
    <url>http://www.last.fm/music/John+Coltrane</url>
</artist>
<artist>
    <name>Radiohead</name>
    <mbid>a74b1b7f-71a5-4011-9441-d0b5e4122711</mbid>
    <chartposition>4</chartposition>
    <reach>124</reach>
    <url>http://www.last.fm/music/Radiohead</url>
</artist>
<artist>
    <name>Herbie Hancock</name>
    <mbid>27613b78-1b9d-4ec3-9db5-fa0743465fdd</mbid>
    <chartposition>6</chartposition>
    <reach>106</reach>
    <url>http://www.last.fm/music/Herbie+Hancock</url>
</artist>
<artist>
    <name>Led Zeppelin</name>
    <mbid>678d88b2-87b0-403b-b63d-5da7465aecc3</mbid>
    <chartposition>7</chartposition>
    <reach>104</reach>
    <url>http://www.last.fm/music/Led+Zeppelin</url>
</artist>
<artist>
    <name>David Bowie</name>
    <mbid>5441c29d-3602-4898-b1a1-b77fa23b8e50</mbid>
    <chartposition>8</chartposition>
    <reach>102</reach>
    <url>http://www.last.fm/music/David+Bowie</url>
</artist>
<artist>
    <name>AIR</name>
    <mbid>cb67438a-7f50-4f2b-a6f1-2bb2729fd538</mbid>
    <chartposition>9</chartposition>
    <reach>96</reach>
    <url>http://www.last.fm/music/AIR</url>
</artist>
<artist>
    <name>Red Hot Chili Peppers</name>
    <mbid>8bfac288-ccc5-448d-9573-c33ea2aa5c30</mbid>
    <chartposition>9</chartposition>
    <reach>96</reach>
    <url>http://www.last.fm/music/Red+Hot+Chili+Peppers</url>
</artist>
</weeklyartistchart>
';
        $this->setAudioscrobblerResponse($testing_response);
        $as = $this->getAudioscrobblerService();

        $as->set('group', urlencode('Jazz Club'));
        $response = $as->groupGetWeeklyArtistChartList();
        $this->assertNotNull(count($response));
        $artist = $response->artist[1];

        $this->assertEquals((string)$artist->name, 'The Beatles');
        $this->assertEquals((string)$artist->url, 'http://www.last.fm/music/The+Beatles');
        $this->assertEquals((string)$response['group'], 'Jazz Club');
    }

    public function testWeeklyAlbumChartList()
    {
        $testing_response = $this->header .
'<?xml version="1.0" encoding="UTF-8"?>
<weeklyalbumchart group="Jazz Club" from="1172404800" to="1173009600">
<album>
    <artist mbid="561d854a-6a28-4aa7-8c99-323e6ce46c2a">Miles Davis</artist>
    <name>Kind of Blue</name>
    <mbid>bee5e0cd-1767-4a8e-9578-6455e87ba60b</mbid>
    <chartposition>1</chartposition>
    <reach>56</reach>
    <url>http://www.last.fm/music/Miles+Davis/Kind+of+Blue</url>
</album>
<album>
    <artist mbid="a74b1b7f-71a5-4011-9441-d0b5e4122711">Radiohead</artist>
    <name>OK Computer</name>
    <mbid>fba5f8fe-c6c8-4511-8562-c9febf482674</mbid>
    <chartposition>2</chartposition>
    <reach>42</reach>
    <url>http://www.last.fm/music/Radiohead/OK+Computer</url>
</album>
<album>
    <artist mbid="8f6bd1e4-fbe1-4f50-aa9b-94c450ec0f11">Portishead</artist>
    <name>Dummy</name>
    <mbid>87888070-1b25-4830-aebc-dee490058b74</mbid>
    <chartposition>3</chartposition>
    <reach>37</reach>
    <url>http://www.last.fm/music/Portishead/Dummy</url>
</album>
<album>
    <artist mbid="">The Arcade Fire</artist>
    <name>Funeral</name>
    <mbid></mbid>
    <chartposition>3</chartposition>
    <reach>37</reach>
    <url>http://www.last.fm/music/The+Arcade+Fire/Funeral</url>
</album>
<album>
    <artist mbid="cc197bad-dc9c-440d-a5b5-d52ba2e14234">Coldplay</artist>
    <name>A Rush of Blood to the Head</name>
    <mbid>b83b32dd-aa1a-4f18-a5af-00e418041617</mbid>
    <chartposition>5</chartposition>
    <reach>34</reach>
    <url>http://www.last.fm/music/Coldplay/A+Rush+of+Blood+to+the+Head</url>
</album>
<album>
    <artist mbid="b10bbbfc-cf9e-42e0-be17-e2c3e1d2600d">The Beatles</artist>
    <name>Abbey Road</name>
    <mbid>03503af3-a0e0-4f7e-8a0d-a1cd4d7225c5</mbid>
    <chartposition>6</chartposition>
    <reach>32</reach>
    <url>http://www.last.fm/music/The+Beatles/Abbey+Road</url>
</album>
<album>
    <artist mbid="b10bbbfc-cf9e-42e0-be17-e2c3e1d2600d">The Beatles</artist>
    <name>Rubber Soul</name>
    <mbid>34b8cb33-5f91-4e0c-b4ec-3fb2d3f2f926</mbid>
    <chartposition>6</chartposition>
    <reach>32</reach>
    <url>http://www.last.fm/music/The+Beatles/Rubber+Soul</url>
</album>
<album>
    <artist mbid="10adbe5e-a2c0-4bf3-8249-2b4cbf6e6ca8">Massive Attack</artist>
    <name>Mezzanine</name>
    <mbid>0d33ef7a-1f5d-4365-b807-b412271b99c3</mbid>
    <chartposition>8</chartposition>
    <reach>31</reach>
    <url>http://www.last.fm/music/Massive+Attack/Mezzanine</url>
</album>
<album>
    <artist mbid="cc197bad-dc9c-440d-a5b5-d52ba2e14234">Coldplay</artist>
    <name>Parachutes</name>
    <mbid>8fb50f96-279e-4d44-92aa-d49ea56f5c08</mbid>
    <chartposition>8</chartposition>
    <reach>31</reach>
    <url>http://www.last.fm/music/Coldplay/Parachutes</url>
</album>
<album>
    <artist mbid="">Gnarls Barkley</artist>
    <name>St. Elsewhere</name>
    <mbid></mbid>
    <chartposition>8</chartposition>
    <reach>31</reach>
    <url>http://www.last.fm/music/Gnarls+Barkley/St.+Elsewhere</url>
</album>
</weeklyalbumchart>
';
        $this->setAudioscrobblerResponse($testing_response); $as = $this->getAudioscrobblerService();

        $as->set('group', urlencode('Jazz Club'));
        $response = $as->groupGetWeeklyAlbumChartList();
        $this->assertNotNull(count($response));
        $album = $response->album[0];

        $this->assertEquals((string)$album->artist, 'Miles Davis');
        $this->assertEquals((string)$album->name, 'Kind of Blue');
        $this->assertEquals((string)$album->chartposition, 1);
        $this->assertEquals((string)$response['group'], 'Jazz Club');
    }

    public function testPreviousWeeklyChartList()
{
        $testing_response = $this->header .
'<?xml version="1.0" encoding="UTF-8"?>
<weeklyartistchart group="Jazz Club" from="1114965332" to="1115570132">
</weeklyartistchart>
';
        $this->setAudioscrobblerResponse($testing_response);

        $as = $this->getAudioscrobblerService();
        $as->set('group', urlencode('Jazz Club'));
        $from = 1114965332;
        $to = 1115570132;
        $response = $as->groupGetWeeklyChartList($from, $to);

        $this->assertNotNull(count($response));
        $this->assertEquals((string)$response['group'], 'Jazz Club');
        $this->assertEquals((int)$response['from'], $from);
        $this->assertEquals((int)$response['to'], $to);
    }

}
