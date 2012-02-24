<?php

namespace Zend\Db\Adapter\Driver\Sqlsrv;

use Zend\Db\Adapter\Driver\ResultInterface;

class Result implements \Iterator, ResultInterface
{
    /**
     * @var resource
     */
    protected $resource = null;
    
    protected $currentData = false;
    
    protected $currentComplete = false;

    protected $position = -1;

    public function initialize($resource)
    {
        $this->resource = $resource;
        return $this;
    }
    
    public function getResource()
    {
        return $this->resource;
    }
    
    public function current()
    {
        if ($this->currentComplete) {
            return $this->currentData;
        }
        
        $this->load();
        return $this->currentData;
    }
    
    public function next()
    {
        $this->load();
        return true;
    }
    
    protected function load($row = SQLSRV_SCROLL_NEXT)
    {
        $this->currentData = sqlsrv_fetch_array($this->resource, SQLSRV_FETCH_ASSOC, $row);
        $this->currentComplete = true;
        $this->position++;
        return ($this->currentData);
    }
    
    public function key()
    {
        return $this->position;
    }
    
    public function rewind()
    {
        $this->position = 0;
        $this->load(SQLSRV_SCROLL_FIRST);
        return true;
    }
    
    public function valid()
    {
        if ($this->currentComplete && $this->currentData) {
            return true;
        }

        return $this->load();
    }
    
    public function count()
    {
        return sqlsrv_num_rows($this->resource);
    }

    public function isQueryResult()
    {
        if (is_bool($this->resource)) {
            return false;
        }
        return (sqlsrv_num_fields($this->resource) > 0);
    }

    public function getAffectedRows()
    {
        return sqlsrv_rows_affected($this->resource);
    }
}