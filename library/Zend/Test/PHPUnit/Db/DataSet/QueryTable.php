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
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Test\PHPUnit\Db\DataSet;

/**
 * Represent a PHPUnit Database Extension table with Queries using a Zend_Db adapter for assertion against other tables.
 *
 * @uses       PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData
 * @uses       PHPUnit_Extensions_Database_DataSet_QueryTable
 * @uses       PHPUnit_Extensions_Database_DB_IDatabaseConnection
 * @uses       \Zend\Db\Db
 * @uses       \Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
