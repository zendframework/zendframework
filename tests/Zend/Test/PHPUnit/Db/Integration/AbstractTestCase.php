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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Test\PHPUnit\Db\Integration;
use Zend\Test\PHPUnit\Db\DataSet;
use Zend\Db\Table;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $dbAdapter;

    public function testZendDbTableDataSet()
    {
        $dataSet = new DataSet\DbTableDataSet();
        $dataSet->addTable($this->createFooTable());
        $dataSet->addTable($this->createBarTable());

        $this->assertEquals(
            "foo", $dataSet->getTableMetaData('foo')->getTableName()
        );
        $this->assertEquals(
            "bar", $dataSet->getTableMetaData("bar")->getTableName()
        );

        $this->assertEquals(array("foo", "bar"), $dataSet->getTableNames());
    }

    public function testZendDbTableEqualsXmlDataSet()
    {
        $fooTable = $this->createFooTable();
        $fooTable->insert(array("id" => null, "foo" => "foo", "bar" => "bar", "baz" => "baz"));
        $fooTable->insert(array("id" => null, "foo" => "bar", "bar" => "bar", "baz" => "bar"));
        $fooTable->insert(array("id" => null, "foo" => "baz", "bar" => "baz", "baz" => "baz"));

        $dataSet = new DataSet\DbTableDataSet();
        $dataSet->addTable($fooTable);

        $xmlDataSet = new \PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(
            __DIR__."/_files/sqliteIntegrationFixture.xml"
        );
        $this->assertTrue($xmlDataSet->assertEquals($dataSet));
    }

    /**
     * @return Zend_Test_PHPUnit_Db_Connection
     */
    public function getConnection()
    {
        return new \Zend\Test\PHPUnit\Db\Connection($this->dbAdapter, 'foo');
    }

    public function testSimpleTesterSetupAndRowsetEquals()
    {
        $dataSet = new \PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(
            __DIR__."/_files/sqliteIntegrationFixture.xml"
        );
        $fooDataTable = $dataSet->getTable("foo");

        $tester = new \Zend\Test\PHPUnit\Db\SimpleTester($this->getConnection());
        $tester->setUpDatabase($dataSet);

        $fooTable = $this->createFooTable();
        $rows = $fooTable->fetchAll();

        $this->assertEquals(3, count($rows));

        $rowsetTable = new DataSet\DbRowset($rows);
        $rowsetTable->assertEquals($fooDataTable);
    }

    /**
     * @return Zend_Test_PHPUnit_Db_TableFoo
     */
    public function createFooTable()
    {
        $table = new TableFoo(array('db' => $this->dbAdapter));
        return $table;
    }

    /**
     * @return Zend_Test_PHPUnit_Db_TableBar
     */
    public function createBarTable()
    {
        $table = new TableBar(array('db' => $this->dbAdapter));
        return $table;
    }
}

class TableFoo extends Table\AbstractTable
{
    protected $_name = "foo";

    protected $_primary = "id";
}

class TableBar extends Table\AbstractTable
{
    protected $_name = "bar";

    protected $_primary = "id";
}
