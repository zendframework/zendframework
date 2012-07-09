<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */

namespace Zend\Test\PHPUnit\Db\DataSet;

/**
 * Represent a PHPUnit Database Extension table with Queries using a Zend_Db adapter for assertion against other tables.
 *
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 */
class QueryTable extends \PHPUnit_Extensions_Database_DataSet_QueryTable
{
    /**
     * Creates a new database query table object.
     *
     * @param string $table_name
     * @param string $query
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection
     */
    public function __construct($tableName, $query, \PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection)
    {
        if( !($databaseConnection instanceof \Zend\Test\PHPUnit\Db\Connection) ) {
            throw new \Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException(
            	"Zend\Test\PHPUnit\Db\DataSet\QueryTable only works with Zend\Test\PHPUnit\Db\Connection connections-"
            );
        }
        parent::__construct($tableName, $query, $databaseConnection);
    }

    /**
     * Load data from the database.
     *
     * @return void
     */
    protected function loadData()
    {
        if($this->data === null) {
            $stmt = $this->databaseConnection->getConnection()->query($this->query);
            $this->data = $stmt->fetchAll(\Zend\Db\Db::FETCH_ASSOC);
        }
    }

    /**
     * Create Table Metadata
     */
    protected function createTableMetaData()
    {
        if ($this->tableMetaData === NULL)
        {
            $this->loadData();
            $keys = array();
            if(count($this->data) > 0) {
                $keys = array_keys($this->data[0]);
            }
            $this->tableMetaData = new \PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData(
                $this->tableName, $keys
            );
        }
    }
}
