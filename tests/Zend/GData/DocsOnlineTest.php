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

/**
 * @namespace
 */
namespace ZendTest\GData;
use Zend\GData\Docs;

/**
 * @category   Zend
 * @package    Zend_GData_Docs
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Docs
 */
class DocsOnlineTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!constant('TESTS_ZEND_GDATA_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend_GData online tests are not enabled');
        }
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $this->docTitle = constant('TESTS_ZEND_GDATA_DOCS_DOCUMENTTITLE');
        $service = Docs::AUTH_SERVICE_NAME;
        $client = \Zend\GData\ClientLogin::getHttpClient($user, $pass, $service);
        $this->gdata = new Docs($client);
    }

    public function testGetSpreadsheetFeed()
    {
        $feed = $this->gdata->getDocumentListFeed();
        $this->assertTrue($feed instanceof Docs\DocumentListFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Docs\DocumentListEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }

        $query = new Docs\Query();
        $feed = $this->gdata->getDocumentListFeed($query);
        $this->assertTrue($feed instanceof Docs\DocumentListFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Docs\DocumentListEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }

        $uri = $query->getQueryUrl();
        $feed = $this->gdata->getDocumentListFeed($uri);
        $this->assertTrue($feed instanceof Docs\DocumentListFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Docs\DocumentListEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }
    }

    public function testQueryForTitle()
    {
        $query = new Docs\Query();
        $query->title = $this->docTitle;
        $feed = $this->gdata->getDocumentListFeed($query);
        $this->assertTrue(strpos(strtolower($feed->entries[0]->title), strtolower($this->docTitle)) !== FALSE);
    }

    public function testGetDocumentListEntry()
    {
        $query = new Docs\Query();
        $feed = $this->gdata->getDocumentListFeed($query);
        $selfLinkHref = $feed->entries[0]->getSelfLink()->href;
        $entry = $this->gdata->getDocumentListEntry($selfLinkHref);
        $this->assertTrue($entry instanceof Docs\DocumentListEntry);
    }

    public function testUploadFindAndDelete()
    {
        $documentTitle = 'spreadsheet_upload_test.csv';
        $newDocumentEntry = $this->gdata->uploadFile(
            'Zend/GData/_files/DocsTest.csv', $documentTitle,
            $this->gdata->lookupMimeType('CSV'),
            Docs::DOCUMENTS_LIST_FEED_URI);
        $this->assertTrue($newDocumentEntry->title->text === $documentTitle);

        // Get the newly created document.
        // First extract the document's ID key from the Atom id.
        $idParts = explode('/', $newDocumentEntry->id->text);
        $keyParts = explode('%3A', end($idParts));
        $documentFromGetDoc = $this->gdata->getDoc($keyParts[1], $keyParts[0]);
        $this->assertTrue($documentFromGetDoc->title->text === $documentTitle);
        if ($keyParts[0] == 'document') {
            $documentFromGetDocument = $this->gdata->getDocument($keyParts[1]);
            $this->assertTrue(
                $documentFromGetDocument->title->text === $documentTitle);
        }
        if ($keyParts[0] == 'spreadsheet') {
            $documentFromGetSpreadsheet = $this->gdata->getSpreadsheet(
                $keyParts[1]);
            $this->assertTrue(
                $documentFromGetSpreadsheet->title->text === $documentTitle);
        }
        if ($keyParts[0] == 'presentation') {
            $documentFromGetPresentation = $this->gdata->getPresentation(
                $keyParts[1]);
            $this->assertTrue(
                $documentFromGetPresentation->title->text === $documentTitle);
        }

        // Cleanup and remove the new document.
        $newDocumentEntry->delete();
    }

}
