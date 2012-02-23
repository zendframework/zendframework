<?php

namespace Zend\Db\Adapter\Driver;

class Sqlsrv implements \Zend\Db\Adapter\DriverInterface
{

    /**
     * @var Sqlsrv\Connection
     */
    protected $connection = null;

    /**
     * @var Sqlsrv\Statement
     */
    protected $statementPrototype = null;

    /**
     * @var Sqlsrv\Result
     */
    protected $resultPrototype = null;

    /**
     * @param array|Sqlsrv\Connection $connection
     * @param null|Sqlsrv\Statement $statementPrototype
     * @param null|Sqlsrv\Result $resultPrototype
     */
    public function __construct($connection, Sqlsrv\Statement $statementPrototype = null, Sqlsrv\Result $resultPrototype = null)
    {
        if (is_array($connection)) {
            $connection = new Sqlsrv\Connection($connection);
        }

        if (!$connection instanceof Sqlsrv\Connection) {
            throw new \InvalidArgumentException('$connection must be an array of parameters or a Pdo\Connection object');
        }

        $this->registerConnection($connection);
        $this->registerStatementPrototype(($statementPrototype) ?: new Sqlsrv\Statement());
        $this->registerResultPrototype(($resultPrototype) ?: new Sqlsrv\Result());
    }

    public function registerConnection(Sqlsrv\Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setDriver($this);
        return $this;
    }

    public function registerStatementPrototype(Sqlsrv\Statement $statementPrototype)
    {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this);
    }

    public function registerResultPrototype(Sqlsrv\Result $resultPrototype)
    {
        $this->resultPrototype = $resultPrototype;
        $this->resultPrototype->setDriver($this);
    }

    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        if ($nameFormat == self::NAME_FORMAT_CAMELCASE) {
            return 'SqlServer';
        } else {
            return 'SQLServer';
        }
    }

    public function checkEnvironment()
    {
        if (!extension_loaded('sqlsrv')) {
            throw new \Exception('The Sqlsrv extension is required for this adapter but the extension is not loaded');
        }
    }

    /**
     * @return Sqlsrv\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $sql
     * @return Sqlsrv\Statement
     */
    public function createStatement($sql)
    {
        $statement = clone $this->statementPrototype;
        $statement->initialize($this->connection->getResource(), $sql);
        return $statement;
    }

    /**
     * @param resource $result
     * @return Sqlsrv\Result
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
