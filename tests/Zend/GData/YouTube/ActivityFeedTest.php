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
class ActivityFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->feedText = file_get_contents(
                'Zend/GData/YouTube/_files/ActivityFeedDataSample1.xml',
                true);
        $this->feed = new YouTube\ActivityFeed();
        $this->feed->setMajorProtocolVersion(2);
    }

    private function verifyAllSamplePropertiesAreCorrect ($activityFeed) {
        $this->assertEquals('2009-01-28T09:13:49.000-08:00',
            $activityFeed->updated->text);
        $this->assertEquals(
            'http://schemas.google.com/g/2005#kind',
            $activityFeed->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#userEvent',
            $activityFeed->category[0]->term);
        $this->assertEquals('Activity of tayzonzay',
            $activityFeed->title->text);

        $this->assertEquals('self', $activityFeed->getLink('self')->rel);
        $this->assertEquals('application/atom+xml',
            $activityFeed->getLink('self')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/events?author=gdpython' .
            '&start-index=1&max-results=25&v=2',
            $activityFeed->getLink('self')->href);
        $this->assertEquals('http://schemas.google.com/g/2005#feed',
            $activityFeed->getLink(
                'http://schemas.google.com/g/2005#feed')->rel);
        $this->assertEquals('application/atom+xml',
            $activityFeed->getLink(
                'http://schemas.google.com/g/2005#feed')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/api/events?v=2',
            $activityFeed->getLink(
                'http://schemas.google.com/g/2005#feed')->href);
        $this->assertEquals('http://schemas.google.com/g/2005#batch',
            $activityFeed->getLink(
                'http://schemas.google.com/g/2005#batch')->rel);
        $this->assertEquals('application/atom+xml',
            $activityFeed->getLink(
                'http://schemas.google.com/g/2005#batch')->type);
        $this->assertEquals(
            'application/atom+xml',
            $activityFeed->getLink(
                'http://schemas.google.com/g/2005#batch')->type);
        $this->assertEquals('service',
            $activityFeed->getLink('service')->rel);
        $this->assertEquals('application/atomsvc+xml',
            $activityFeed->getLink('service')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/events?alt=atom-service&v=2',
            $activityFeed->getLink('service')->href);

        $this->assertEquals('YouTube', $activityFeed->author[0]->name->text);
        $this->assertEquals('http://www.youtube.com/',
            $activityFeed->author[0]->uri->text);
        $this->assertEquals(12, $activityFeed->totalResults->text);
        $this->assertEquals(1, $activityFeed->startIndex->text);
        $this->assertEquals(25, $activityFeed->itemsPerPage->text);
    }

    public function testEmptyEntryShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertEquals(0, count($this->feed->extensionElements));
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->feed->extensionAttributes));
        $this->assertEquals(0, count($this->feed->extensionAttributes));
    }

    public function testSampleEntryShouldHaveNoExtensionElements() {
        $this->feed->transferFromXML($this->feedText);
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertEquals(0, count($this->feed->extensionElements));
    }

    public function testSampleEntryShouldHaveNoExtensionAttributes() {
        $this->feed->transferFromXML($this->feedText);
        $this->assertTrue(is_array($this->feed->extensionAttributes));
        $this->assertEquals(0, count($this->feed->extensionAttributes));
    }

    public function testEmptyActivityFeedToAndFromStringShouldMatch() {
        $entryXml = $this->feed->saveXML();
        $newActivityFeed = new YouTube\ActivityFeed();
        $newActivityFeed->transferFromXML($entryXml);
        $newActivityFeedXml = $newActivityFeed->saveXML();
        $this->assertTrue($entryXml == $newActivityFeedXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->feed->transferFromXML($this->feedText);
        $this->verifyAllSamplePropertiesAreCorrect($this->feed);
    }

    public function testConvertActivityFeedToAndFromString() {
        $this->feed->transferFromXML($this->feedText);
        $entryXml = $this->feed->saveXML();
        $newActivityFeed = new YouTube\ActivityFeed();
        $newActivityFeed->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newActivityFeed);
        $newActivityFeedXml = $newActivityFeed->saveXML();
        $this->assertEquals($entryXml, $newActivityFeedXml);
    }

    public function testEntryCanBeRetrieved() {
        $this->feed->transferFromXML($this->feedText);
        $this->assertTrue(count($this->feed->entries) > 0);
    }

}
