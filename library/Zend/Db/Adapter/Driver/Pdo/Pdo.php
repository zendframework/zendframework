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

use Zend\Db\Adapter\Driver\DriverInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
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

    /**
     * Register connection
     * 
     * @param  Connection $connection
     * @return Pdo 
     */
    public function registerConnection(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setDriver($this);
        return $this;
    }
    /**
     * Register statement prototype
     * 
     * @param Statement $statementPrototype 
     */
    public function registerStatementPrototype(Statement $statementPrototype)
    {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this);
    }
    /**
     * Register result prototype
     * 
     * @param Result $resultPrototype 
     */
    public function registerResultPrototype(Result $resultPrototype)
    {
        $this->resultPrototype = $resultPrototype;
    }
    /**
     * Get database platform name
     * 
     * @param  string $nameFormat
     * @return string 
     */
    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        $name = $this->getConnection()->getResource()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        if ($nameFormat == self::NAME_FORMAT_CAMELCASE) {
            return ucfirst($name);
        } else {
            switch ($name) {
                case 'sqlite':
                    return 'SQLite';
                case 'mysql':
                    return 'MySQL';
                default:
                    return ucfirst($name);
            }
        }
    }
    /**
     * Check environment
     */
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
    public function createStatement($sqlOrResource = null)
    {
        $statement = clone $this->statementPrototype;
        if (is_string($sqlOrResource)) {
            $statement->setSql($sqlOrResource);
        } elseif ($sqlOrResource instanceof \PDOStatement) {
            $statement->setResource($sqlOrResource);
        }
        $statement->initialize($this->connection->getResource());
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
    public function getPrepareType()
    {
        return self::PARAMETERIZATION_NAMED;
    }

    /**
     * @param string $name
     * @param string|null $type
     * @return string
     */
    public function formatParameterName($name, $type = null)
    {
        if ($type == null && !is_numeric($name) || $type == self::PARAMETERIZATION_NAMED) {
            return ':' . $name;
        } else {
            return '?';
        }
    }

}
