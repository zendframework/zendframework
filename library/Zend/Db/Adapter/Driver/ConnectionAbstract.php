<?php
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
     * Checks whether the connection is in transaction state.
     *
     * @return boolean
     */
    public function isInTransaction()
    {
        return $this->inTransaction;
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
