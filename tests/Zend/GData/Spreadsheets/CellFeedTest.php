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
namespace ZendTest\GData\Spreadsheets;
use Zend\GData\Spreadsheets;
use Zend\GData\Spreadsheets\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_Spreadsheets
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Spreadsheets
 */
class CellFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->cellFeed = new Spreadsheets\CellFeed(
                file_get_contents('Zend/GData/Spreadsheets/_files/TestDataCellFeedSample1.xml', true),
                true);
    }

    public function testToAndFromString()
    {
        $this->assertTrue(count($this->cellFeed->entries) == 1);
        foreach($this->cellFeed->entries as $entry)
        {
            $this->assertTrue($entry instanceof Spreadsheets\CellEntry);
        }
        $this->assertTrue($this->cellFeed->getRowCount() instanceof Extension\RowCount);
        $this->assertTrue($this->cellFeed->getRowCount()->getText() == '100');
        $this->assertTrue($this->cellFeed->getColumnCount() instanceof Extension\ColCount);
        $this->assertTrue($this->cellFeed->getColumnCount()->getText() == '20');

        $newCellFeed = new Spreadsheets\CellFeed();
        $doc = new \DOMDocument();
        $doc->loadXML($this->cellFeed->saveXML());
        $newCellFeed->transferFromDom($doc->documentElement);

        $this->assertTrue(count($newCellFeed->entries) == 1);
        foreach($newCellFeed->entries as $entry)
        {
            $this->assertTrue($entry instanceof Spreadsheets\CellEntry);
        }
        $this->assertTrue($newCellFeed->getRowCount() instanceof Extension\RowCount);
        $this->assertTrue($newCellFeed->getRowCount()->getText() == '100');
        $this->assertTrue($newCellFeed->getColumnCount() instanceof Extension\ColCount);
        $this->assertTrue($newCellFeed->getColumnCount()->getText() == '20');
    }

    public function testGetSetCounts()
    {
        $newRowCount = new Extension\RowCount();
        $newRowCount->setText("20");
        $newColCount = new Extension\ColCount();
        $newColCount->setText("50");

        $this->cellFeed->setRowCount($newRowCount);
        $this->cellFeed->setColumnCount($newColCount);

        $this->assertTrue($this->cellFeed->getRowCount()->getText() == "20");
        $this->assertTrue($this->cellFeed->getColumnCount()->getText() == "50");
    }

}
