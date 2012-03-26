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
class TrackDataTest extends AudioscrobblerTestCase
{
    var $header = "HTTP/1.1 200 OK\r\nContent-type: text/xml\r\n\r\n";

    public function testGetTopFans()
    {
        $testing_response = $this->header .
                            '<?xml version="1.0" encoding="UTF-8"?>
                            <fans artist="Metallica" track="Enter Sandman">
                            <user username="suhis">
                                <url>http://www.last.fm/user/suhis/</url>
                                <image>http://static.last.fm/depth/catalogue/noimage/nouser_140px.jpg</image>
                                <weight>2816666</weight>
                            </user>
                            <user username="M4lu5">
                                <url>http://www.last.fm/user/M4lu5/</url>
                                <image>http://static.last.fm/avatar/ea9c0ddf6b6cc236dfc4297e376e9901.jpg</image>
                                <weight>2380500</weight>
                            </user>
                            <user username="Ceniza666">
                                <url>http://www.last.fm/user/Ceniza666/</url>
                                <image>http://static.last.fm/depth/catalogue/noimage/nouser_140px.jpg</image>
                                <weight>1352000</weight>
                            </user>
                            </fans>
                            ';
        $this->setAudioscrobblerResponse($testing_response);
        $as = $this->getAudioscrobblerService();

        $as->set('artist', 'Metallica');
        $as->set('track', 'Enter Sandman');
        $response = $as->trackGetTopFans();
        $this->assertEquals((string)$response['artist'], 'Metallica');
        $this->assertEquals((string)$response['track'], 'Enter Sandman');
        $this->assertNotNull(count($response->user));
    }

    public function testGetTopTags()
    {
        $testing_response = $this->header .
                            '<?xml version="1.0" encoding="UTF-8"?>
                            <toptags artist="Metallica" track="Enter Sandman">
                            <tag>
                                <name>metal</name>
                                <count>100</count>
                                <url>http://www.last.fm/tag/metal</url>
                            </tag>
                            <tag>
                                <name>heavy metal</name>
                                <count>55</count>
                                <url>http://www.last.fm/tag/heavy%20metal</url>
                            </tag>
                            <tag>
                                <name>rock</name>
                                <count>21</count>
                                <url>http://www.last.fm/tag/rock</url>
                            </tag>
                            </toptags>
                            ';
        $this->setAudioscrobblerResponse($testing_response);
        $as = $this->getAudioscrobblerService();

        $as->set('artist', 'Metallica');
        $as->set('track', 'Enter Sandman');
        $response = $as->trackGetTopTags();
        $this->assertNotNull(count($response->tag));
        $this->assertEquals((string)$response['artist'], 'Metallica');
        $this->assertEquals((string)$response['track'], 'Enter Sandman');
    }
}
