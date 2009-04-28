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
class Zend_Gdata_Spreadsheets_DocumentQueryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->docQuery = new Zend_Gdata_Spreadsheets_DocumentQuery();
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
