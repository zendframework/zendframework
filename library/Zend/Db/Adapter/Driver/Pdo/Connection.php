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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Driver\ConnectionInterface,
    Zend\Db\Adapter\Driver\DriverInterface,
    Zend\Db\Adapter\Exception\InvalidQueryException;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Connection implements ConnectionInterface
{
    /**
     * @var Pdo
     */
    protected $driver = null;

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
     * @param array|\PDO $connectionInfo
     */
    public function __construct($connectionInfo = null)
    {
        if (is_array($connectionInfo)) {
            $this->setConnectionParameters($connectionInfo);
        } elseif ($connectionInfo instanceof \PDO) {
            $this->setResource($connectionInfo);
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
     * @param array $connectionParameters
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

        /** @var $result \PDOStatement */
        $result = $this->resource->query('SELECT DATABASE()');
        $r = $result->fetch_row();
        return $r[0];
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

        // @todo method createKnownDsn

        $dsn = $username = $password = $database = null;
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
                case 'database':
                case 'dbname':
                    $database = (string) $value;
                    break;
                case 'driver_options':
                case 'options':
                    $options = array_merge($options, (array) $value);
                    break;
                default:
                    $options[$key] = $value;
                    break;
            }
        }

        if (!isset($dsn) && isset($pdoDriver)) {
            $dsn = $pdoDriver . ':';
            if (isset($database)) {
                $dsn .= $database;
            }
        } elseif (!isset($dsn)) {
            throw new \Exception('A dsn was not provided or could not be constructed from your parameters');
        }

        try {
            $this->resource = new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            throw new \Exception('Connect Error: ' . $e->getMessage(), $e->getCode(), $e);
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
    public function getLastGeneratedId()
    {
        return $this->resource->lastInsertId();
    }

}
