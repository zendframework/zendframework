<?php

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\DriverInterface,
    Zend\Db\Adapter\DriverResultInterface,
    Iterator,
    PDO,
    PDOStatement;

/**
 * Resultset for PDO
 *
 * @todo Use PDO's native interface for fetching into named objects?
 */
class Result implements Iterator, DriverResultInterface
{

    const STATEMENT_MODE_SCROLLABLE = 'scrollable';
    const STATEMENT_MODE_FORWARD    = 'forward';

    protected $statementMode = self::STATEMENT_MODE_FORWARD;

    /**
     * @var Zend\Db\Adapter\Driver\AbstractDriver
     */
    protected $driver = null;
    
    /**
     * @var \PDOStatement
     */
    protected $resource = null;

    /**
     * @var array Result options
     */
    protected $options;

    /**
     * Is the current complete?
     * @var bool
     */
    protected $currentComplete = false;

    /**
     * Track current item in recordset
     * @var mixed
     */
    protected $currentData;

    /**
     * Current position of scrollable statement
     * @var int
     */
    protected $position = -1;

    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    public function initialize(PDOStatement $resource)
    {
        $this->resource = $resource;
        return $this;
    }

    public function getResource()
    {
        return $this->resource;
    }
    
    /**
     * @todo Should we allow passing configuration flags to the fetch() call?
     */
    public function current()
    {
        if ($this->currentComplete) {
            return $this->currentData;
        }

        $this->currentData = $this->resource->fetch(PDO::FETCH_ASSOC);
        return $this->currentData;
    }
    
    public function next()
    {
        $this->currentData = $this->resource->fetch(PDO::FETCH_ASSOC);
        $this->currentComplete = true;
        $this->position++;
        return $this->currentData;
    }
    
    public function key()
    {
        return $this->position;
    }

    /**
     * @return void
     */
    public function rewind()
    {
        if ($this->statementMode == self::STATEMENT_MODE_FORWARD && $this->position > 0) {
            throw new \Exception('This result is a forward only result set, calling rewind() after moving forward is not supported');
        }
        $this->currentData = $this->resource->fetch(PDO::FETCH_ASSOC);
        $this->currentComplete = true;
        $this->position = 0;
    }
    
    public function valid()
    {
        return ($this->currentData != false);
    }
    
    public function count()
    {
        return $this->resource->rowCount();
    }

    public function isQueryResult()
    {
        return ($this->resource->columnCount() > 0);
    }

    public function getAffectedRows()
    {
        return $this->resource->rowCount();
    }
}
