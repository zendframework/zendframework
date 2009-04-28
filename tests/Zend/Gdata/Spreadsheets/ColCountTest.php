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
class Zend_Gdata_Spreadsheets_ColCountTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->colCount = new Zend_Gdata_Spreadsheets_Extension_ColCount();
    }

    public function testToAndFromString()
    {
        $this->colCount->setText('20');
        $this->assertTrue($this->colCount->getText() == '20');
        $newColCount = new Zend_Gdata_Spreadsheets_Extension_ColCount();
        $doc = new DOMDocument();
        $doc->loadXML($this->colCount->saveXML());
        $newColCount->transferFromDom($doc->documentElement);
        $this->assertTrue($this->colCount->getText() == $newColCount->getText());
    }

}
