<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */

namespace ZendTest\Test\PHPUnit\Db\DataSet;

use Zend\Test\PHPUnit\Db\DataSet;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @group      Zend_Test
 */
class DbTableDataSetTest extends \PHPUnit_Framework_TestCase
{
    public function testAddTableAppendedToTableNames()
    {
        $fixtureTable = "foo";

        $table = $this->getMock('Zend\Db\Table\Table', array(), array(), '', false);
        $table->expects($this->at(0))->method('info')->with('name')->will($this->returnValue($fixtureTable));
        $table->expects($this->at(1))->method('info')->with('name')->will($this->returnValue($fixtureTable));
        $table->expects($this->at(2))->method('info')->with('cols')->will($this->returnValue(array()));

        $dataSet = new DataSet\DbTableDataSet();
        $dataSet->addTable($table);

        $this->assertEquals(array($fixtureTable), $dataSet->getTableNames());
    }

    public function testAddTableCreatesDbTableInstance()
    {
        $fixtureTable = "foo";

        $table = $this->getMock('Zend\Db\Table\Table', array(), array(), '', false);
        $table->expects($this->at(0))->method('info')->with('name')->will($this->returnValue($fixtureTable));
        $table->expects($this->at(1))->method('info')->with('name')->will($this->returnValue($fixtureTable));
        $table->expects($this->at(2))->method('info')->with('cols')->will($this->returnValue(array()));

        $dataSet = new DataSet\DbTableDataSet();
        $dataSet->addTable($table);

        $this->assertInstanceOf('Zend\Test\PHPUnit\Db\DataSet\DbTable', $dataSet->getTable($fixtureTable));
    }

    public function testGetUnknownTableThrowsException()
    {
        $this->setExpectedException('\Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException');
        $dataSet = new DataSet\DbTableDataSet();
        $dataSet->getTable('unknown');
    }
}
