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
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Test\PHPUnit\Db\DataSet;
use Zend\Test\PHPUnit\Db\DataSet;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class DbTableTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadDataSetDelegatesWhereLimitOrderBy()
    {
        $fixtureWhere = "where";
        $fixtureLimit = "limit";
        $fixtureOffset = "offset";
        $fixtureOrderBy = "order";

        $table = $this->getMock('Zend\Db\Table\Table', array(), array(), '', false);
        $table->expects($this->once())
              ->method('fetchAll')
              ->with($fixtureWhere, $fixtureOrderBy, $fixtureLimit, $fixtureOffset)
              ->will($this->returnValue(array()));

        $dataSet = new DataSet\DbTable($table, $fixtureWhere, $fixtureOrderBy, $fixtureLimit, $fixtureOffset);
        $count = $dataSet->getRowCount();
    }

    public function testGetTableMetadata()
    {
        $fixtureTableName = "foo";

        $table = $this->getMock('Zend\Db\Table\Table', array(), array(), '', false);
        $table->expects($this->at(0))
              ->method('info')
              ->with($this->equalTo('name'))
              ->will($this->returnValue($fixtureTableName));
        $table->expects($this->at(1))
              ->method('info')
              ->with($this->equalTo('cols'))
              ->will($this->returnValue( array("foo", "bar") ));
        $table->expects($this->once())
              ->method('fetchAll')
              ->will($this->returnValue(array( array("foo" => 1, "bar" => 2) )));

        $dataSet = new DataSet\DbTable($table);

        $this->assertEquals($fixtureTableName, $dataSet->getTableMetaData()->getTableName());
        $this->assertEquals(array("foo", "bar"), $dataSet->getTableMetaData()->getColumns());
    }

    public function testLoadDataOnlyCalledOnce()
    {
        $table = $this->getMock('Zend\Db\Table\Table', array(), array(), '', false);
        $table->expects($this->once())
              ->method('fetchAll')
              ->will($this->returnValue(array( array("foo" => 1, "bar" => 2) )));

        $dataSet = new DataSet\DbTable($table);
        $dataSet->getRow(0);
        $dataSet->getRow(0);
    }
}
