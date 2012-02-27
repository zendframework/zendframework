<?php

namespace Zend\Db\TableGateway;

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet;

abstract class AbstractTableGateway extends TableGateway
{
    protected $initialized = false;
    protected $lazyInitialize = false;

    public function __construct()
    {
        $this->setup();
        if ($this->lazyInitialize != true) {
            $this->initialize();
        }
    }

    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @abstract
     */
    public function setup()
    {
        // filled in by the child class
    }

    public function initialize()
    {
        if ($this->initialized == true) {
            return;
        }

        if ($this->tableName == null) {
            throw new \Exception('$tableName must be configured in initialize()');
        }

        if (!$this->adapter) {
            // what to do?
        }


        if ($this->databaseSchema == null) {
            $this->databaseSchema = $this->adapter->getDefaultSchema();
        }

        if (!$this->selectResultPrototype) {
            $this->setSelectResultPrototype(new ResultSet);
        }

        $this->initializeSqlObjects();
        $this->initialized = true;
    }

    public function select($where = null)
    {
        $this->initialize();
        return parent::select($where);
    }

    public function insert($set)
    {
        $this->initialize();
        return parent::insert($set);
    }

    public function update($set, $where)
    {
        $this->initialize();
        return parent::update($set, $where);
    }

    public function delete($where)
    {
        $this->initialize();
        return parent::delete($where);
    }



}
