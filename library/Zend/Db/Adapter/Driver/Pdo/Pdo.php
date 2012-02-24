<?php

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Driver\DriverInterface;

class Pdo implements DriverInterface
{
    /**
     * @var Connection
     */
    protected $connection = null;

    /**
     * @var Statement
     */
    protected $statementPrototype = null;

    /**
     * @var Result
     */
    protected $resultPrototype = null;

    /**
     * @param array|Connection|\PDO $connection
     * @param null|Statement $statementPrototype
     * @param null|Result $resultPrototype
     */
    public function __construct($connection, Statement $statementPrototype = null, Result $resultPrototype = null)
    {
        if (!$connection instanceof Connection) {
            $connection = new Connection($connection);
        }

        if (!$connection instanceof Connection) {
            throw new \InvalidArgumentException('$connection must be an array of parameters or a Pdo\Connection object');
        }

        $this->registerConnection($connection);
        $this->registerStatementPrototype(($statementPrototype) ?: new Statement());
        $this->registerResultPrototype(($resultPrototype) ?: new Result());
    }

    public function registerConnection(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setDriver($this);
        return $this;
    }

    public function registerStatementPrototype(Statement $statementPrototype)
    {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this);
    }

    public function registerResultPrototype(Result $resultPrototype)
    {
        $this->resultPrototype = $resultPrototype;
    }

    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        return ucwords($this->getConnection()->getResource()->getAttribute(\PDO::ATTR_DRIVER_NAME));
    }
    
    public function checkEnvironment()
    {
        if (!extension_loaded('PDO')) {
            throw new \Exception('The PDO extension is required for this adapter but the extension is not loaded');
        }
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $sql
     * @return Statement
     */
    public function createStatement($sql)
    {
        $statement = clone $this->statementPrototype;
        $statement->initialize($this->connection->getResource(), $sql);
        return $statement;
    }

    /**
     * @param resource $resource
     * @return Result
     */
    public function createResult($resource)
    {
        $result = clone $this->resultPrototype;
        $result->initialize($resource);
        return $result;
    }

    /**
     * @return array
     */
    public function getPrepareTypeSupport()
    {
        return array('named', 'positional');
    }

    /**
     * @param string $name
     * @param string|null $type
     * @return string
     */
    public function formatParameterName($name, $type = null)
    {
        if ($type == null && !is_numeric($name) || $type == 'named') {
            return ':' . $name;
        } else {
            return '?';
        }
    }

}
