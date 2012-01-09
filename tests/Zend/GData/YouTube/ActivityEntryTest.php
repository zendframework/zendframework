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

/**
 * @namespace
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
class ActivityEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/GData/YouTube/_files/ActivityEntryDataSample1.xml',
                true);
        $this->entry = new YouTube\ActivityEntry();
        $this->entry->setMajorProtocolVersion(2);
    }

    private function verifyAllSamplePropertiesAreCorrect ($activityEntry) {
        $this->assertEquals(
            'tag:youtube.com,2008:event:Z2RweXRob24xMTIzNDMwMDAyMzI5NTQ2N' .
            'zg2MA%3D%3D',
            $activityEntry->id->text);
        $this->assertEquals('2009-01-16T09:13:49.000-08:00',
            $activityEntry->updated->text);
        $this->assertEquals(
            'http://gdata.youtube.com/schemas/2007/userevents.cat',
            $activityEntry->category[0]->scheme);
        $this->assertEquals('video_favorited',
            $activityEntry->category[0]->term);
        $this->assertEquals('http://schemas.google.com/g/2005#kind',
            $activityEntry->category[1]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#userEvent',
            $activityEntry->category[1]->term);
        $this->assertEquals('tayzonzay has favorited a video',
            $activityEntry->title->text);

        $this->assertEquals('self', $activityEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml',
            $activityEntry->getLink('self')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/events/VGF5Wm9uZGF5MzEyaIl2' .
            'MTMxOTcxMDk3NzQ5MzM%3D?v=2',
            $activityEntry->getLink('self')->href);

        $this->assertEquals('alternate',
            $activityEntry->getLink('alternate')->rel);
        $this->assertEquals('text/html',
            $activityEntry->getLink('alternate')->type);
        $this->assertEquals('http://www.youtube.com',
            $activityEntry->getLink('alternate')->href);

        $this->assertEquals('http://gdata.youtube.com/schemas/2007#video',
            $activityEntry->getLink(
                'http://gdata.youtube.com/schemas/2007#video')->rel);
        $this->assertEquals('application/atom+xml', $activityEntry->getLink(
                'http://gdata.youtube.com/schemas/2007#video')->type);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/videos/z3U0kuLH974?v=2',
            $activityEntry->getLink(
                'http://gdata.youtube.com/schemas/2007#video')->href);

        $this->assertEquals('tayzonzay', $activityEntry->author[0]->name->text);
        $this->assertEquals(
            'http://gdata.youtube.com/feeds/api/users/tayzonzay',
            $activityEntry->author[0]->uri->text);
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

    public function testEmptyActivityEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newActivityEntry = new YouTube\ActivityEntry();
        $newActivityEntry->transferFromXML($entryXml);
        $newActivityEntryXml = $newActivityEntry->saveXML();
        $this->assertTrue($entryXml == $newActivityEntryXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->entry->transferFromXML($this->entryText);
        $this->verifyAllSamplePropertiesAreCorrect($this->entry);
    }

    public function testHelperMethods() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertEquals('z3U0kuLH974',
            $this->entry->getVideoId()->getText());
        $this->assertEquals('foo',
            $this->entry->getUsername()->getText());
        $this->assertEquals('2',
            $this->entry->getRatingValue());
        $this->assertEquals('video_favorited',
            $this->entry->getActivityType());
    }

    public function testConvertActivityEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newActivityEntry = new YouTube\ActivityEntry();
        $newActivityEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newActivityEntry);
        $newActivityEntryXml = $newActivityEntry->saveXML();
        $this->assertEquals($entryXml, $newActivityEntryXml);
    }

}
