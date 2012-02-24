<?php

namespace Zend\Db\Adapter\Driver\Sqlsrv;

use Zend\Db\Adapter\Driver\ConnectionInterface;

class Connection implements ConnectionInterface
{
    /**
     * @var Sqlsrv
     */
    protected $driver = null;

    /**
     * @var array
     */
    protected $connectionParameters = array();
    
    /**
     * @var resource
     */
    protected $resource = null;

    /**
     * @var bool
     */
    protected $inTransaction = false;
    
    public function __construct($connectionInfo)
    {
        if (is_array($connectionInfo)) {
            $this->setConnectionParameters($connectionInfo);
        } elseif (is_resource($connectionInfo)) {
            $this->setResource($connectionInfo);
        }
    }
    
    public function setDriver(Sqlsrv $driver)
    {
        $this->driver = $driver;
        return $this;
    }
    
    public function setConnectionParameters(array $connectionParameters)
    {
        $this->connectionParameters = $connectionParameters;
        return $this;
    }
    
    public function getConnectionParameters()
    {
        return $this->connectionParameters;
    }
    
    public function getDefaultCatalog()
    {
        return null;
    }
    
    public function getDefaultSchema()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $result = sqlsrv_query($this->resource, 'SELECT SCHEMA_NAME()');
        $r = sqlsrv_fetch_array($result);
        return $r[0];
    }

    public function setResource($resource)
    {
        if (get_resource_type($resource) !== 'SQL Server Connection') {
            throw new \Exception('Resource provided was not of type SQL Server Connection');
        }
        $this->resource = $resource;
        return $this;
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }
    
    public function connect()
    {
        if ($this->resource) {
            return;
        }
        
        $serverName = '.';
        $params = array();
        foreach ($this->connectionParameters as $cpName => $cpValue) {
            switch (strtolower($cpName)) {
                case 'hostname':
                case 'servername':
                    $serverName = $cpValue;
                    break;
                case 'username':
                case 'uid':
                    $params['UID'] = $cpValue;
                    break;
                case 'password':
                case 'pwd':
                    $params['PWD'] = $cpValue;
                    break;
                case 'database':
                case 'dbname':
                    $params['Database'] = $cpValue;
                    break;
            }
        }

        $this->resource = sqlsrv_connect($serverName, $params);

        if (!$this->resource) {
            $prevErrorException = new ErrorException(sqlsrv_errors());
            throw new \Exception('Connect Error', null, $prevErrorException);
        }

    }
    
    public function isConnected()
    {
        return (is_resource($this->resource));
    }
    
    public function disconnect()
    {
        sqlsrv_close($this->resource);
        unset($this->resource);
    }
    
    public function beginTransaction()
    {
        // http://msdn.microsoft.com/en-us/library/cc296151.aspx
        /*
        $this->resource->autocommit(false);
        $this->inTransaction = true;
        */
    }
    
    public function commit()
    {
        // http://msdn.microsoft.com/en-us/library/cc296194.aspx
        /*
        if (!$this->resource) {
            $this->connect();
        }
        
        $this->resource->commit();
        
        $this->inTransaction = false;
        */
    }
    
    public function rollback()
    {
        // http://msdn.microsoft.com/en-us/library/cc296176.aspx
        /*
        if (!$this->resource) {
            throw new \Exception('Must be connected before you can rollback.');
        }
        
        if (!$this->_inCommit) {
            throw new \Exception('Must call commit() before you can rollback.');
        }
        
        $this->resource->rollback();
        return $this;
        */
    }
    
    
    public function execute($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $returnValue = sqlsrv_query($this->resource, $sql);
        
        // if the returnValue is something other than a Sqlsrv_result, bypass wrapping it
        if ($returnValue === false) {
            $errors = sqlsrv_errors();
            // ignore general warnings
            if ($errors[0]['SQLSTATE'] != '01000') {
                throw new \RuntimeException($errors[0]['message']);
            }
        }

        $result = $this->driver->createResult($returnValue);
        return $result;
    }
    
    public function prepare($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $statement = $this->driver->createStatement($sql);
        return $statement;
    }

    public function getLastGeneratedId()
    {
        $sql = 'SELECT SCOPE_IDENTITY() as Current_Identity';
        $result = sqlsrv_query($this->resource, $sql);
        $row = sqlsrv_fetch_array($result);
        return $row['Current_Identity'];
    }

}
    