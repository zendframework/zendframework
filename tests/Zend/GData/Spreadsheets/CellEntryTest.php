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

use Zend\GData\Spreadsheets;

/**
 * @category   Zend
 * @package    Zend_GData_Spreadsheets
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_Spreadsheets
 */
class CellEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->cellEntry = new Spreadsheets\CellEntry();
    }

    public function testToAndFromString()
    {
        $this->cellEntry->setCell(new \Zend\GData\Spreadsheets\Extension\Cell('my cell', '1', '2', 'input value', 'numeric value'));
        $this->assertTrue($this->cellEntry->getCell()->getText() == 'my cell');
        $this->assertTrue($this->cellEntry->getCell()->getRow() == '1');
        $this->assertTrue($this->cellEntry->getCell()->getColumn() == '2');
        $this->assertTrue($this->cellEntry->getCell()->getInputValue() == 'input value');
        $this->assertTrue($this->cellEntry->getCell()->getNumericValue() == 'numeric value');

        $newCellEntry = new Spreadsheets\CellEntry();
        $doc = new \DOMDocument();
        $doc->loadXML($this->cellEntry->saveXML());
        $newCellEntry->transferFromDom($doc->documentElement);

        $this->assertTrue($this->cellEntry->getCell()->getText() == $newCellEntry->getCell()->getText());
        $this->assertTrue($this->cellEntry->getCell()->getRow() == $newCellEntry->getCell()->getRow());
        $this->assertTrue($this->cellEntry->getCell()->getColumn() == $newCellEntry->getCell()->getColumn());
        $this->assertTrue($this->cellEntry->getCell()->getInputValue() == $newCellEntry->getCell()->getInputValue());
        $this->assertTrue($this->cellEntry->getCell()->getNumericValue() == $newCellEntry->getCell()->getNumericValue());
    }

}
