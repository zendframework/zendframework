<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */

namespace Zend\Test\PHPUnit\Db;

/**
 * Generic Abstraction of Zend_Db Connections in the PHPUnit Database Extension context.
 *
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 */
class Connection extends \PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
{
    /**
     * Zend_Db_Adapter_Abstract
     *
     * @var \Zend\Db\Adapter\AbstractAdapter
     */
    protected $_connection;

    /**
     * Database Schema
     *
     * @var string $db
     */
    protected $_schema;

    /**
     * Metadata
     *
     * @param PHPUnit_Extensions_Database_DB_IMetaData $db
     */
    protected $_metaData;

    /**
     * Construct Connection based on Zend_Db_Adapter_Abstract
     *
     * @param \Zend\Db\Adapter\AbstractAdapter $db
     * @param string $schema
     */
    public function __construct(\Zend\Db\Adapter\AbstractAdapter $db, $schema)
    {
        $this->_connection = $db;
        $this->_schema = $schema;
    }

    /**
     * Close this connection.
     *
     * @return void
     */
    public function close()
    {
        $this->_connection->closeConnection();
    }

    /**
     * Creates a table with the result of the specified SQL statement.
     *
     * @param string $resultName
     * @param string $sql
     * @return PHPUnit_Extensions_Database_DataSet_ITable
     */
    public function createQueryTable($resultName, $sql)
    {
        return new DataSet\QueryTable($resultName, $sql, $this);
    }

    /**
     * Returns a Zend_Db Connection
     *
     * @return \Zend\Db\Adapter\AbstractAdapter
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Returns a database metadata object that can be used to retrieve table
     * meta data from the database.
     *
     * @return PHPUnit_Extensions_Database_DB_IMetaData
     */
    public function getMetaData()
    {
        if($this->_metaData === null) {
            $this->_metaData = new Metadata\Generic($this->getConnection(), $this->getSchema());
        }
        return $this->_metaData;
    }

    /**
     * Returns the schema for the connection.
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->_schema;
    }

    /**
     * Returns the command used to truncate a table.
     *
     * @return string
     */
    public function getTruncateCommand()
    {
        return "DELETE";
    }
}
