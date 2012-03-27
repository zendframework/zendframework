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
 * @package    Zend_GData_App
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\App;
use Zend\GData\App\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_App
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_App
 */
class ContentTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->contentText = file_get_contents(
                'Zend/GData/App/_files/ContentElementSample1.xml',
                true);
        $this->contentText2 = file_get_contents(
                'Zend/GData/App/_files/ContentElementSample2.xml',
                true);
        $this->content = new Extension\Content();
    }

    public function testEmptyContentShouldHaveEmptyExtensionsList() {
        $this->assertTrue(is_array($this->content->extensionElements));
        $this->assertTrue(count($this->content->extensionElements) == 0);
    }

    public function testEmptyContentToAndFromStringShouldMatch() {
        $contentXml = $this->content->saveXML();
        $newContent = new Extension\Content();
        $newContent->transferFromXML($contentXml);
        $newContentXml = $newContent->saveXML();
        $this->assertTrue($contentXml == $newContentXml);
    }

    public function testContentWithTextAndTypeToAndFromStringShouldMatch() {
        $this->content->text = '<img src="http://www.example.com/image.jpg"/>';
        $this->content->type = 'xhtml';
        $contentXml = $this->content->saveXML();
        $newContent = new Extension\Content();
        $newContent->transferFromXML($contentXml);
        $newContentXml = $newContent->saveXML();
        $this->assertEquals($newContentXml, $contentXml);
        $this->assertEquals('<img src="http://www.example.com/image.jpg"/>', $newContent->text);
        $this->assertEquals('xhtml', $newContent->type);
    }

    public function testContentWithSrcAndTypeToAndFromStringShouldMatch() {
        $this->content->src = 'http://www.example.com/image.png';
        $this->content->type = 'image/png';
        $contentXml = $this->content->saveXML();
        $newContent = new Extension\Content();
        $newContent->transferFromXML($contentXml);
        $newContentXml = $newContent->saveXML();
        $this->assertEquals($newContentXml, $contentXml);
        $this->assertEquals('http://www.example.com/image.png', $newContent->src);
        $this->assertEquals('image/png', $newContent->type);
    }

    public function testConvertContentWithSrcAndTypeToAndFromString() {
        $this->content->transferFromXML($this->contentText);
        $this->assertEquals('http://www.example.com/image.png', $this->content->src);
        $this->assertEquals('image/png', $this->content->type);
    }

    public function testConvertContentWithTextAndTypeToAndFromString() {
        $this->content->transferFromXML($this->contentText2);
        $this->assertEquals('xhtml', $this->content->type);
        $this->assertEquals(1, count($this->content->extensionElements));
    }

}
