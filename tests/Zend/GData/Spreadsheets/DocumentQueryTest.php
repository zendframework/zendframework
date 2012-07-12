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
class DocumentQueryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->docQuery = new \Zend\GData\Spreadsheets\DocumentQuery();
    }

    public function testTitle()
    {
        $this->assertTrue($this->docQuery->getTitle() == null);
        $this->docQuery->setTitle('test title');
        $this->assertTrue($this->docQuery->getTitle() == 'test title');
        $this->assertTrue($this->docQuery->getQueryString() == '?title=test+title');
        $this->docQuery->setTitle(null);
        $this->assertTrue($this->docQuery->getTitle() == null);
    }

    public function testTitleExact()
    {
        $this->assertTrue($this->docQuery->getTitleExact() == null);
        $this->docQuery->setTitleExact('test title');
        $this->assertTrue($this->docQuery->getTitleExact() == 'test title');
        $this->assertTrue($this->docQuery->getQueryString() == '?title-exact=test+title');
        $this->docQuery->setTitleExact(null);
        $this->assertTrue($this->docQuery->getTitleExact() == null);
    }

    public function testWorksheetId()
    {
        $this->assertTrue($this->docQuery->getWorksheetId() == null);
        $this->docQuery->setWorksheetId('123');
        $this->assertTrue($this->docQuery->getWorksheetId() == '123');
    }

    public function testSpreadsheetKey()
    {
        $this->assertTrue($this->docQuery->getSpreadsheetKey() == null);
        $this->docQuery->setSpreadsheetKey('abc');
        $this->assertTrue($this->docQuery->getSpreadsheetKey() == 'abc');
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
