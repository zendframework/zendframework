<?php

namespace Zend\Db\RowGateway;

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\Row,
    Zend\Db\ResultSet\RowObjectInterface,
    Zend\Db\TableGateway\TableGateway;

class RowGateway implements RowGatewayInterface, RowObjectInterface
{
    protected $tableGateway = null;
    protected $primaryKey = null;

    protected $originalData = null;
    protected $currentData = null;

    public function __construct(TableGateway $tableGateway, $primaryKey)
    {
        $this->tableGateway = clone $tableGateway;
        $this->tableGateway->getSelectResultPrototype()->setReturnType(new Row());
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
        if (is_array($this->primaryKey)) {
            // @todo compound primary keys
        }

        if (isset($this->originalData[$this->primaryKey])) {
            // UPDATE
            $where = array($this->primaryKey => $this->originalData[$this->primaryKey]);
            $data = $this->currentData;
            unset($data[$this->primaryKey]);
            $rowsAffected = $this->tableGateway->update($data, $where);
        } else {
            // INSERT
            $rowsAffected = $this->tableGateway->insert($this->currentData);
            $primaryKey = $this->tableGateway->getLastInsertId();
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
        if (is_array($this->primaryKey)) {
            // @todo compound primary keys
        }

        $where = array($this->primaryKey => $this->originalData[$this->primaryKey]);
        return $this->tableGateway->delete($where);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->currentData);
    }

    public function offsetGet($offset)
    {
        return $this->currentData[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->currentData[$offset] = $value;
        return $this;
    }

    public function offsetUnset($offset)
    {
        $this->currentData[$offset] = null;
        return $this;
    }

    public function exchangeArray($input)
    {
        $this->originalData = $this->currentData = $input;
        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->currentData);
    }

    public function toArray()
    {
        return $this->currentData;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->currentData)) {
            return $this->currentData[$name];
        } else {
            throw new \InvalidArgumentException('Not a valid column in this row: ' . $name);
        }
    }
}