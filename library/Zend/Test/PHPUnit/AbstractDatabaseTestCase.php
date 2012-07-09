<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */

namespace Zend\Test\PHPUnit;

/**
 * Generic Testcase for Zend Framework related DbUnit Testing with PHPUnit
 *
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 */
abstract class AbstractDatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * Creates a new Zend Database Connection using the given Adapter and database schema name.
     *
     * @param  \Zend\Db\Adapter\AbstractAdapter $connection
     * @param  string $schema
     * @return \Zend\Test\PHPUnit\Db\Connection
     */
    protected function createZendDbConnection(\Zend\Db\Adapter\AbstractAdapter $connection, $schema)
    {
        return new Db\Connection($connection, $schema);
    }

    /**
     * Convenience function to get access to the database connection.
     *
     * @return \Zend\Db\Adapter\AbstractAdapter
     */
    protected function getAdapter()
    {
        return $this->getConnection()->getConnection();
    }

    /**
     * Returns the database operation executed in test setup.
     *
     * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
     */
    protected function getSetUpOperation()
    {
        return new \PHPUnit_Extensions_Database_Operation_Composite(array(
            new Db\Operation\Truncate(),
            new Db\Operation\Insert(),
        ));
    }

    /**
     * Returns the database operation executed in test cleanup.
     *
     * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
     */
    protected function getTearDownOperation()
    {
        return \PHPUnit_Extensions_Database_Operation_Factory::NONE();
    }

    /**
     * Create a dataset based on multiple Zend_Db_Table instances
     *
     * @param  array $tables
     * @return \Zend\Test\PHPUnit\Db\DataSet\DbTableDataSet
     */
    protected function createDbTableDataSet(array $tables=array())
    {
        $dataSet = new Db\DataSet\DbTableDataSet();
        foreach($tables AS $table) {
            $dataSet->addTable($table);
        }
        return $dataSet;
    }

    /**
     * Create a table based on one Zend_Db_Table instance
     *
     * @param \Zend\Db\Table\AbstractTable $table
     * @param string $where
     * @param string $order
     * @param string $count
     * @param string $offset
     * @return \Zend\Test\PHPUnit\Db\DataSet\DbTable
     */
    protected function createDbTable(\Zend\Db\Table\AbstractTable $table, $where=null, $order=null, $count=null, $offset=null)
    {
        return new Db\DataSet\DbTable($table, $where, $order, $count, $offset);
    }

    /**
     * Create a data table based on a Zend_Db_Table_Rowset instance
     *
     * @param  \Zend\Db\Table\AbstractRowset $rowset
     * @param  string
     * @return \Zend\Test\PHPUnit\Db\DataSet\DbRowset
     */
    protected function createDbRowset(\Zend\Db\Table\AbstractRowset $rowset, $tableName = null)
    {
        return new Db\DataSet\DbRowset($rowset, $tableName);
    }
}
