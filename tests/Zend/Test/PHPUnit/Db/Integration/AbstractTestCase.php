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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(__FILE__)."/../../../../../TestHelper.php";

require_once "Zend/Db/Table/Abstract.php";

require_once "Zend/Db/Table.php";

require_once "Zend/Test/PHPUnit/Db/DataSet/DbTableDataSet.php";

require_once "PHPUnit/Extensions/Database/DataSet/FlatXmlDataSet.php";

require_once "Zend/Test/PHPUnit/Db/SimpleTester.php";

require_once "Zend/Test/PHPUnit/Db/DataSet/DbRowset.php";

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
abstract class Zend_Test_PHPUnit_Db_Integration_AbstractTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $dbAdapter;

    public function testZendDbTableDataSet()
    {
        $dataSet = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
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

        $dataSet = new Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet();
        $dataSet->addTable($fooTable);

        $xmlDataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(
            dirname(__FILE__)."/_files/sqliteIntegrationFixture.xml"
        );
        $this->assertTrue($xmlDataSet->assertEquals($dataSet));
    }

    /**
     * @return Zend_Test_PHPUnit_Db_Connection
     */
    public function getConnection()
    {
        return new Zend_Test_PHPUnit_Db_Connection($this->dbAdapter, 'foo');
    }

    public function testSimpleTesterSetupAndRowsetEquals()
    {
        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(
            dirname(__FILE__)."/_files/sqliteIntegrationFixture.xml"
        );
        $fooDataTable = $dataSet->getTable("foo");

        $tester = new Zend_Test_PHPUnit_Db_SimpleTester($this->getConnection());
        $tester->setUpDatabase($dataSet);

        $fooTable = $this->createFooTable();
        $rows = $fooTable->fetchAll();

        $this->assertEquals(3, count($rows));

        $rowsetTable = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rows);
        $rowsetTable->assertEquals($fooDataTable);
    }

    /**
     * @return Zend_Test_PHPUnit_Db_TableFoo
     */
    public function createFooTable()
    {
        $table = new Zend_Test_PHPUnit_Db_TableFoo(array('db' => $this->dbAdapter));
        return $table;
    }

    /**
     * @return Zend_Test_PHPUnit_Db_TableBar
     */
    public function createBarTable()
    {
        $table = new Zend_Test_PHPUnit_Db_TableBar(array('db' => $this->dbAdapter));
        return $table;
    }
}

class Zend_Test_PHPUnit_Db_TableFoo extends Zend_Db_Table_Abstract
{
    protected $_name = "foo";

    protected $_primary = "id";
}

class Zend_Test_PHPUnit_Db_TableBar extends Zend_Db_Table_Abstract
{
    protected $_name = "bar";

    protected $_primary = "id";
}
