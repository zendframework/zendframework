<?php
/**
 * File DocBlock
 */

namespace Zend\Db\Adapter;

use Zend\Db\ResultSet;

/**
 * Class DocBlock
 *
 * @property Driver\DriverInterface $driver
 * @property Platform\PlatformInterface $platform
 */
class Adapter
{
    /**
     * Query Mode Constants
     */
    const QUERY_MODE_EXECUTE = 'execute';
    const QUERY_MODE_PREPARE = 'prepare';

    /**
     * Prepare Type Constants
     */
    const PREPARE_TYPE_POSITIONAL = 'positional';
    const PREPARE_TYPE_NAMED = 'named';

    const FUNCTION_FORMAT_PARAMETER_NAME = 'formatParameterName';
    const FUNCTION_QUOTE_IDENTIFIER = 'quoteIdentifier';
    const FUNCTION_QUOTE_VALUE = 'quoteValue';


    /**
     * @var Driver\DriverInterface
     */
    protected $driver = null;

    /**
     * @var Platform\PlatformInterface
     */
    protected $platform = null;

    /**
     * @var \Zend\Db\ResultSet\ResultSet
     */
    protected $queryResultSetPrototype = null;

    /**
     * @var string
     */
    protected $queryMode = self::QUERY_MODE_PREPARE;


    /**
     * @param Driver\DriverInterface|array $driver
     * @param Platform\PlatformInterface $platform
     * @param ResultSet\ResultSet $queryResultPrototype
     */
    public function __construct($driver, Platform\PlatformInterface $platform = null, ResultSet\ResultSet $queryResultPrototype = null)
    {
        if (is_array($driver)) {
            $driver = $this->createDriverFromParameters($driver);
        }

        if (!$driver instanceof Driver\DriverInterface) {
            throw new \InvalidArgumentException('Invalid driver');
        }

        $driver->checkEnvironment();
        $this->driver = $driver;

        if ($platform == null) {
            $platform = $this->createPlatformFromDriver($driver);
        }

        $this->platform = $platform;
        $this->queryResultSetPrototype = ($queryResultPrototype) ?: new ResultSet\ResultSet();
    }

    /**
     * @param array $parameters
     * @return Driver\DriverInterface
     * @throws \InvalidArgumentException
     */
    protected function createDriverFromParameters(array $parameters)
    {
        if (!isset($parameters['driver']) || !is_string($parameters['driver'])) {
            throw new \InvalidArgumentException('createDriverFromParameters() expects a "driver" key to be present inside the parameters');
        }

        $driverName = strtolower($parameters['driver']);
        switch ($driverName) {
            case 'mysqli':
                $driver = new Driver\Mysqli\Mysqli($parameters);
                break;
            case 'sqlsrv':
                $driver = new Driver\Sqlsrv\Sqlsrv($parameters);
                break;
            case 'pdo':
            default:
                if ($driverName == 'pdo' || strpos($driverName, 'pdo') === 0) {
                    $driver = new Driver\Pdo\Pdo($parameters);
                }
        }

        if (!isset($driver) || !$driver instanceof Driver\DriverInterface) {
            throw new \InvalidArgumentException('DriverInterface expected', null, null);
        }

        return $driver;
    }
    
    /**
     * getDriver()
     * 
     * @throws Exception
     * @return Driver\DriverInterface
     */
    public function getDriver()
    {
        if ($this->driver == null) {
            throw new \Exception('Driver has not been set or configured for this adapter.');
        }
        return $this->driver;
    }

    /**
     * @param string $queryMode
     * @return Adapter
     * @throws \InvalidArgumentException
     */
    public function setQueryMode($queryMode)
    {
        if (!in_array($queryMode, array(self::QUERY_MODE_EXECUTE, self::QUERY_MODE_PREPARE))) {
            throw new \InvalidArgumentException('mode must be one of query_execute or query_prepare');
        }
        
        $this->queryMode = $queryMode;
        return $this;
    }

    /**
     * @return Platform\PlatformInterface
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param Driver\DriverInterface $driver
     * @return Platform\PlatformInterface
     */
    protected function createPlatformFromDriver(Driver\DriverInterface $driver)
    {
        // consult driver for platform implementation
        $platformName = $driver->getDatabasePlatformName(Driver\DriverInterface::NAME_FORMAT_CAMELCASE);
        switch ($platformName) {
            case 'Mysql':
                return new Platform\Mysql();
            case 'SqlServer':
                return new Platform\SqlServer();
            case 'Sqlite':
                return new Platform\Sqlite();
            default:
                return new Platform\Sql92();
        }
    }

    public function getDefaultSchema()
    {
        return $this->driver->getConnection()->getDefaultSchema();
    }

    /**
     * query() is a convenience function
     *
     * @param string $sql
     * @param string|array $parametersOrPrepareExecuteFlag
     * @return Driver\StatementInterface
     */
    public function query($sql, $parametersOrPrepareExecuteFlag = self::QUERY_MODE_PREPARE)
    {
        if (is_string($parametersOrPrepareExecuteFlag) && in_array($parametersOrPrepareExecuteFlag, array(self::QUERY_MODE_PREPARE, self::QUERY_MODE_EXECUTE))) {
            $mode = $parametersOrPrepareExecuteFlag;
        } elseif (is_array($parametersOrPrepareExecuteFlag)) {
            $mode = self::QUERY_MODE_PREPARE;
            $parameters = $parametersOrPrepareExecuteFlag;
        } else {
            throw new \Exception('Parameter 2 to this method must be a flag or an array');
        }

        $c = $this->driver->getConnection();

        if ($mode == self::QUERY_MODE_PREPARE) {
            $statement = $c->prepare($sql);
            return $statement;
            // @todo determine if we fulfill the request
            // $result = $statement->execute($parameters);
        } else {
            $result = $c->execute($sql);
        }

        if ($result instanceof Driver\ResultInterface && $result->isQueryResult()) {
            $resultSet = clone $this->queryResultSetPrototype;
            $resultSet->setDataSource($result);
            return $resultSet;
        }

        return $result;
    }

    /**
     * @param $name
     * @return Driver\DriverInterface|Platform\PlatformInterface
     */
    public function __get($name)
    {
        switch (strtolower($name)) {
            case 'driver': return $this->driver;
            case 'platform': return $this->platform;
        }
        throw new \Exception('Invalid magic property on adapter');
    }

}
