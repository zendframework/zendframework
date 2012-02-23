<?php

namespace Zend\Db\RowGateway;

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\ResultSet\ResultSetInterface,
    Zend\Db\TableGateway\TableGateway;

class RowGateway implements RowGatewayInterface
{
    protected $tableGateway = null;
    protected $primaryKey = null;

    protected $originalData = null;
    protected $currentData = null;

    public function __construct(TableGateway $tableGateway, $primaryKey)
    {
        $this->tableGateway = $tableGateway;
        $this->primaryKey = $primaryKey;
    }

    public function populateOriginalData($originalData, $originalDataIsCurrent = true)
    {
        $this->originalData = $originalData;
        if ($originalDataIsCurrent) {
            $this->populateCurrentData($originalData);
        }
        return $this;
    }

    public function populateCurrentData($currentData)
    {
        $this->currentData = $currentData;
        return $this;
    }

    public function save()
    {
        if (is_array($this->primaryId)) {
            // @todo compound primary keys
        }

        if (isset($this->originalData[$this->primaryKey])) {
            // UPDATE
            $where = array($this->primaryKey => $this->originalData[$this->primaryKey]);
            $rowsAffected = $this->tableGateway->update($this->currentData, $where);
        } else {
            // INSERT
            $rowsAffected = $this->tableGateway->insert($this->currentData);

            // @todo is there a better way to do this?
            $primaryKey = $this->tableGateway->getAdapter()->getDriver()->getConnection()->getLastGeneratedId();
            $where = array($this->primaryKey => $primaryKey);
        }

        // refresh data
        $result = $this->tableGateway->select($where);
        $rowData = $result->current();
        $this->populateOriginalData($rowData);

        return $rowsAffected;
    }

    public function delete()
    {
        if (is_array($this->primaryId)) {
            // @todo compound primary keys
        }

        $where = array($this->primaryKey => $this->originalData[$this->primaryKey]);
        return $this->tableGateway->delete($where);
    }


}