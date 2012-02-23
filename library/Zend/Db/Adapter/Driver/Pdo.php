<?php

namespace Zend\Db\Adapter\Driver;

use PDOStatement;

class Pdo implements \Zend\Db\Adapter\DriverInterface
{
    /**
     * @var Pdo\Connection
     */
    protected $connection = null;

    /**
     * @var Pdo\Statement
     */
    protected $statementPrototype = null;

    /**
     * @var Pdo\Result
     */
    protected $resultPrototype = null;

    /**
     * @param array|Pdo\Connection $connection
     * @param null|Pdo\Statement $statementPrototype
     * @param null|Pdo\Result $resultPrototype
     */
    public function __construct($connection, Pdo\Statement $statementPrototype = null, Pdo\Result $resultPrototype = null)
    {
        if (is_array($connection)) {
            $connection = new Pdo\Connection($connection);
        }

        if (!$connection instanceof Pdo\Connection) {
            throw new \InvalidArgumentException('$connection must be an array of parameters or a Pdo\Connection object');
        }

        $this->registerConnection($connection);
        $this->registerStatementPrototype(($statementPrototype) ?: new Pdo\Statement());
        $this->registerResultPrototype(($resultPrototype) ?: new Pdo\Result());
    }

    public function registerConnection(Pdo\Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setDriver($this);
        return $this;
    }

    public function registerStatementPrototype(Pdo\Statement $statementPrototype)
    {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this);
    }

    public function registerResultPrototype(Pdo\Result $resultPrototype)
    {
        $this->resultPrototype = $resultPrototype;
        $this->resultPrototype->setDriver($this);
    }

    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        // have to pull this from the dsn
        $connectionParameters = $this->getConnection()->getConnectionParams();
        list($type, $options) = preg_split('/:/', $connectionParameters['dsn'], 2);
        return ucwords($type);
    }
    
    public function checkEnvironment()
    {
        if (!extension_loaded('PDO')) {
            throw new \Exception('The PDO extension is required for this adapter but the extension is not loaded');
        }
    }

    /**
     * @return Pdo\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $sql
     * @return Pdo\Statement
     */
    public function createStatement($sql)
    {
        $statement = clone $this->statementPrototype;
        $statement->initialize($this->connection->getResource(), $sql);
        return $statement;
    }

    /**
     * @param resource $resource
     * @return Pdo\Result
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
