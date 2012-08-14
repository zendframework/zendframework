<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter\Driver\Pgsql;

use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Exception;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
class Pgsql implements DriverInterface
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
     * @var array
     */
    protected $options = array(
        'buffer_results' => false
    );

    /**
     * @param array|Connection|resource $connection
     * @param null|Statement $statementPrototype
     * @param null|Result $resultPrototype
     * @param array $options
     */
    public function __construct($connection, Statement $statementPrototype = null, Result $resultPrototype = null, $options = null)
    {
        if (!$connection instanceof Connection) {
            $connection = new Connection($connection);
        }

        $this->registerConnection($connection);
        $this->registerStatementPrototype(($statementPrototype) ?: new Statement());
        $this->registerResultPrototype(($resultPrototype) ?: new Result());
    }

    /**
     * @param Connection $connection
     * @return Pgsql
     */
    public function registerConnection(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setDriver($this);
        return $this;
    }

    /**
     * @param Statement $statement
     * @return Pgsql
     */
    public function registerStatementPrototype(Statement $statement)
    {
        $this->statementPrototype = $statement;
        $this->statementPrototype->setDriver($this); // needs access to driver to createResult()
        return $this;
    }

    /**
     * @param Result $result
     * @return Pgsql
     */
    public function registerResultPrototype(Result $result)
    {
        $this->resultPrototype = $result;
        return $this;
    }

    /**
     * @param string $nameFormat
     * @return string
     */
    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        if ($nameFormat == self::NAME_FORMAT_CAMELCASE) {
            return 'Postgresql';
        } else {
            return 'PostgreSQL';
        }
    }

    /**
     * @return bool
     */
    public function checkEnvironment()
    {
        if (!extension_loaded('mysqli')) {
            throw new Exception\RuntimeException('The Mysqli extension is required for this adapter but the extension is not loaded');
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
     * @param string|null $sqlOrResource
     * @return Statement
     */
    public function createStatement($sqlOrResource = null)
    {
        /*
        if (is_resource($sqlOrResource) && !in_array($sqlOrResource, $this->resources, true)) {
            $this->resources[] = $sqlOrResource;
        }
        */

        $statement = clone $this->statementPrototype;
        if (is_string($sqlOrResource)) {
            $statement->setSql($sqlOrResource);
        }

        /* elseif ($sqlOrResource instanceof \mysqli_stmt) {
            $statement->setResource($sqlOrResource);
        }
        */

        $statement->initialize($this->connection->getResource());
        return $statement;
    }

    /**
     * @return Result
     */
    public function createResult($resource)
    {
        $result = clone $this->resultPrototype;
        $result->initialize($resource, $this->connection->getLastGeneratedValue());
        return $result;
    }

    /**
     * @return array
     */
    public function getPrepareType()
    {
        return self::PARAMETERIZATION_POSITIONAL;
    }

    /**
     * @param string $name
     * @param mixed  $type
     * @return string
     */
    public function formatParameterName($name, $type = null)
    {
        return '$#';
    }

    /**
     * @return mixed
     */
    public function getLastGeneratedValue()
    {
        return $this->connection->getLastGeneratedValue();
    }
}
