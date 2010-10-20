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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Test\PHPUnit\Db\DataSet;
use Zend\Test\PHPUnit\Db\DataSet;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

        $this->assertType('Zend\Test\PHPUnit\Db\DataSet\DbTable', $dataSet->getTable($fixtureTable));
    }

    public function testGetUnknownTableThrowsException()
    {
        $this->setExpectedException('\Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException');
        $dataSet = new DataSet\DbTableDataSet();
        $dataSet->getTable('unknown');
    }
}
