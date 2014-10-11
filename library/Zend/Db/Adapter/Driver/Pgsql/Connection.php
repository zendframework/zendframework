<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Adapter\Driver\Pgsql;

use Zend\Db\Adapter\Driver\AbstractConnection;
use Zend\Db\Adapter\Exception;

class Connection extends AbstractConnection
{
    /**
     * @var Pgsql
     */
    protected $driver = null;

    /**
     * Constructor
     *
     * @param resource|array|null $connectionInfo
     */
    public function __construct($connectionInfo = null)
    {
        if (is_array($connectionInfo)) {
            $this->setConnectionParameters($connectionInfo);
        } elseif (is_resource($connectionInfo)) {
            $this->setResource($connectionInfo);
        }
    }

    /**
     * Set driver
     *
     * @param  Pgsql $driver
     * @return self
     */
    public function setDriver(Pgsql $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * Get current schema
     *
     * @return null|string
     */
    public function getCurrentSchema()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $result = pg_query($this->resource, 'SELECT CURRENT_SCHEMA AS "currentschema"');
        if ($result == false) {
            return null;
        }

        return pg_fetch_result($result, 0, 'currentschema');
    }

    /**
     * Connect to the database
     *
     * @return self
     * @throws Exception\RuntimeException on failure
     */
    public function connect()
    {
        if (is_resource($this->resource)) {
            return $this;
        }

        // localize
        $p = $this->connectionParameters;

        // given a list of key names, test for existence in $p
        $findParameterValue = function (array $names) use ($p) {
            foreach ($names as $name) {
                if (isset($p[$name])) {
                    return $p[$name];
                }
            }

            return null;
        };

        $connection             = array();
        $connection['host']     = $findParameterValue(array('hostname', 'host'));
        $connection['user']     = $findParameterValue(array('username', 'user'));
        $connection['password'] = $findParameterValue(array('password', 'passwd', 'pw'));
        $connection['dbname']   = $findParameterValue(array('database', 'dbname', 'db', 'schema'));
        $connection['port']     = (isset($p['port'])) ? (int) $p['port'] : null;
        $connection['socket']   = (isset($p['socket'])) ? $p['socket'] : null;

        $connection = array_filter($connection); // remove nulls
        $connection = http_build_query($connection, null, ' '); // @link http://php.net/pg_connect

        set_error_handler(function ($number, $string) {
            throw new Exception\RuntimeException(
                __METHOD__ . ': Unable to connect to database', null, new Exception\ErrorException($string, $number)
            );
        });
        $this->resource = pg_connect($connection);
        restore_error_handler();

        if ($this->resource === false) {
            throw new Exception\RuntimeException(sprintf(
                '%s: Unable to connect to database',
                __METHOD__
            ));
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return (is_resource($this->resource));
    }

    /**
     * @return void
     */
    public function disconnect()
    {
        pg_close($this->resource);
    }

    /**
     * Begin transaction
     *
     * @return self
     */
    public function beginTransaction()
    {
        if ($this->inTransaction()) {
            throw new Exception\RuntimeException('Nested transactions are not supported');
        }

        if (!$this->isConnected()) {
            $this->connect();
        }

        pg_query($this->resource, 'BEGIN');
        $this->inTransaction = true;

        return $this;
    }

    /**
     * Commit
     *
     * @return self
     */
    public function commit()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        if (!$this->inTransaction()) {
            return; // We ignore attempts to commit non-existing transaction
        }

        pg_query($this->resource, 'COMMIT');
        $this->inTransaction = false;

        return $this;
    }

    /**
     * Rollback
     *
     * @return self
     */
    public function rollback()
    {
        if (!$this->isConnected()) {
            throw new Exception\RuntimeException('Must be connected before you can rollback');
        }

        if (!$this->inTransaction()) {
            throw new Exception\RuntimeException('Must call beginTransaction() before you can rollback');
        }

        pg_query($this->resource, 'ROLLBACK');
        $this->inTransaction = false;

        return $this;
    }

    /**
     * @param  string                                         $sql
     * @throws Exception\InvalidQueryException
     * @return resource|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function execute($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        if ($this->profiler) {
            $this->profiler->profilerStart($sql);
        }

        $resultResource = pg_query($this->resource, $sql);

        if ($this->profiler) {
            $this->profiler->profilerFinish($sql);
        }

        // if the returnValue is something other than a pg result resource, bypass wrapping it
        if ($resultResource === false) {
            throw new Exception\InvalidQueryException(pg_errormessage());
        }

        $resultPrototype = $this->driver->createResult(($resultResource === true) ? $this->resource : $resultResource);

        return $resultPrototype;
    }

    /**
     * @param  null   $name Ignored
     * @return string
     */
    public function getLastGeneratedValue($name = null)
    {
        if ($name == null) {
            return null;
        }
        $result = pg_query($this->resource, 'SELECT CURRVAL(\'' . str_replace('\'', '\\\'', $name) . '\') as "currval"');

        return pg_fetch_result($result, 0, 'currval');
    }
}
