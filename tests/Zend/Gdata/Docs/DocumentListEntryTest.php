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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Docs.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Gdata/Docs/DocumentListEntry.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Docs_DocumentListEntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->doc = new Zend_Gdata_Docs_DocumentListEntry( 
                file_get_contents('Zend/Gdata/Docs/_files/TestDataDocumentListEntrySample.xml', true));
    }

    public function testToAndFromString()
    {
        $this->assertTrue($this->doc instanceof Zend_Gdata_Docs_DocumentListEntry);
        $this->assertTrue($this->doc->title->text === 'Test Spreadsheet');
        
        $newDoc = new Zend_Gdata_Docs_DocumentListEntry();
        $doc = new DOMDocument();
        $doc->loadXML($this->doc->saveXML());
        $newDoc->transferFromDom($doc->documentElement);
        
        $this->assertTrue($newDoc->title == $this->doc->title);
    }

    public function testSetMediaSource() 
    {
        // Service object to create the media file source.
        $this->docsClient = new Zend_Gdata_Docs(null);
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
