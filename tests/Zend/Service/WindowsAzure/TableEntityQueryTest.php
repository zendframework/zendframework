<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service_WindowsAzure
 */

namespace ZendTest\Service\WindowsAzure;

use Zend\Service\WindowsAzure\Storage\TableEntityQuery;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 */
class TableEntityQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test all records query
     */
    public function testAllRecordsQuery()
    {
        $target = new TableEntityQuery();
        $target->select()
               ->from('MyTable');

        $this->assertEquals('MyTable()', $target->__toString());
    }

    /**
     * Test partition key query
     */
    public function testPartitionKeyQuery()
    {
        $target = new TableEntityQuery();
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
        $target = new TableEntityQuery();
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
        $target = new TableEntityQuery();
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
        $target = new TableEntityQuery();
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
        $target = new TableEntityQuery();
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
        $target = new TableEntityQuery();
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
        $target = new TableEntityQuery();
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
        $target = new TableEntityQuery();
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
        $target = new TableEntityQuery();
        $target->select()
               ->from('MyTable')
               ->where('Name eq ?', 'Maarten')
               ->andWhere('Visible eq true');

        $this->assertEquals('MyTable()?$filter=Name eq \'Maarten\' and Visible eq true', $target->__toString());
    }
}
