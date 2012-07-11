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
class WorksheetEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->wksEntry = new Spreadsheets\WorksheetEntry();
    }

    public function testToAndFromString()
    {
        $this->wksEntry->setRowCount(new \Zend\GData\Spreadsheets\Extension\RowCount('20'));
        $this->assertTrue($this->wksEntry->getRowCount()->getText() == '20');
        $this->wksEntry->setColumnCount(new \Zend\GData\Spreadsheets\Extension\ColCount('40'));
        $this->assertTrue($this->wksEntry->getColumnCount()->getText() == '40');
        $newWksEntry = new Spreadsheets\WorksheetEntry();
        $doc = new \DOMDocument();
        $doc->loadXML($this->wksEntry->saveXML());
        $newWksEntry->transferFromDom($doc->documentElement);
        $this->assertTrue($this->wksEntry->getRowCount()->getText() == $newWksEntry->getRowCount()->getText());
        $this->assertTrue($this->wksEntry->getColumnCount()->getText() == $newWksEntry->getColumnCount()->getText());
    }

}
