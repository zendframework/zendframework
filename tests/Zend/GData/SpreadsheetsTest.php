<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData;

/**
 * @category   Zend
 * @package    Zend_GData_Spreadsheets
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_Spreadsheets
 */
class SpreadsheetsTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->gdata = new \Zend\GData\Spreadsheets(new \Zend\Http\Client());
    }

    public function testSpreadsheets()
    {
        $this->assertTrue(true);
    }

}
