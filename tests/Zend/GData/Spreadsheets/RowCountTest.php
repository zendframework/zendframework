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

use Zend\GData\Spreadsheets\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_Spreadsheets
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_Spreadsheets
 */
class RowCountTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->rowCount = new Extension\RowCount();
    }

    public function testToAndFromString()
    {
        $this->rowCount->setText('20');
        $this->assertTrue($this->rowCount->getText() == '20');
        $newRowCount = new Extension\RowCount();
        $doc = new \DOMDocument();
        $doc->loadXML($this->rowCount->saveXML());
        $newRowCount->transferFromDom($doc->documentElement);
        $this->assertTrue($this->rowCount->getText() == $newRowCount->getText());
    }

}
