<?php

namespace Zend\Db\Adapter\Driver\Sqlsrv;
use Zend\Db\Adapter;


class Connection implements Adapter\DriverConnectionInterface
{
    /**
     * @var \Zend\Db\Adapter\DriverInterface
     */
    protected $driver = null;

    /**
     * @var array
     */
    protected $connectionParams = array();
    
    /**
     * @var \Sqlsrv
     */
    protected $resource = null;

    /**
     * @var bool
     */
    protected $inTransaction = false;
    
    public function __construct(array $connectionParameters = array())
    {
        if ($connectionParameters) {
            $this->setConnectionParams($connectionParameters);
        }
    }
    
    public function setDriver(Adapter\DriverInterface $driver)
    {
        $this->driver = $driver;
        return $this;
    }
    
    public function setConnectionParams(array $connectionParameters)
    {
        $this->connectionParams = $connectionParameters;
        return $this;
    }
    
    public function getConnectionParams()
    {
        return $this->connectionParams;
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
    
    /**
     * @return \Sqlsrv
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
        foreach ($this->connectionParams as $cpName => $cpValue) {
            switch (strtolower($cpName)) {
                case 'hostname':
                case 'servername':
                    $serverName = $cpValue;
                    break;
                // @todo check other sqlsrv param values
                default:
                    $params[$cpName] = $cpValue;
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
    