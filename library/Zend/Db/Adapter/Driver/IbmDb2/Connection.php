<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Adapter\Driver\IbmDb2;

use Zend\Db\Adapter\Driver\ConnectionInterface;
use Zend\Db\Adapter\Exception;
use Zend\Db\Adapter\Profiler;

class Connection implements ConnectionInterface, Profiler\ProfilerAwareInterface
{
    /**
     *  @var IbmDb2
     */
    protected $driver = null;

    /**
     * @var array
     */
    protected $connectionParameters = null;

    /**
     * @var resource
     */
    protected $resource = null;

    /**
     * @var Profiler\ProfilerInterface
     */
    protected $profiler = null;

    /**
     * In transaction
     *
     * @var bool
     */
    protected $inTransaction = false;

    /**
     * i5 OS
     *
     * @var bool
     */
    protected $i5;

    /**
     * Previous autocommit set
     *
     * @var mixed
     */
    protected $prevAutocommit;

    /**
     * Constructor
     *
     * @param  array|resource|null                $connectionParameters (ibm_db2 connection resource)
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($connectionParameters = null)
    {
        if (is_array($connectionParameters)) {
            $this->setConnectionParameters($connectionParameters);
        } elseif (is_resource($connectionParameters)) {
            $this->setResource($connectionParameters);
        } elseif (null !== $connectionParameters) {
            throw new Exception\InvalidArgumentException(
                '$connection must be an array of parameters, a db2 connection resource or null'
            );
        }
    }

    /**
     * Set driver
     *
     * @param  IbmDb2     $driver
     * @return Connection
     */
    public function setDriver(IbmDb2 $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @param  Profiler\ProfilerInterface $profiler
     * @return Connection
     */
    public function setProfiler(Profiler\ProfilerInterface $profiler)
    {
        $this->profiler = $profiler;
        return $this;
    }

    /**
     * @return null|Profiler\ProfilerInterface
     */
    public function getProfiler()
    {
        return $this->profiler;
    }

    /**
     * @param  array      $connectionParameters
     * @return Connection
     */
    public function setConnectionParameters(array $connectionParameters)
    {
        $this->connectionParameters = $connectionParameters;
        return $this;
    }

    /**
     * @return array
     */
    public function getConnectionParameters()
    {
        return $this->connectionParameters;
    }

    /**
     * @param  resource   $resource DB2 resource
     * @return Connection
     */
    public function setResource($resource)
    {
        if (!is_resource($resource) || get_resource_type($resource) !== 'DB2 Connection') {
            throw new Exception\InvalidArgumentException('The resource provided must be of type "DB2 Connection"');
        }
        $this->resource = $resource;
        return $this;
    }

    /**
     * Get current schema
     *
     * @return string
     */
    public function getCurrentSchema()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $info = db2_server_info($this->resource);
        return (isset($info->DB_NAME) ? $info->DB_NAME : '');
    }

    /**
     * Get resource
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Connect
     *
     * @return self
     */
    public function connect()
    {
        if (is_resource($this->resource)) {
            return $this;
        }

        // localize
        $p = $this->connectionParameters;

        // given a list of key names, test for existence in $p
        $findParameterValue = function (array $names) use ($p) {
            foreach ($names as $name) {
                if (isset($p[$name])) {
                    return $p[$name];
                }
            }

            return null;
        };

        $database     = $findParameterValue(array('database', 'db'));
        $username     = $findParameterValue(array('username', 'uid', 'UID'));
        $password     = $findParameterValue(array('password', 'pwd', 'PWD'));
        $isPersistent = $findParameterValue(array('persistent', 'PERSISTENT', 'Persistent'));
        $options      = (isset($p['driver_options']) ? $p['driver_options'] : array());
        $connect      = ((bool) $isPersistent) ? 'db2_pconnect' : 'db2_connect';

        $this->resource = $connect($database, $username, $password, $options);

        if ($this->resource === false) {
            throw new Exception\RuntimeException(sprintf(
                '%s: Unable to connect to database',
                __METHOD__
            ));
        }

        return $this;
    }

    /**
     * Is connected
     *
     * @return bool
     */
    public function isConnected()
    {
        return ($this->resource !== null);
    }

    /**
     * Disconnect
     *
     * @return ConnectionInterface
     */
    public function disconnect()
    {
        if ($this->resource) {
            db2_close($this->resource);
            $this->resource = null;
        }

        return $this;
    }

    /**
     * Begin transaction
     *
     * @return ConnectionInterface
     */
    public function beginTransaction()
    {
        if ($this->isI5() && !ini_get('ibm_db2.i5_allow_commit')) {
            throw new Exception\RuntimeException(
                'DB2 transactions are not enabled, you need to set the ibm_db2.i5_allow_commit=1 in your php.ini'
            );
        }

        if (!$this->isConnected()) {
            $this->connect();
        }

        $this->prevAutocommit = db2_autocommit($this->resource);
        db2_autocommit($this->resource, DB2_AUTOCOMMIT_OFF);
        $this->inTransaction = true;
        return $this;
    }

    /**
     * In transaction
     *
     * @return bool
     */
    public function inTransaction()
    {
        return $this->inTransaction;
    }

    /**
     * Commit
     *
     * @return ConnectionInterface
     */
    public function commit()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        if (!db2_commit($this->resource)) {
            throw new Exception\RuntimeException("The commit has not been successful");
        }

        if ($this->prevAutocommit) {
            db2_autocommit($this->resource, $this->prevAutocommit);
        }

        $this->inTransaction = false;
        return $this;
    }

    /**
     * Rollback
     *
     * @return ConnectionInterface
     */
    public function rollback()
    {
        if (!$this->resource) {
            throw new Exception\RuntimeException('Must be connected before you can rollback.');
        }

        if (!$this->inTransaction) {
            throw new Exception\RuntimeException('Must call beginTransaction() before you can rollback.');
        }

        if (!db2_rollback($this->resource)) {
            throw new Exception\RuntimeException('The rollback has not been successful');
        }

        if ($this->prevAutocommit) {
            db2_autocommit($this->resource, $this->prevAutocommit);
        }

        $this->inTransaction = false;
        return $this;
    }

    /**
     * Execute
     *
     * @param  string $sql
     * @return Result
     */
    public function execute($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        if ($this->profiler) {
            $this->profiler->profilerStart($sql);
        }

        set_error_handler(function () {}, E_WARNING); // suppress warnings
        $resultResource = db2_exec($this->resource, $sql);
        restore_error_handler();

        if ($this->profiler) {
            $this->profiler->profilerFinish($sql);
        }

        // if the returnValue is something other than a pg result resource, bypass wrapping it
        if ($resultResource === false) {
            throw new Exception\InvalidQueryException(db2_stmt_errormsg());
        }

        return $this->driver->createResult(($resultResource === true) ? $this->resource : $resultResource);
    }

    /**
     * Get last generated id
     *
     * @param  null $name Ignored
     * @return int
     */
    public function getLastGeneratedValue($name = null)
    {
        return db2_last_insert_id($this->resource);
    }

    /**
     * Determine if the OS is OS400 (AS400, IBM i)
     *
     * @return bool
     */
    protected function isI5()
    {
        if (isset($this->i5)) {
            return $this->i5;
        }

        $this->i5 = php_uname('s') == 'OS400' ? true : false;
        return $this->i5;
    }
}
