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
 * @package    Zend_GData_Docs
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData\Docs;
use Zend\GData\Docs;

/**
 * @category   Zend
 * @package    Zend_GData_Docsj
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Docsj
 */
class DocumentListEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->doc = new Docs\DocumentListEntry(
                file_get_contents('Zend/GData/Docs/_files/TestDataDocumentListEntrySample.xml', true));
    }

    public function testToAndFromString()
    {
        $this->assertTrue($this->doc instanceof Docs\DocumentListEntry);
        $this->assertTrue($this->doc->title->text === 'Test Spreadsheet');

        $newDoc = new Docs\DocumentListEntry();
        $doc = new \DOMDocument();
        $doc->loadXML($this->doc->saveXML());
        $newDoc->transferFromDom($doc->documentElement);

        $this->assertTrue($newDoc->title == $this->doc->title);
    }

    public function testSetMediaSource()
    {
        // Service object to create the media file source.
        $this->docsClient = new Docs(null);
        $mediaSource = $this->docsClient->newMediaFileSource('test_file_name');
        $mediaSource->setSlug('test slug');
        $mediaSource->setContentType('test content type');
        $this->doc->setMediaSource($mediaSource);
        $this->assertTrue($this->doc->getMediaSource()->getContentType() ===
            'test content type');
        $this->assertTrue($this->doc->getMediaSource()->getSlug() ===
            'test slug');
    }

}
