<?php

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter,
    Zend\Db\Adapter\DriverInterface,
    Zend\Db\Adapter\Exception\InvalidQueryException,
    PDO,
    PDOException,
    PDOStatement;


class Connection implements Adapter\DriverConnectionInterface
{
    /**
     * @var \Zend\Db\Adapter\Driver\Pdo
     */
    protected $driver = null;

    /**
     * @var array
     */
    protected $connectionParameters = array();
    
    /**
     * @var PDO
     */
    protected $resource = null;

    /**
     * @var bool
     */
    protected $inTransaction = false;

    /**
     * @param array|PDO $connectionParameters
     */
    public function __construct($connectionInfo = null)
    {
        if (is_array($connectionInfo)) {
            $this->setConnectionParameters($connectionInfo);
        } elseif ($connectionInfo instanceof PDO) {
            $this->setResource($connectionInfo);
        }
    }

    /**
     * @param DriverInterface $driver
     * @return Connection
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @param array $connectionParams
     */
    public function setConnectionParameters(array $connectionParameters)
    {
        $this->connectionParameters = $connectionParameters;
    }

    /**
     * @return array
     */
    public function getConnectionParameters()
    {
        return $this->connectionParameters;
    }

    /**
     * @return null
     */
    public function getDefaultCatalog()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getDefaultSchema()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        
        $result = $this->resource->query('SELECT DATABASE()');
        $r = $result->fetch_row();
        return $r[0];
    }

    public function setResource(PDO $resource)
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * @return PDO
     */
    public function getResource()
    {
        if ($this->resource == null) {
            $this->connect();
        }
        return $this->resource;
    }

    /**
     * @return Connection
     * @throws \Exception
     */
    public function connect()
    {
        if ($this->resource) {
            return $this;
        }

        // @todo method createKnownDsn

        $dsn = $username = $password = null;
        $options = array();
        foreach ($this->connectionParameters as $key => $value) {
            switch ($key) {
                case 'dsn':
                    $dsn = (string) $value;
                    break;
                case 'username':
                    $username = (string) $value;
                    break;
                case 'password':
                    $password = (string) $value;
                    break;
                case 'options':
                    $options = array_merge($options, (array) $value);
                    break;
                default:
                    $options[$key] = $value;
                    break;
            }
        }

        try {
            $this->resource = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new \Exception('Connect Error: ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return ($this->resource instanceof PDO);
    }

    /**
     * @return Connection
     */
    public function disconnect()
    {
        if ($this->isConnected()) {
            unset($this->resource);
        }
        return $this;
    }

    /**
     * @return Connection
     */
    public function beginTransaction()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        $this->resource->beginTransaction();
        $this->inTransaction = true;
        return $this;
    }

    /**
     * @return Connection
     */
    public function commit()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        
        $this->resource->commit();
        $this->inTransaction = false;
        return $this;
    }

    /**
     * @return Connection
     * @throws \Exception
     */
    public function rollback()
    {
        if (!$this->isConnected()) {
            throw new \Exception('Must be connected before you can rollback');
        }
        
        if (!$this->inTransaction) {
            throw new \Exception('Must call commit() before you can rollback');
        }
        
        $this->resource->rollBack();
        return $this;
    }
    
    /**
     * @param $sql
     * @return Result
     * @throws \Zend\Db\Adapter\Exception\InvalidQueryException
     */
    public function execute($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        
        $resultResource = $this->resource->query($sql);
        
        if ($resultResource === false) {
            $errorInfo = $this->resource->errorInfo();
            throw new InvalidQueryException($errorInfo[2]);
        }

        $result = $this->driver->createResult($resultResource);
        return $result;

    }
    
    /**
     * @todo PDO_SQLite does not support scrollable cursors; make this configurable based on dsn?
     */
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
        return $this->resource->lastInsertId();
    }

}
