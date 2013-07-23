<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Adapter\Driver;

use Zend\Db\Adapter\Profiler;

abstract class ConnectionAbstract implements ConnectionInterface, Profiler\ProfilerAwareInterface
{
    /**
     * @var array
     */
    protected $connectionParameters = array();

    /**
     * @var string
     */
    protected $driverName = null;

    /**
     * @var boolean
     */
    protected $inTransaction = false;

    /**
     * @var Profiler\ProfilerInterface
     */
    protected $profiler = null;

    /**
     * @var resource
     */
    protected $resource = null;

    /**
     * Disconnect
     *
     * @return Connection
     */
    public function disconnect()
    {
        if ($this->isConnected()) {
            $this->resource = null;
        }

        return $this;
    }

    /**
     * Get connection parameters
     *
     * @return array
     */
    public function getConnectionParameters()
    {
        return $this->connectionParameters;
    }

    /**
     * Get driver name
     *
     * @return null|string
     */
    public function getDriverName()
    {
        return $this->driverName;
    }

    /**
     * @return null|Profiler\ProfilerInterface
     */
    public function getProfiler()
    {
        return $this->profiler;
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
     * Checks whether the connection is in transaction state.
     *
     * @return boolean
     */
    public function inTransaction()
    {
        return $this->inTransaction;
    }

    /**
     * @param array $connectionParameters
     * @return Connection
     */
    public function setConnectionParameters(array $connectionParameters)
    {
        $this->connectionParameters = $connectionParameters;

        return $this;
    }

    /**
     * @param Profiler\ProfilerInterface $profiler
     * @return Connection
     */
    public function setProfiler(Profiler\ProfilerInterface $profiler)
    {
        $this->profiler = $profiler;

        return $this;
    }
}
