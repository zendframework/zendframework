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
 * @package    Zend_GData_Spreadsheets
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData;
use Zend\GData\Spreadsheets;

/**
 * @category   Zend
 * @package    Zend_GData_Spreadsheets
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Spreadsheets
 */
class SpreadsheetsOnlineTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!constant('TESTS_ZEND_GDATA_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend_GData online tests are not enabled');
        }
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $this->sprKey = constant('TESTS_ZEND_GDATA_SPREADSHEETS_SPREADSHEETKEY');
        $this->wksId = constant('TESTS_ZEND_GDATA_SPREADSHEETS_WORKSHEETID');
        $service = Spreadsheets::AUTH_SERVICE_NAME;
        $client = \Zend\GData\ClientLogin::getHttpClient($user, $pass, $service);
        $this->gdata = new Spreadsheets($client);
    }

    public function testGetSpreadsheetsAndWorksheetsAndData()
    {
        $spreadsheetCount = 0;

        $spreadsheets = $this->gdata->getSpreadsheets();
        $testedContents = false;
        foreach($spreadsheets as $spreadsheet) {
            $spreadsheetCount++;
            $worksheetCount = 0;
            $this->assertTrue($spreadsheet instanceof Spreadsheets\SpreadsheetEntry, 'not instance of SpreadsheetEntry');
            foreach($spreadsheet->getWorksheets() as $worksheet) {
                $this->assertTrue($worksheet instanceof Spreadsheets\WorksheetEntry, 'not instance of WorksheetEntry');
                $worksheetCount++;
                if ($spreadsheet->getTitle()->getText() == 'PHP Unit Test Sheet') {
                    $testedContents = true;
                    $contentAsCells = $worksheet->getContentsAsCells();
                    $this->assertEquals('a1', $contentAsCells['A1']['value']);
                    $this->assertEquals('new', $contentAsCells['A2']['value']);
                    $this->assertEquals('row', $contentAsCells['B2']['value']);
                    $contentAsRows = $worksheet->getContentsAsRows();
                    $this->assertEquals('new', $contentAsRows[0]['a1']);
                    $this->assertEquals('data', $contentAsRows[0]['c1']);
                    $this->assertEquals('here', $contentAsRows[0]['d1']);
                }
            }
            $this->assertTrue($worksheetCount >= 1, 'didn\'t get >= 1 worksheet');
        }
        $this->assertTrue($spreadsheetCount > 1, 'didn\'t get >1 spreadsheet');
        $this->assertTrue($testedContents, 'didn\'t test the contents of the worksheet');
    }

    public function testGetSpreadsheetFeed()
    {
        $feed = $this->gdata->getSpreadsheetFeed();
        $this->assertTrue($feed instanceof Spreadsheets\SpreadsheetFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Spreadsheets\SpreadsheetEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }

        $query = new Spreadsheets\DocumentQuery();
        $feed = $this->gdata->getSpreadsheetFeed($query);
        $this->assertTrue($feed instanceof Spreadsheets\SpreadsheetFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Spreadsheets\SpreadsheetEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }

        $uri = $query->getQueryUrl();
        $feed = $this->gdata->getSpreadsheetFeed($uri);
        $this->assertTrue($feed instanceof Spreadsheets\SpreadsheetFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Spreadsheets\SpreadsheetEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }
    }

    public function testGetWorksheetFeed()
    {
        $query = new Spreadsheets\DocumentQuery();
        $query->setSpreadsheetKey($this->sprKey);
        $feed = $this->gdata->getWorksheetFeed($query);
        $this->assertTrue($feed instanceof Spreadsheets\WorksheetFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Spreadsheets\WorksheetEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }

        $uri = $query->getQueryUrl();
        $feed = $this->gdata->getWorksheetFeed($uri);
        $this->assertTrue($feed instanceof Spreadsheets\WorksheetFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Spreadsheets\WorksheetEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }
    }

    public function testGetCellFeed()
    {
        $query = new Spreadsheets\CellQuery();
        $query->setSpreadsheetKey($this->sprKey);
        $query->setWorksheetId($this->wksId);
        $feed = $this->gdata->getCellFeed($query);
        $this->assertTrue($feed instanceof Spreadsheets\CellFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Spreadsheets\CellEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }

        $feed = $this->gdata->getCellFeed($query->getQueryUrl());
        $this->assertTrue($feed instanceof Spreadsheets\CellFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Spreadsheets\CellEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }
    }

    public function testGetListFeed()
    {
        $query = new Spreadsheets\ListQuery();
        $query->setSpreadsheetKey($this->sprKey);
        $query->setWorksheetId($this->wksId);
        $feed = $this->gdata->getListFeed($query);
        $this->assertTrue($feed instanceof Spreadsheets\ListFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Spreadsheets\ListEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }

        $feed = $this->gdata->getListFeed($query->getQueryUrl());
        $this->assertTrue($feed instanceof Spreadsheets\ListFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Spreadsheets\ListEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }
    }

    public function testGetSpreadsheetEntry()
    {
        $query = new Spreadsheets\DocumentQuery();
        $query->setSpreadsheetKey($this->sprKey);
        $entry = $this->gdata->getSpreadsheetEntry($query);
        $this->assertTrue($entry instanceof Spreadsheets\SpreadsheetEntry);

        $entry = $this->gdata->getSpreadsheetEntry($query->getQueryUrl());
        $this->assertTrue($entry instanceof Spreadsheets\SpreadsheetEntry);
    }

    public function testGetWorksheetEntry()
    {
        $query = new Spreadsheets\DocumentQuery();
        $query->setSpreadsheetKey($this->sprKey);
        $query->setWorksheetId($this->wksId);
        $entry = $this->gdata->getWorksheetEntry($query);
        $this->assertTrue($entry instanceof Spreadsheets\WorksheetEntry);

        $entry = $this->gdata->getWorksheetEntry($query->getQueryUrl());
        $this->assertTrue($entry instanceof Spreadsheets\WorksheetEntry);
    }

    public function testGetCellEntry()
    {
        $query = new Spreadsheets\CellQuery();
        $query->setSpreadsheetKey($this->sprKey);
        $query->setCellId('R1C1');
        $entry = $this->gdata->getCellEntry($query);
        $this->assertTrue($entry instanceof Spreadsheets\CellEntry);

        $entry = $this->gdata->getCellEntry($query->getQueryUrl());
        $this->assertTrue($entry instanceof Spreadsheets\CellEntry);
    }

    public function testGetListEntry()
    {
        $query = new Spreadsheets\ListQuery();
        $query->setSpreadsheetKey($this->sprKey);
        $query->setStartIndex('1');
        $query->setMaxResults('1');
        $entry = $this->gdata->getListEntry($query);
        $this->assertTrue($entry instanceof Spreadsheets\ListEntry);

        $entry = $this->gdata->getListEntry($query->getQueryUrl());
        $this->assertTrue($entry instanceof Spreadsheets\ListEntry);
    }

    public function testUpdateCell()
    {
        $this->gdata->updateCell(5, 1, 'updated data', $this->sprKey, $this->wksId);

        $query = new Spreadsheets\CellQuery();
        $query->setSpreadsheetKey($this->sprKey);
        $query->setCellId('R5C1');
        $entry = $this->gdata->getCellEntry($query);
        $this->assertTrue($entry instanceof Spreadsheets\CellEntry);
        $this->assertTrue($entry->cell->getText() == 'updated data');

        $this->gdata->updateCell(5, 1, '', $this->sprKey, $this->wksId);
    }

    public function testInsertUpdateDeleteRow()
    {
        $rowData = array();
        $rowData['a1'] = 'new';
        $rowData['b1'] = 'row';
        $rowData['c1'] = 'data';
        $rowData['d1'] = 'here';
        $entry = $this->gdata->insertRow($rowData, $this->sprKey);
        $rowData['a1'] = 'newer';
        $entry = $this->gdata->updateRow($entry, $rowData);
        $this->gdata->deleteRow($entry);
    }

    public function testInsertUpdateDeleteRow2()
    {
        $rowData = array();
        $rowData['a1'] = 'new';
        $rowData['b1'] = 'row';
        $rowData['c1'] = 'data';
        $rowData['d1'] = 'here';
        $entry = $this->gdata->insertRow($rowData, $this->sprKey);
        $rowData['a1'] = 'newer';
        $entry = $this->gdata->updateRow($entry, $rowData);
        $ssTest = new Spreadsheets($entry->getHttpClient());
        $ssTest->delete($entry->getEditLink()->href);
    }

    public function testInsertUpdateDeleteRow3()
    {
        $rowData = array();
        $rowData['a1'] = 'new';
        $rowData['b1'] = 'row';
        $rowData['c1'] = 'data';
        $rowData['d1'] = 'here';
        $entry = $this->gdata->insertRow($rowData, $this->sprKey);
        $rowData['a1'] = 'newer';
        $entry = $this->gdata->updateRow($entry, $rowData);
        $ssTest = new Spreadsheets($entry->getHttpClient());
        $ssTest->delete($entry);
    }

    public function testCustomElementsCollected() {
        $rowData = array();
        $rowData['a1'] = 'new';
        $rowData['b1'] = 'row';
        $rowData['c1'] = 'data';
        $rowData['d1'] = 'here';
        $entry = $this->gdata->insertRow($rowData, $this->sprKey);

        $this->assertEquals(4, count($entry->custom));
        $this->assertEquals(4, count($entry->customByName));

        $this->assertEquals('new', $entry->custom[0]->getText());
        $this->assertEquals('row', $entry->custom[1]->getText());
        $this->assertEquals('data', $entry->custom[2]->getText());
        $this->assertEquals('here', $entry->custom[3]->getText());

        $this->assertEquals('new', $entry->customByName['a1']->getText());
        $this->assertEquals('row', $entry->customByName['b1']->getText());
        $this->assertEquals('data', $entry->customByName['c1']->getText());
        $this->assertEquals('here', $entry->customByName['d1']->getText());

        $ssTest = new Spreadsheets($entry->getHttpClient());
        $ssTest->delete($entry);
    }

}
