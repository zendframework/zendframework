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
class Zend_Gdata_Spreadsheets_CellTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->cell = new Zend_Gdata_Spreadsheets_Extension_Cell();
    }

    public function testToAndFromString()
    {
        $this->cell->setText('test cell');
        $this->assertTrue($this->cell->getText() == 'test cell');
        $this->cell->setRow('1');
        $this->assertTrue($this->cell->getRow() == '1');
        $this->cell->setColumn('2');
        $this->assertTrue($this->cell->getColumn() == '2');
        $this->cell->setInputValue('test input value');
        $this->assertTrue($this->cell->getInputValue() == 'test input value');
        $this->cell->setNumericValue('test numeric value');
        $this->assertTrue($this->cell->getNumericValue() == 'test numeric value');
        
        $newCell = new Zend_Gdata_Spreadsheets_Extension_Cell();
        $doc = new DOMDocument();
        $doc->loadXML($this->cell->saveXML());
        $newCell->transferFromDom($doc->documentElement);
        $this->assertTrue($this->cell->getText() == $newCell->getText());
        $this->assertTrue($this->cell->getRow() == $newCell->getRow());
        $this->assertTrue($this->cell->getColumn() == $newCell->getColumn());
        $this->assertTrue($this->cell->getInputValue() == $newCell->getInputValue());
        $this->assertTrue($this->cell->getNumericValue() == $newCell->getNumericValue());
    }

}
