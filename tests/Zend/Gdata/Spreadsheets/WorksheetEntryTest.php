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
class Zend_Gdata_Spreadsheets_WorksheetEntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->wksEntry = new Zend_Gdata_Spreadsheets_WorksheetEntry();
    }

    public function testToAndFromString()
    {
        $this->wksEntry->setRowCount(new Zend_Gdata_Spreadsheets_Extension_RowCount('20'));
        $this->assertTrue($this->wksEntry->getRowCount()->getText() == '20');
        $this->wksEntry->setColumnCount(new Zend_Gdata_Spreadsheets_Extension_ColCount('40'));
        $this->assertTrue($this->wksEntry->getColumnCount()->getText() == '40');
        $newWksEntry = new Zend_Gdata_Spreadsheets_WorksheetEntry();
        $doc = new DOMDocument();
        $doc->loadXML($this->wksEntry->saveXML());
        $newWksEntry->transferFromDom($doc->documentElement);
        $this->assertTrue($this->wksEntry->getRowCount()->getText() == $newWksEntry->getRowCount()->getText());
        $this->assertTrue($this->wksEntry->getColumnCount()->getText() == $newWksEntry->getColumnCount()->getText());
    }

}
