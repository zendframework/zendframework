<?php
/**
 * File DocBlock
 */

namespace Zend\Db\Adapter;

use Zend\Db\ResultSet;

/**
 * Class DocBlock
 *
 * @property DriverInterface $driver
 * @property PlatformInterface $platform
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

    /**
     * Built-in namespaces
     */
    const BUILTIN_DRIVERS_NAMESPACE = 'Zend\Db\Adapter\Driver';
    const BUILTIN_PLATFORMS_NAMESPACE = 'Zend\Db\Adapter\Platform';

    const FUNCTION_FORMAT_PARAMETER_NAME = 'formatParameterName';
    const FUNCTION_QUOTE_IDENTIFIER = 'quoteIdentifier';
    const FUNCTION_QUOTE_VALUE = 'quoteValue';


    /**
     * @var DriverInterface
     */
    protected $driver = null;

    /**
     * @var PlatformInterface
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
     * @param DriverInterface|array $driver
     * @param PlatformInterface $platform
     * @param ResultSet\ResultSet $queryResultPrototype
     */
    public function __construct($driver, PlatformInterface $platform = null, ResultSet\ResultSet $queryResultPrototype = null)
    {
        if (is_array($driver)) {
            $driver = $this->createDriverFromParameters($driver);
        }

        if (!$driver instanceof DriverInterface) {
            throw new \InvalidArgumentException('Invalid driver');
        }

        $driver->checkEnvironment();
        $this->setDriver($driver);

        if ($platform == null) {
            $platform = $this->createPlatformFromDriver($driver);
        }

        $this->setPlatform($platform);

        $this->queryResultSetPrototype = ($queryResultPrototype) ?: new ResultSet\ResultSet();
    }

    /**
     * setDriver()
     * 
     * @param DriverInterface $driver
     * @return Adapter
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @param array $parameters
     * @return DriverInterface
     * @throws \InvalidArgumentException
     */
    public function createDriverFromParameters(array $parameters)
    {
        if (!isset($parameters['type']) || !is_string($parameters['type'])) {
            throw new \InvalidArgumentException('createDriverFromParameters() expects a "type" key to be present inside the parameters');
        }

        $className = $parameters['type'];
        if (strpos($className, '\\') === false) {
            $className = self::BUILTIN_DRIVERS_NAMESPACE . '\\' . $parameters['type'];
        }
        unset($parameters['type']);
        $driver = $className;

        if (is_string($driver) && class_exists($driver, true)) {
            $driver = new $driver($parameters);
        } else {
            throw new \InvalidArgumentException('Class by name ' . $driver . ' not found', null, null);
        }

        if (!$driver instanceof DriverInterface) {
            throw new \InvalidArgumentException('$driver provided is neither a driver class name or object of type DriverInterface', null, null);
        }

        return $driver;
    }
    
    /**
     * getDriver()
     * 
     * @throws Exception
     * @return DriverInterface
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
     * @param PlatformInterface $platform
     * @return Adapter
     */
    public function setPlatform(PlatformInterface $platform)
    {
        $this->platform = $platform;
        return $this;
    }

    /**
     * @return PlatformInterface
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param DriverInterface $driver
     * @return PlatformInterface
     */
    public function createPlatformFromDriver(DriverInterface $driver)
    {
        // consult driver for platform implementation
        $platform = $driver->getDatabasePlatformName(DriverInterface::NAME_FORMAT_CAMELCASE);
        if ($platform == '') {
            $platform = 'Sql92';
        }
        if ($platform{0} != '\\') {
            $platform = self::BUILTIN_PLATFORMS_NAMESPACE . '\\' . $platform;
        }
        return new $platform;
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
     * @return Zend\Db\Adapter\DriverStatement|
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

        //$resultSetProducing = (stripos(trim($sql), 'SELECT') === 0); // will this sql produce a rowset?

        if ($result instanceof DriverResultInterface && $result->isQueryResult()) {
            $resultSet = clone $this->queryResultSetPrototype;
            $resultSet->setDataSource($result);
            return $resultSet;
        }

        return $result;
    }

    /**
     * @param $name
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
