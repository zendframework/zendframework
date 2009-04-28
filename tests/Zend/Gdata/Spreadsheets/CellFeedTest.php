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

require_once 'Zend/Gdata/Spreadsheets.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Spreadsheets_CellFeedTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->cellFeed = new Zend_Gdata_Spreadsheets_CellFeed(
                file_get_contents('Zend/Gdata/Spreadsheets/_files/TestDataCellFeedSample1.xml', true),
                true);
    }

    public function testToAndFromString()
    {
        $this->assertTrue(count($this->cellFeed->entries) == 1);
        foreach($this->cellFeed->entries as $entry)
        {
            $this->assertTrue($entry instanceof Zend_Gdata_Spreadsheets_CellEntry);
        }
        $this->assertTrue($this->cellFeed->getRowCount() instanceof Zend_Gdata_Spreadsheets_Extension_RowCount);
        $this->assertTrue($this->cellFeed->getRowCount()->getText() == '100');
        $this->assertTrue($this->cellFeed->getColumnCount() instanceof Zend_Gdata_Spreadsheets_Extension_ColCount);
        $this->assertTrue($this->cellFeed->getColumnCount()->getText() == '20');
        
        $newCellFeed = new Zend_Gdata_Spreadsheets_CellFeed();
        $doc = new DOMDocument();
        $doc->loadXML($this->cellFeed->saveXML());
        $newCellFeed->transferFromDom($doc->documentElement);
        
        $this->assertTrue(count($newCellFeed->entries) == 1);
        foreach($newCellFeed->entries as $entry)
        {
            $this->assertTrue($entry instanceof Zend_Gdata_Spreadsheets_CellEntry);
        }
        $this->assertTrue($newCellFeed->getRowCount() instanceof Zend_Gdata_Spreadsheets_Extension_RowCount);
        $this->assertTrue($newCellFeed->getRowCount()->getText() == '100');
        $this->assertTrue($newCellFeed->getColumnCount() instanceof Zend_Gdata_Spreadsheets_Extension_ColCount);
        $this->assertTrue($newCellFeed->getColumnCount()->getText() == '20');
    }
    
    public function testGetSetCounts()
    {
        $newRowCount = new Zend_Gdata_Spreadsheets_Extension_RowCount();
        $newRowCount->setText("20");
        $newColCount = new Zend_Gdata_Spreadsheets_Extension_ColCount();
        $newColCount->setText("50");
        
        $this->cellFeed->setRowCount($newRowCount);
        $this->cellFeed->setColumnCount($newColCount);
        
        $this->assertTrue($this->cellFeed->getRowCount()->getText() == "20");
        $this->assertTrue($this->cellFeed->getColumnCount()->getText() == "50");
    }

}
