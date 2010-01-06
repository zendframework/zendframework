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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once 'Zend/Gdata/Entry.php';

/**
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 */
class Zend_Gdata_EntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entry = new Zend_Gdata_Entry();
        $this->entryText = file_get_contents(
                'Zend/Gdata/_files/EntrySample1.xml',
                true);
        $this->etagLocalName = 'etag';
        $this->expectedEtag = 'W/"CkcHQH8_fCp7ImA9WxRTGEw."';
        $this->expectedMismatchExceptionMessage = "ETag mismatch";
        $this->gdNamespace = 'http://schemas.google.com/g/2005';
        $this->openSearchNamespacev1 = 'http://a9.com/-/spec/opensearchrss/1.0/';
        $this->openSearchNamespacev2 = 'http://a9.com/-/spec/opensearch/1.1/';

    }

    public function testXMLHasNoEtagsWhenUsingV1() {
        $etagData = 'Quux';
        $this->entry->setEtag($etagData);
        $domNode = $this->entry->getDOM(null, 1, null);
        $this->assertNull($domNode->attributes->getNamedItemNS($this->gdNamespace, $this->etagLocalName));
    }

    public function testXMLHasNoEtagsWhenUsingV1X() {
        $etagData = 'Quux';
        $this->entry->setEtag($etagData);
        $domNode = $this->entry->getDOM(null, 1, 1);
        $this->assertNull($domNode->attributes->getNamedItemNS($this->gdNamespace, $this->etagLocalName));
    }

    public function testXMLHasEtagsWhenUsingV2() {
        $etagData = 'Quux';
        $this->entry->setEtag($etagData);
        $domNode = $this->entry->getDOM(null, 2, null);
        $this->assertEquals($etagData, $domNode->attributes->getNamedItemNS($this->gdNamespace, $this->etagLocalName)->nodeValue);
    }

    public function testXMLHasEtagsWhenUsingV2X() {
        $etagData = 'Quux';
        $this->entry->setEtag($etagData);
        $domNode = $this->entry->getDOM(null, 2, 1);
        $this->assertEquals($etagData, $domNode->attributes->getNamedItemNS($this->gdNamespace, $this->etagLocalName)->nodeValue);
    }

    public function testXMLETagsPropagateToEntry() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertEquals($this->expectedEtag, $this->entry->getEtag());
    }

    public function testXMLandHTMLEtagsDifferingThrowsException() {
        $exceptionCaught = false;
        $this->entry->setEtag("Foo");
        try {
            $this->entry->transferFromXML($this->entryText);
        } catch (Zend_Gdata_App_IOException $e) {
            $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught, "Exception Zend_Gdata_IO_Exception expected");
    }

    public function testHttpAndXmlEtagsDifferingThrowsExceptionWithMessage() {
        $messageCorrect = false;
        $this->entry->setEtag("Foo");
        try {
            $this->entry->transferFromXML($this->entryText);
        } catch (Zend_Gdata_App_IOException $e) {
            if ($e->getMessage() == $this->expectedMismatchExceptionMessage)
                $messageCorrect = true;
        }
        $this->assertTrue($messageCorrect, "Exception Zend_Gdata_IO_Exception message incorrect");
    }

    public function testNothingBadHappensWhenHttpAndXmlEtagsMatch() {
        $this->entry->setEtag($this->expectedEtag);
        $this->entry->transferFromXML($this->entryText);
        $this->assertEquals($this->expectedEtag, $this->entry->getEtag());
    }

    public function testLookUpOpenSearchv1Namespace() {
        $this->assertEquals($this->openSearchNamespacev1,
            $this->entry->lookupNamespace('openSearch', 1, 0));
        $this->assertEquals($this->openSearchNamespacev1,
            $this->entry->lookupNamespace('openSearch', 1, null));
    }

    public function testLookupOpenSearchv2Namespace() {
        $this->assertEquals($this->openSearchNamespacev2,
            $this->entry->lookupNamespace('openSearch', 2, 0));
        $this->assertEquals($this->openSearchNamespacev2,
            $this->entry->lookupNamespace('openSearch', 2, null));
    }

}
