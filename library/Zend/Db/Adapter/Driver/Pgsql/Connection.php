<?php

namespace Zend\Db\Adapter\Driver\Pgsql;

use Zend\Db\Adapter\Driver\ConnectionInterface,
    Zend\Db\Adapter\Exception;

class Connection implements ConnectionInterface
{

    /**
     * @var Pgsql
     */
    protected $driver = null;

    /**
     * Connection parameters
     *
     * @var array
     */
    protected $connectionParameters = array();

    /**
     * @var resource
     */
    protected $resource = null;

    /**
     * In transaction
     *
     * @var boolean
     */
    protected $inTransaction = false;

    /**
     * Constructor
     *
     * @param mysqli $connectionInfo
     */
    public function __construct($connectionInfo = null)
    {
        if (is_array($connectionInfo)) {
            $this->setConnectionParameters($connectionInfo);
        } elseif ($connectionInfo instanceof \mysqli) {
            $this->setResource($connectionInfo);
        }
    }

    public function setConnectionParameters(array $connectionParameters)
    {
        $this->connectionParameters = $connectionParameters;
        return $this;
    }

    public function setDriver(Pgsql $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    public function setResource($resource)
    {
        $this->resource = $resource;
        return;
    }

    public function getDefaultCatalog()
    {
        return null;
    }

    public function getDefaultSchema()
    {
        // TODO: Implement getDefaultSchema() method.
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function connect()
    {
        if (is_resource($this->resource)) {
            return;
        }

        // localize
        $p = $this->connectionParameters;

        // given a list of key names, test for existence in $p
        $findParameterValue = function(array $names) use ($p) {
            foreach ($names as $name) {
                if (isset($p[$name])) {
                    return $p[$name];
                }
            }
            return null;
        };

        $connection = array();
        $connection['host'] = $findParameterValue(array('hostname', 'host'));
        $connection['user'] = $findParameterValue(array('username', 'user'));
        $connection['password'] = $findParameterValue(array('password', 'passwd', 'pw'));
        $connection['dbname'] = $findParameterValue(array('database', 'dbname', 'db', 'schema'));
        $connection['port'] = (isset($p['port'])) ? (int) $p['port'] : null;
        $connection['socket'] = (isset($p['socket'])) ? $p['socket'] : null;
        $connection = array_filter($connection); // remove nulls
        $connection = http_build_query($connection, null, ' '); // @link http://php.net/pg_connect

        $this->resource = pg_connect($connection);

        if ($this->resource === false) {
            die('dead');
        }

//        if ($this->resource === false) {
//            throw new Exception\RuntimeException(
//                'Connection error',
//                null,
//                new Exception\ErrorException($this->resource->connect_error, $this->resource->connect_errno)
//            );
//        }

    }

    public function isConnected()
    {
        return (is_resource($this->resource));
    }

    public function disconnect()
    {
        pg_close($this->resource);
    }

    public function beginTransaction()
    {
        // TODO: Implement beginTransaction() method.
    }

    public function commit()
    {
        // TODO: Implement commit() method.
    }

    public function rollback()
    {
        // TODO: Implement rollback() method.
    }

    public function execute($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $resultResource = pg_query($this->resource, $sql);

        //var_dump(pg_result_status($resultResource));

        // if the returnValue is something other than a mysqli_result, bypass wrapping it
        if ($resultResource === false) {
            throw new Exception\InvalidQueryException(pg_errormessage());
        }

        $resultPrototype = $this->driver->createResult(($resultResource === true) ? $this->resource : $resultResource);
        return $resultPrototype;
    }

    public function getLastGeneratedValue($name = null)
    {
        // @todo create a feature that opts-into this behavior, using sql to find serial name
        $result = @pg_query($this->resource, 'SELECT LASTVAL() as "lastvalue"');
        if ($result == false) {
            return null;
        }
        return pg_fetch_result($result, 0, 'lastvalue');
    }

    public function getCurrentSchema()
    {
        return null;
    }
}