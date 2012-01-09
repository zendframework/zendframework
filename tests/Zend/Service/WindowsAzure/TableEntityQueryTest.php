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
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Service_WindowsAzure_Storage_TableEntityQuery
 */

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_WindowsAzure_TableEntityQueryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test all records query
     */
    public function testAllRecordsQuery()
    {
        $target = new Zend_Service_WindowsAzure_Storage_TableEntityQuery();
        $target->select()
               ->from('MyTable');
               
        $this->assertEquals('MyTable()', $target->__toString());
    }
    
    /**
     * Test partition key query
     */
    public function testPartitionKeyQuery()
    {
        $target = new Zend_Service_WindowsAzure_Storage_TableEntityQuery();
        $target->select()
               ->from('MyTable')
               ->wherePartitionKey('test');
               
        $this->assertEquals('MyTable(PartitionKey=\'test\')', $target->__toString());
    }
    
    /**
     * Test row key query
     */
    public function testRowKeyQuery()
    {
        $target = new Zend_Service_WindowsAzure_Storage_TableEntityQuery();
        $target->select()
               ->from('MyTable')
               ->whereRowKey('test');
               
        $this->assertEquals('MyTable(RowKey=\'test\')', $target->__toString());
    }
    
    /**
     * Test identifier query
     */
    public function testIdentifierQuery()
    {
        $target = new Zend_Service_WindowsAzure_Storage_TableEntityQuery();
        $target->select()
               ->from('MyTable')
               ->wherePartitionKey('test')
               ->whereRowKey('123');
               
        $this->assertEquals('MyTable(PartitionKey=\'test\', RowKey=\'123\')', $target->__toString());
    }
    
    /**
     * Test top records query
     */
    public function testTopQuery()
    {
        $target = new Zend_Service_WindowsAzure_Storage_TableEntityQuery();
        $target->select()
               ->from('MyTable')
               ->top(10);
               
        $this->assertEquals('MyTable()?$top=10', $target->__toString());
    }
    
    /**
     * Test order by query
     */
    public function testOrderByQuery()
    {
        $target = new Zend_Service_WindowsAzure_Storage_TableEntityQuery();
        $target->select()
               ->from('MyTable')
               ->orderBy('Name', 'asc');
               
        $this->assertEquals('MyTable()?$orderby=Name asc', $target->__toString());
    }
    
    /**
     * Test order by multiple query
     */
    public function testOrderByMultipleQuery()
    {
        $target = new Zend_Service_WindowsAzure_Storage_TableEntityQuery();
        $target->select()
               ->from('MyTable')
               ->orderBy('Name', 'asc')
               ->orderBy('Visible', 'desc');
               
        $this->assertEquals('MyTable()?$orderby=Name asc,Visible desc', $target->__toString());
    }
    
    /**
     * Test where query
     */
    public function testWhereQuery()
    {
        $target = new Zend_Service_WindowsAzure_Storage_TableEntityQuery();
        $target->select()
               ->from('MyTable')
               ->where('Name eq ?', 'Maarten');
               
        $this->assertEquals('MyTable()?$filter=Name eq \'Maarten\'', $target->__toString());
    }
    
    /**
     * Test where array query
     */
    public function testWhereArrayQuery()
    {
        $target = new Zend_Service_WindowsAzure_Storage_TableEntityQuery();
        $target->select()
               ->from('MyTable')
               ->where('Name eq ? or Name eq ?', array('Maarten', 'Vijay'));
               
        $this->assertEquals('MyTable()?$filter=Name eq \'Maarten\' or Name eq \'Vijay\'', $target->__toString());
    }
    
    /**
     * Test where multiple query
     */
    public function testWhereMultipleQuery()
    {
        $target = new Zend_Service_WindowsAzure_Storage_TableEntityQuery();
        $target->select()
               ->from('MyTable')
               ->where('Name eq ?', 'Maarten')
               ->andWhere('Visible eq true');
               
        $this->assertEquals('MyTable()?$filter=Name eq \'Maarten\' and Visible eq true', $target->__toString());
    }
}
