<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Driver\ConnectionInterface,
    Zend\Db\Adapter\Driver\DriverInterface,
    Zend\Db\Adapter\Exception;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
class Connection implements ConnectionInterface
{
    /**
     * @var Pdo
     */
    protected $driver = null;

    /**
     * @var string
     */
    protected $driverName = null;

    /**
     * @var array
     */
    protected $connectionParameters = array();

    /**
     * @var \PDO
     */
    protected $resource = null;

    /**
     * @var bool
     */
    protected $inTransaction = false;

    /**
     * @param array|\PDO $connectionParameters
     */
    public function __construct($connectionParameters = null)
    {
        if (is_array($connectionParameters)) {
            $this->setConnectionParameters($connectionParameters);
        } elseif ($connectionParameters instanceof \PDO) {
            $this->setResource($connectionParameters);
        }
    }

    /**
     * @param Pdo $driver
     * @return Connection
     */
    public function setDriver(Pdo $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDriverName()
    {
        return $this->driverName;
    }

    /**
     * @param array $connectionParameters
     */
    public function setConnectionParameters(array $connectionParameters)
    {
        $this->connectionParameters = $connectionParameters;
        if (isset($connectionParameters['dsn'])) {
            $this->driverName = substr($connectionParameters['dsn'], 0,
                strpos($connectionParameters['dsn'], ':')
            );
        } elseif (isset($connectionParameters['pdodriver'])) {
            $this->driverName = strtolower($connectionParameters['pdodriver']);
        } elseif (isset($connectionParameters['driver'])) {
            $this->driverName = strtolower(substr(
                str_replace(array('-', '_', ' '), '', $connectionParameters['driver']),
                3
            ));
        }
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

        /** @var $result \PDOStatement */
        $result = $this->resource->query('SELECT DATABASE()');
        if ($result instanceof \PDOStatement) {
            $r = $result->fetch_row();
            return $r[0];
        }
        return false;
    }

    /**
     * Set resource
     * 
     * @param  \PDO $resource
     * @return Connection 
     */
    public function setResource(\PDO $resource)
    {
        $this->resource = $resource;
        $this->driverName = strtolower($this->resource->getAttribute(\PDO::ATTR_DRIVER_NAME));
        return $this;
    }

    /**
     * @return \PDO
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

        $dsn = $username = $password = $hostname = $database = null;
        $options = array();
        foreach ($this->connectionParameters as $key => $value) {
            switch (strtolower($key)) {
                case 'dsn':
                    $dsn = $value;
                    break;
                case 'driver':
                    $value = strtolower($value);
                    if (strpos($value, 'pdo') === 0) {
                        $pdoDriver = strtolower(substr(str_replace(array('-', '_', ' '), '', $value), 3));
                    }
                    break;
                case 'pdodriver':
                    $pdoDriver = (string) $value;
                    break;
                case 'user':
                case 'username':
                    $username = (string) $value;
                    break;
                case 'pass':
                case 'password':
                    $password = (string) $value;
                    break;
                case 'host':
                case 'hostname':
                    $hostname = (string) $value;
                    break;
                case 'database':
                case 'dbname':
                    $database = (string) $value;
                    break;
                case 'driver_options':
                case 'options':
                    $value = (array) $value;
                    $options = array_diff_key($options, $value) + $value;
                    break;
                default:
                    $options[$key] = $value;
                    break;
            }
        }

        if (!isset($dsn) && isset($pdoDriver)) {
            $dsn = array();
            switch ($pdoDriver) {
                case 'sqlite':
                    $dsn[] = $database;
                    break;
                default:
                    if (isset($database)) {
                        $dsn[] = "dbname={$database}";
                    }
                    if (isset($hostname)) {
                        $dsn[] = "host={$hostname}";
                    }
                    break;
            }
            $dsn = $pdoDriver . ':' . implode(';', $dsn);
        } elseif (!isset($dsn)) {
            throw new Exception\InvalidConnectionParametersException(
                'A dsn was not provided or could not be constructed from your parameters',
                $this->connectionParameters
            );
        }

        try {
            $this->resource = new \PDO($dsn, $username, $password, $options);
            $this->resource->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->driverName = strtolower($this->resource->getAttribute(\PDO::ATTR_DRIVER_NAME));
        } catch (\PDOException $e) {
            throw new Exception\RuntimeException('Connect Error: ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return ($this->resource instanceof \PDO);
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
            throw new Exception\RuntimeException('Must be connected before you can rollback');
        }

        if (!$this->inTransaction) {
            throw new Exception\RuntimeException('Must call beginTransaction() before you can rollback');
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
            throw new Exception\InvalidQueryException($errorInfo[2]);
        }

        $result = $this->driver->createResult($resultResource, $sql);
        return $result;

    }

    /**
     * @param string $sql
     * @return Statement
     */
    public function prepare($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $statement = $this->driver->createStatement($sql);
        return $statement;
    }

    /**
     * Get last generated id
     * 
     * @return integer 
     */
    public function getLastGeneratedValue()
    {
        try {
            return $this->resource->lastInsertId();
        } catch (\Exception $e) {
            // do nothing
        }
        return false;
    }

}
