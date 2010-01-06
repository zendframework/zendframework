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
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * PHPUnit_Util_Filter
 */
require_once 'PHPUnit/Util/Filter.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @see Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Abstract.php';


/**
 * @see Zend_Db_Statement_Static
 */
require_once 'Zend/Db/Statement/Static.php';


/**
 * Class for connecting to SQL databases and performing common operations.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Static extends Zend_Db_Adapter_Abstract
{
    public $config = null;

    /**
     * The number of seconds to sleep upon query execution
     *
     * @var integer
     */
    protected $_onQuerySleep = 0;

    /**
     * Sets the number of seconds to sleep upon query execution
     *
     * @param  integer $seconds
     * @return Zend_Db_Adapter_Static Provides a fluent interface
     */
    public function setOnQuerySleep($seconds = 0)
    {
        $this->_onQuerySleep = (integer) $seconds;

        return $this;
    }

    /**
     * Returns the number of seconds to sleep upon query execution
     *
     * @return integer
     */
    public function getOnQuerySleep()
    {
        return $this->_onQuerySleep;
    }

    /**
     * Check for config options that are mandatory.
     * Throw exceptions if any are missing.
     *
     * @param array $config
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _checkRequiredOptions(array $config)
    {
        // we need at least a dbname
        if (! array_key_exists('dbname', $config)) {
            require_once 'Zend/Db/Adapter/Exception.php';
            throw new Zend_Db_Adapter_Exception("Configuration must have a key for 'dbname' that names the database instance");
        }
        $this->config = $config;
    }

    /**
     * Prepares and executes a SQL statement with bound data.
     *
     * @param  string|Zend_Db_Select $sql  The SQL statement with placeholders.
     * @param  mixed                 $bind An array of data to bind to the placeholders.
     * @return Zend_Db_Statement (may also be PDOStatement in the case of PDO)
     */
    public function query($sql, $bind = array())
    {
        // connect to the database if needed
        $this->_connect();

        // is the $sql a Zend_Db_Select object?
        if ($sql instanceof Zend_Db_Select) {
            $sql = $sql->__toString();
        }

        // make sure $bind to an array;
        // don't use (array) typecasting because
        // because $bind may be a Zend_Db_Expr object
        if (!is_array($bind)) {
            $bind = array($bind);
        }

        // prepare and execute the statement with profiling
        $stmt = $this->prepare($sql);
        $q = $this->_profiler->queryStart($sql);
        if ($this->_onQuerySleep > 0) {
            sleep($this->_onQuerySleep);
        }
        $stmt->execute($bind);
        $this->_profiler->queryEnd($q);

        // return the results embedded in the prepared statement object
        $stmt->setFetchMode($this->_fetchMode);
        return $stmt;
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        return array('dummy');
    }

    /**
     * Returns the column descriptions for a table.
     *
     * The return value is an associative array keyed by the column name,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string;
     * COLUMN_NAME      => string; column name
     * COLUMN_POSITION  => number; ordinal position of column in table
     * DATA_TYPE        => string; SQL datatype name of column
     * DEFAULT          => string; default expression of column, null if none
     * NULLABLE         => boolean; true if column can have nulls
     * LENGTH           => number; length of CHAR/VARCHAR
     * SCALE            => number; scale of NUMERIC/DECIMAL
     * PRECISION        => number; precision of NUMERIC/DECIMAL
     * UNSIGNED         => boolean; unsigned property of an integer type
     * PRIMARY          => boolean; true if column is part of the primary key
     * PRIMARY_POSITION => integer; position of column in primary key
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        return array(
            'SCHEMA_NAME'      => $schemaName,
            'TABLE_NAME'       => $tableName,
            'COLUMN_NAME'      => null,
            'COLUMN_POSITION'  => null,
            'DATA_TYPE'        => null,
            'DEFAULT'          => null,
            'NULLABLE'         => null,
            'LENGTH'           => null,
            'SCALE'            => null,
            'PRECISION'        => null,
            'UNSIGNED'         => null,
            'PRIMARY'          => null,
            'PRIMARY_POSITION' => null,
        );
    }

    /**
     * Creates a connection to the database.
     *
     * @return void
     */
    protected function _connect()
    {
        $this->_connection = $this;
        return;
    }

    /**
     * Test if a connection is active
     *
     * @return boolean
     */
    public function isConnected()
    {
        return ((bool) (!is_null($this->_connection)));
    }

    /**
     * Force the connection to close.
     *
     * @return void
     */
    public function closeConnection()
    {
        $this->_connection = null;
    }

    /**
     * Prepare a statement and return a PDOStatement-like object.
     *
     * @param  string|Zend_Db_Select $sql SQL query
     * @return Zend_Db_Statment_Static
     */
    public function prepare($sql)
    {
        return new Zend_Db_Statement_Static();
    }

    /**
     * Gets the last ID generated automatically by an IDENTITY/AUTOINCREMENT column.
     *
     * As a convention, on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2), this method forms the name of a sequence
     * from the arguments and returns the last id generated by that sequence.
     * On RDBMS brands that support IDENTITY/AUTOINCREMENT columns, this method
     * returns the last value generated for such a column, and the table name
     * argument is disregarded.
     *
     * @param string $tableName   OPTIONAL Name of table.
     * @param string $primaryKey  OPTIONAL Name of primary key column.
     * @return integer
     */
    public function lastInsertId($tableName = null, $primaryKey = 'id')
    {
        return null;
    }

    /**
     * Begin a transaction.
     */
    protected function _beginTransaction()
    {
        return true;
    }

    /**
     * Commit a transaction.
     */
    protected function _commit()
    {
        return true;
    }

    /**
     * Roll-back a transaction.
     */
    protected function _rollBack()
    {
        return true;
    }

    /**
     * Set the fetch mode.
     *
     * @param integer $mode
     */
    public function setFetchMode($mode)
    {
        return;
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param mixed $sql
     * @param integer $count
     * @param integer $offset
     * @return string
     */
    public function limit($sql, $count, $offset = 0)
    {
        return $sql . " LIMIT $count OFFSET $offset";
    }

    /**
     * Check if the adapter supports real SQL parameters.
     *
     * @param string $type
     * @return bool
     */
    public function supportsParameters($type)
    {
        return true;
    }

    /**
     * Retrieve server version in PHP style
     *
     * @return string
     */
    public function getServerVersion() {
        return "5.6.7.8";
    }
}
