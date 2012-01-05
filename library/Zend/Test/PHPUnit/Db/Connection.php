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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Test\PHPUnit\Db;

/**
 * Generic Abstraction of Zend_Db Connections in the PHPUnit Database Extension context.
 *
 * @uses       PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
 * @uses       \Zend\Db\Adapter\AbstractAdapter
 * @uses       \Zend\Test\PHPUnit\Db\DataSet\QueryTable
 * @uses       \Zend\Test\PHPUnit\Db\Metadata\Generic
 * @uses       PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
