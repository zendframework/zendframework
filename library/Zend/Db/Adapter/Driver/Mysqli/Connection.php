<?php

namespace Zend\Db\Adapter\Driver\Mysqli;
use Zend\Db\Adapter;


class Connection implements Adapter\DriverConnectionInterface
{
    /**
     * @var \Zend\Db\Adapter\Driver\Mysqli
     */
    protected $driver = null;
    
    protected $connectionParams = array();
    
    /**
     * @var \mysqli
     */
    protected $resource = null;

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
        
        $result = $this->resource->query('SELECT DATABASE()');
        $r = $result->fetch_row();
        return $r[0];
    }
    
    /**
     * @return \Mysqli
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

        // localize
        $p = $this->connectionParams;

        // given a list of key names, test for existence in $p
        $findParameterValue = function(array $names) use ($p) {
            foreach ($names as $name) {
                if (isset($p[$name])) {
                    return $p[$name];
                }
            }
            return null;
        };

        $hostname = $findParameterValue(array('hostname', 'host'));
        $username = $findParameterValue(array('username', 'user'));
        $password = $findParameterValue(array('password', 'passwd', 'pw'));
        $database = $findParameterValue(array('database', 'dbname', 'db', 'schema'));
        $port     = (isset($p['port'])) ? (int) $p['port'] : null;
        $socket   = (isset($p['socket'])) ? $p['socket'] : null;

        $this->resource = new \Mysqli($hostname, $username, $password, $database, $port, $socket);

        if ($this->resource->connect_error) {
            throw new \Exception('Connect Error (' . $this->resource->connect_errno . ') ' . $this->resource->connect_error);
        }

        if (!empty($p['charset'])) {
            $this->resource->set_charset($this->resource, $p['charset']);
        }

    }
    
    public function isConnected()
    {
        return ($this->resource instanceof \Mysqli);
    }
    
    public function disconnect()
    {
        $this->resource->close();
        unset($this->resource);
    }
    
    public function beginTransaction()
    {
        $this->resource->autocommit(false);
        $this->inTransaction = true;
    }
    
    public function commit()
    {
        if (!$this->resource) {
            $this->connect();
        }
        
        $this->resource->commit();
        
        $this->inTransaction = false;
    }
    
    public function rollback()
    {
        if (!$this->resource) {
            throw new \Exception('Must be connected before you can rollback.');
        }
        
        if (!$this->inTransaction) {
            throw new \Exception('Must call commit() before you can rollback.');
        }
        
        $this->resource->rollback();
        return $this;
    }
    
    
    public function execute($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $resultResource = $this->resource->query($sql);

        // if the returnValue is something other than a mysqli_result, bypass wrapping it
        if ($resultResource === false) {
            throw new \Zend\Db\Adapter\Exception\InvalidQueryException($this->resource->error);
        }

        $resultPrototype = $this->driver->createResult(($resultResource === true) ? $this->resource : $resultResource);
        return $resultPrototype;
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
        return $this->resource->insert_id;
    }
}
    