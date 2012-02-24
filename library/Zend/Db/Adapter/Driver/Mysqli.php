<?php

namespace Zend\Db\Adapter\Driver;

class Mysqli implements \Zend\Db\Adapter\DriverInterface
{

    /**
     * @var Mysqli\Connection
     */
    protected $connection = null;

    /**
     * @var Mysqli\Statement
     */
    protected $statementPrototype = null;

    /**
     * @var Mysqli\Result
     */
    protected $resultPrototype = null;

    /**
     * @param array|Mysqli\Connection|\mysqli $connection
     * @param null|Mysqli\Statement $statementPrototype
     * @param null|Mysqli\Result $resultPrototype
     */
    public function __construct($connection, Mysqli\Statement $statementPrototype = null, Mysqli\Result $resultPrototype = null)
    {
        if (!$connection instanceof Mysqli\Connection) {
            $connection = new Mysqli\Connection($connection);
        }

        if (!$connection instanceof Mysqli\Connection) {
            throw new \InvalidArgumentException('$connection must be an array of parameters or a Pdo\Connection object');
        }

        $this->registerConnection($connection);
        $this->registerStatementPrototype(($statementPrototype) ?: new Mysqli\Statement());
        $this->registerResultPrototype(($resultPrototype) ?: new Mysqli\Result());
    }

    public function registerConnection(Mysqli\Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setDriver($this);
        return $this;
    }

    public function registerStatementPrototype(Mysqli\Statement $statementPrototype)
    {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this);
    }

    public function registerResultPrototype(Mysqli\Result $resultPrototype)
    {
        $this->resultPrototype = $resultPrototype;
        $this->resultPrototype->setDriver($this);
    }

    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        if ($nameFormat == self::NAME_FORMAT_CAMELCASE) {
            return 'Mysql';
        } else {
            return 'MySQL';
        }
    }
    
    public function checkEnvironment()
    {
        if (!extension_loaded('mysqli')) {
            throw new \Exception('The Mysqli extension is required for this adapter but the extension is not loaded');
        }
    }

    /**
     * @return Mysqli\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $sql
     * @return Mysqli\Statement
     */
    public function createStatement($sql)
    {
        $statement = clone $this->statementPrototype;
        $statement->initialize($this->connection->getResource(), $sql);
        return $statement;
    }

    /**
     * @return Mysqli\Result
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
        return array('positional');
    }

    /**
     * @param $name
     * @return string
     */
    public function formatParameterName($name)
    {
        return '?';
    }
}
