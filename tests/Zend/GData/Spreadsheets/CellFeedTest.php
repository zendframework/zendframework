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

    public $cellFeed;

    public function setUp()
    {
        $this->cellFeed = new Spreadsheets\CellFeed(
                file_get_contents('Zend/GData/Spreadsheets/_files/TestDataCellFeedSample1.xml', true),
                true);
    }

    public function testToAndFromString()
    {
        $this->assertEquals(2, count($this->cellFeed->entries));
        $this->assertEquals(2, $this->cellFeed->entries->count());
        foreach($this->cellFeed->entries as $entry)
        {
            $this->assertInstanceOf('Zend\GData\Spreadsheets\CellEntry', $entry);
        }
        $this->assertInstanceOf('Zend\GData\Spreadsheets\Extension\RowCount',
                                $this->cellFeed->getRowCount());
        $this->assertTrue($this->cellFeed->getRowCount()->getText() == '100');
        $this->assertInstanceOf('Zend\GData\Spreadsheets\Extension\ColCount',
                                $this->cellFeed->getColumnCount());
        $this->assertTrue($this->cellFeed->getColumnCount()->getText() == '20');

        $newCellFeed = new Spreadsheets\CellFeed();
        $doc = new \DOMDocument();
        $doc->loadXML($this->cellFeed->saveXML());
        $newCellFeed->transferFromDom($doc->documentElement);

        $this->assertEquals(2, count($newCellFeed->entries));
        $this->assertEquals(2, $newCellFeed->entries->count());
        foreach($newCellFeed->entries as $entry)
        {
            $this->assertInstanceOf('Zend\GData\Spreadsheets\CellEntry', $entry);
        }
        $this->assertInstanceOf('Zend\GData\Spreadsheets\Extension\RowCount',
                                $newCellFeed->getRowCount());
        $this->assertTrue($newCellFeed->getRowCount()->getText() == '100');
        $this->assertInstanceOf('Zend\GData\Spreadsheets\Extension\ColCount',
                                $newCellFeed->getColumnCount());
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
