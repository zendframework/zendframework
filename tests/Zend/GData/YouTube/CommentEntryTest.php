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
class CommentEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/GData/YouTube/_files/CommentEntryDataSample1.xml',
                true);
        $this->entry = new YouTube\CommentEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($commentEntry) {
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/Lnio-pqLPgg/comments/CE0314DEBFFC9052',
            $commentEntry->id->text);
        $this->assertEquals('2007-09-02T18:00:04.000-07:00', $commentEntry->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $commentEntry->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#comment', $commentEntry->category[0]->term);
        $this->assertEquals('text', $commentEntry->title->type);
        $this->assertEquals('how to turn ...', $commentEntry->title->text);
        $this->assertEquals('text', $commentEntry->content->type);
        $this->assertEquals('how to turn rejection and heartbreak into something positive is the big mystery of life but you\'re managed to turn it to your advantage with a beautiful song. Who was she?', $commentEntry->content->text);
        $this->assertEquals('self', $commentEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $commentEntry->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/Lnio-pqLPgg/comments/CE0314DEBFFC9052', $commentEntry->getLink('self')->href);
        $this->assertEquals('reneemathome', $commentEntry->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/reneemathome', $commentEntry->author[0]->uri->text);
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

    public function testEmptyCommentEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newCommentEntry = new YouTube\CommentEntry();
        $newCommentEntry->transferFromXML($entryXml);
        $newCommentEntryXml = $newCommentEntry->saveXML();
        $this->assertTrue($entryXml == $newCommentEntryXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->entry->transferFromXML($this->entryText);
        $this->verifyAllSamplePropertiesAreCorrect($this->entry);
    }

    public function testConvertCommentEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newCommentEntry = new YouTube\CommentEntry();
        $newCommentEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newCommentEntry);
        $newCommentEntryXml = $newCommentEntry->saveXML();
        $this->assertEquals($entryXml, $newCommentEntryXml);
    }

}
