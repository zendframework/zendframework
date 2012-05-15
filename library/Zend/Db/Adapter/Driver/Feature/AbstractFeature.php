<?php

namespace Zend\Db\Adapter\Driver\Feature;

use Zend\Db\Adapter\Driver\DriverInterface;

abstract class AbstractFeature
{

    /**
     * @var DriverInterface
     */
    protected $driver = null;

    /**
     * @param DriverInterface $pdoDriver
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    abstract public function getName();

}
