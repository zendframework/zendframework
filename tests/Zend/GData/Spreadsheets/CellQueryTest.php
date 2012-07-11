<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\Spreadsheets;

/**
 * @category   Zend
 * @package    Zend_GData_Spreadsheets
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_Spreadsheets
 */
class CellQueryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->docQuery = new \Zend\GData\Spreadsheets\CellQuery();
    }

    public function testMinRow()
    {
        $this->assertTrue($this->docQuery->getMinRow() == null);
        $this->docQuery->setMinRow('1');
        $this->assertTrue($this->docQuery->getMinRow() == '1');
        $this->assertTrue($this->docQuery->getQueryString() == '?min-row=1');
        $this->docQuery->setMinRow(null);
        $this->assertTrue($this->docQuery->getMinRow() == null);
    }

    public function testMaxRow()
    {
        $this->assertTrue($this->docQuery->getMaxRow() == null);
        $this->docQuery->setMaxRow('2');
        $this->assertTrue($this->docQuery->getMaxRow() == '2');
        $this->assertTrue($this->docQuery->getQueryString() == '?max-row=2');
        $this->docQuery->setMaxRow(null);
        $this->assertTrue($this->docQuery->getMaxRow() == null);
    }

    public function testMinCol()
    {
        $this->assertTrue($this->docQuery->getMinCol() == null);
        $this->docQuery->setMinCol('3');
        $this->assertTrue($this->docQuery->getMinCol() == '3');
        $this->assertTrue($this->docQuery->getQueryString() == '?min-col=3');
        $this->docQuery->setMinCol(null);
        $this->assertTrue($this->docQuery->getMinCol() == null);
    }

    public function testMaxCol()
    {
        $this->assertTrue($this->docQuery->getMaxCol() == null);
        $this->docQuery->setMaxCol('4');
        $this->assertTrue($this->docQuery->getMaxCol() == '4');
        $this->assertTrue($this->docQuery->getQueryString() == '?max-col=4');
        $this->docQuery->setMaxCol(null);
        $this->assertTrue($this->docQuery->getMaxCol() == null);
    }

    public function testRange()
    {
        $this->assertTrue($this->docQuery->getRange() == null);
        $this->docQuery->setRange('A1:B4');
        $this->assertTrue($this->docQuery->getRange() == 'A1:B4');
        $this->assertTrue($this->docQuery->getQueryString() == '?range=A1%3AB4');
        $this->docQuery->setRange(null);
        $this->assertTrue($this->docQuery->getRange() == null);
    }

    public function testReturnEmpty()
    {
        $this->assertTrue($this->docQuery->getReturnEmpty() == null);
        $this->docQuery->setReturnEmpty('false');
        $this->assertTrue($this->docQuery->getReturnEmpty() == 'false');
        $this->assertTrue($this->docQuery->getQueryString() == '?return-empty=false');
        $this->docQuery->setReturnEmpty(null);
        $this->assertTrue($this->docQuery->getReturnEmpty() == null);
    }

    public function testWorksheetId()
    {
        $this->assertTrue($this->docQuery->getWorksheetId() == 'default');
        $this->docQuery->setWorksheetId('123');
        $this->assertTrue($this->docQuery->getWorksheetId() == '123');
    }

    public function testSpreadsheetKey()
    {
        $this->assertTrue($this->docQuery->getSpreadsheetKey() == null);
        $this->docQuery->setSpreadsheetKey('abc');
        $this->assertTrue($this->docQuery->getSpreadsheetKey() == 'abc');
    }

    public function testCellId()
    {
        $this->assertTrue($this->docQuery->getCellId() == null);
        $this->docQuery->setCellId('xyz');
        $this->assertTrue($this->docQuery->getCellId() == 'xyz');
    }

    public function testProjection()
    {
        $this->assertTrue($this->docQuery->getProjection() == 'full');
        $this->docQuery->setProjection('abc');
        $this->assertTrue($this->docQuery->getProjection() == 'abc');
    }

    public function testVisibility()
    {
        $this->assertTrue($this->docQuery->getVisibility() == 'private');
        $this->docQuery->setVisibility('xyz');
        $this->assertTrue($this->docQuery->getVisibility() == 'xyz');
    }

}
