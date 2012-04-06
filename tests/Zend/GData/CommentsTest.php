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
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData;
use Zend\GData\Extension;

/**
 * @category   Zend
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 */
class CommentsTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->commentsText = file_get_contents(
                'Zend/GData/_files/CommentsElementSample1.xml',
                true);
        $this->comments = new Extension\Comments();
    }

    public function testEmptyCommentsShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->comments->extensionElements));
        $this->assertTrue(count($this->comments->extensionElements) == 0);
    }

    public function testEmptyCommentsShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->comments->extensionAttributes));
        $this->assertTrue(count($this->comments->extensionAttributes) == 0);
    }

    public function testSampleCommentsShouldHaveNoExtensionElements() {
        $this->comments->transferFromXML($this->commentsText);
        $this->assertTrue(is_array($this->comments->extensionElements));
        $this->assertTrue(count($this->comments->extensionElements) == 0);
    }

    public function testSampleCommentsShouldHaveNoExtensionAttributes() {
        $this->comments->transferFromXML($this->commentsText);
        $this->assertTrue(is_array($this->comments->extensionAttributes));
        $this->assertTrue(count($this->comments->extensionAttributes) == 0);
    }

    public function testNormalCommentsShouldHaveNoExtensionElements() {
        $this->comments->rel = "http://schemas.google.com/g/2005#regular";

        $this->assertEquals("http://schemas.google.com/g/2005#regular", $this->comments->rel);

        $this->assertEquals(0, count($this->comments->extensionElements));
        $newComments = new Extension\Comments();
        $newComments->transferFromXML($this->comments->saveXML());
        $this->assertEquals(0, count($newComments->extensionElements));
        $newComments->extensionElements = array(
                new \Zend\GData\App\Extension\Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newComments->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#regular", $newComments->rel);

        /* try constructing using magic factory */
        $gdata = new \Zend\GData\GData();
        $newComments2 = $gdata->newComments();
        $newComments2->transferFromXML($newComments->saveXML());
        $this->assertEquals(1, count($newComments2->extensionElements));
        $this->assertEquals("http://schemas.google.com/g/2005#regular", $newComments2->rel);
    }

    public function testEmptyCommentsToAndFromStringShouldMatch() {
        $commentsXml = $this->comments->saveXML();
        $newComments = new Extension\Comments();
        $newComments->transferFromXML($commentsXml);
        $newCommentsXml = $newComments->saveXML();
        $this->assertTrue($commentsXml == $newCommentsXml);
    }

    public function testCommentsWithValueToAndFromStringShouldMatch() {
        $this->comments->rel = "http://schemas.google.com/g/2005#regular";
        $commentsXml = $this->comments->saveXML();
        $newComments = new Extension\Comments();
        $newComments->transferFromXML($commentsXml);
        $newCommentsXml = $newComments->saveXML();
        $this->assertTrue($commentsXml == $newCommentsXml);
        $this->assertEquals("http://schemas.google.com/g/2005#regular", $this->comments->rel);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->comments->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->comments->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->comments->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->comments->extensionAttributes['foo2']['value']);
        $commentsXml = $this->comments->saveXML();
        $newComments = new Extension\Comments();
        $newComments->transferFromXML($commentsXml);
        $this->assertEquals('bar', $newComments->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newComments->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullCommentsToAndFromString() {
        $this->comments->transferFromXML($this->commentsText);
        $this->assertEquals("http://schemas.google.com/g/2005#reviews", $this->comments->rel);
        $this->assertTrue($this->comments->feedLink instanceof Extension\FeedLink);
        $this->assertEquals("http://example.com/restaurants/SanFrancisco/432432/reviews", $this->comments->feedLink->href);
    }

}
