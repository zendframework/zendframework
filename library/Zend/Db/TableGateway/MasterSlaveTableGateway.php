<?php

namespace Zend\Db\TableGateway;

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet;

class MasterSlaveTableGateway extends TableGateway
{
    /**
     * @var Adapter
     */
    protected $slaveAdapter = null;

    /**
     * @var Adapter
     */
    protected $masterAdapter = null;

    public function __construct($tableName, Adapter $masterAdapter, Adapter $slaveAdapter, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        $this->masterAdapter = $masterAdapter;
        $this->slaveAdapter = $slaveAdapter;

        // initialize adapter to masterAdapter
        parent::__construct($tableName, $masterAdapter, $databaseSchema, $selectResultPrototype);
    }


    public function select($where)
    {
        $this->adapter = $this->slaveAdapter;
        return parent::select($where);
    }

    public function insert($set)
    {
        $this->adapter = $this->masterAdapter;
        return parent::insert($set);
    }

    public function update($set, $where)
    {
        $this->adapter = $this->masterAdapter;
        return parent::update($set, $where);
    }

    public function delete($where)
    {
        $this->adapter = $this->masterAdapter;
        return parent::delete($where);
    }

}