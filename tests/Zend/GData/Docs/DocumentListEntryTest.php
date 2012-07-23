<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\Docs;

use Zend\GData\Docs;

/**
 * @category   Zend
 * @package    Zend_GData_Docsj
 * @subpackage UnitTests
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
