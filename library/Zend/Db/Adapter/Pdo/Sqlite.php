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
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Db\Adapter\Pdo;
use Zend\Db,
    Zend\Db\Adapter,
    Zend\Db\Adapter\AbstractPdoAdapter;

/**
 * Class for connecting to Sqlite2 and Sqlite3 databases and performing common operations.
 *
 * @uses       \Zend\Db\Db
 * @uses       \Zend\Db\Adapter\Exception
 * @uses       \Zend\Db\Adapter\Pdo\AbstractPdo
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Sqlite extends \Zend\Db\Adapter\AbstractPdoAdapter
{

    /**
     * Pdo type
     *
     * @var string
     */
     protected $_pdoType = 'sqlite';

    /**
     * Keys are UPPERCASE SQL datatypes or the constants
     * Zend_Db::INT_TYPE, Zend_Db::BIGINT_TYPE, or Zend_Db::FLOAT_TYPE.
     *
     * Values are:
     * 0 = 32-bit integer
     * 1 = 64-bit integer
     * 2 = float or decimal
     *
     * @var array Associative array of datatypes to values 0, 1, or 2.
     */
    protected $_numericDataTypes = array(
        Db\Db::INT_TYPE    => Db\Db::INT_TYPE,
        Db\Db::BIGINT_TYPE => Db\Db::BIGINT_TYPE,
        Db\Db::FLOAT_TYPE  => Db\Db::FLOAT_TYPE,
        'INTEGER'            => Db\Db::BIGINT_TYPE,
        'REAL'               => Db\Db::FLOAT_TYPE
    );

    /**
     * Constructor.
     *
     * $config is an array of key/value pairs containing configuration
     * options.  Note that the Sqlite options are different than most of
     * the other Pdo adapters in that no username or password are needed.
     * Also, an extra config key "sqlite2" specifies compatibility mode.
     *
     * dbname    => (string) The name of the database to user (required,
     *                       use :memory: for memory-based database)
     *
     * sqlite2   => (boolean) Pdo_SQLITE defaults to Sqlite 3.  For compatibility
     *                        with an older Sqlite 2 database, set this to TRUE.
     *
     * @param array $config An array of configuration keys.
     */
    public function __construct(array $config = array())
    {
        if (isset($config['sqlite2']) && $config['sqlite2']) {
            $this->_pdoType = 'sqlite2';
        }

        // Sqlite uses no username/password.  Stub to satisfy parent::_connect()
        $this->_config['username'] = null;
        $this->_config['password'] = null;

        return parent::__construct($config);
    }

    /**
     * Check for config options that are mandatory.
     * Throw exceptions if any are missing.
     *
     * @param array $config
     * @throws \Zend\Db\Adapter\Exception
     */
    protected function _checkRequiredOptions(array $config)
    {
        // we need at least a dbname
        if (! array_key_exists('dbname', $config)) {
            throw new Adapter\Exception("Configuration array must have a key for 'dbname' that names the database instance");
        }
    }

    /**
     * DSN builder
     */
    protected function _dsn()
    {
        return $this->_pdoType .':'. $this->_config['dbname'];
    }

    /**
     * Special configuration for Sqlite behavior: make sure that result sets
     * contain keys like 'column' instead of 'table.column'.
     *
     * @throws \Zend\Db\Adapter\Exception
     */
    protected function _connect()
    {
        /**
         * if we already have a Pdo object, no need to re-connect.
         */
        if ($this->_connection) {
            return;
        }

        parent::_connect();

        $retval = $this->_connection->exec('PRAGMA full_column_names=0');
        if ($retval === false) {
            $error = $this->_connection->errorInfo();
            throw new Adapter\Exception($error[2]);
        }

        $retval = $this->_connection->exec('PRAGMA short_column_names=1');
        if ($retval === false) {
            $error = $this->_connection->errorInfo();
            throw new Adapter\Exception($error[2]);
        }
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        $sql = "SELECT name FROM sqlite_master WHERE type='table' "
             . "UNION ALL SELECT name FROM sqlite_temp_master "
             . "WHERE type='table' ORDER BY name";

        return $this->fetchCol($sql);
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
     * IDENTITY         => integer; true if column is auto-generated with unique values
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        $sql = 'PRAGMA ';

        if ($schemaName) {
            $sql .= $this->quoteIdentifier($schemaName) . '.';
        }

        $sql .= 'table_info('.$this->quoteIdentifier($tableName).')';

        $stmt = $this->query($sql);

        /**
         * Use FETCH_NUM so we are not dependent on the CASE attribute of the Pdo connection
         */
        $result = $stmt->fetchAll(Db\Db::FETCH_NUM);

        $cid        = 0;
        $name       = 1;
        $type       = 2;
        $notnull    = 3;
        $dflt_value = 4;
        $pk         = 5;

        $desc = array();

        $p = 1;
        foreach ($result as $key => $row) {
            list($length, $scale, $precision, $primary, $primaryPosition, $identity) =
                array(null, null, null, false, null, false);
            if (preg_match('/^((?:var)?char)\((\d+)\)/i', $row[$type], $matches)) {
                $row[$type] = $matches[1];
                $length = $matches[2];
            } else if (preg_match('/^decimal\((\d+),(\d+)\)/i', $row[$type], $matches)) {
                $row[$type] = 'DECIMAL';
                $precision = $matches[1];
                $scale = $matches[2];
            }
            if ((bool) $row[$pk]) {
                $primary = true;
                $primaryPosition = $p;
                /**
                 * Sqlite INTEGER primary key is always auto-increment.
                 */
                $identity = (bool) ($row[$type] == 'INTEGER');
                ++$p;
            }
            $desc[$this->foldCase($row[$name])] = array(
                'SCHEMA_NAME'      => $this->foldCase($schemaName),
                'TABLE_NAME'       => $this->foldCase($tableName),
                'COLUMN_NAME'      => $this->foldCase($row[$name]),
                'COLUMN_POSITION'  => $row[$cid]+1,
                'DATA_TYPE'        => $row[$type],
                'DEFAULT'          => $row[$dflt_value],
                'NULLABLE'         => ! (bool) $row[$notnull],
                'LENGTH'           => $length,
                'SCALE'            => $scale,
                'PRECISION'        => $precision,
                'UNSIGNED'         => null, // Sqlite3 does not support unsigned data
                'PRIMARY'          => $primary,
                'PRIMARY_POSITION' => $primaryPosition,
                'IDENTITY'         => $identity
            );
        }
        return $desc;
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param string $sql
     * @param integer $count
     * @param integer $offset OPTIONAL
     * @return string
     */
    public function limit($sql, $count, $offset = 0)
    {
        $count = intval($count);
        if ($count <= 0) {
            throw new Adapter\Exception("LIMIT argument count=$count is not valid");
        }

        $offset = intval($offset);
        if ($offset < 0) {
            throw new Adapter\Exception("LIMIT argument offset=$offset is not valid");
        }

        $sql .= " LIMIT $count";
        if ($offset > 0) {
            $sql .= " OFFSET $offset";
        }

        return $sql;
    }

}
