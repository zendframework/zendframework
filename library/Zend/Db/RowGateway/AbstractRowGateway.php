<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\RowGateway;

use ArrayAccess;
use Countable;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\TableIdentifier;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage RowGateway
 */
abstract class AbstractRowGateway implements ArrayAccess, Countable, RowGatewayInterface
{

    /**
     * @var bool
     */
    protected $isInitialized = false;

    /**
     * @var string|TableIdentifier
     */
    protected $table = null;

    /**
     * @var string
     */
    protected $primaryKeyColumn = null;

    /**
     * @var array
     */
    protected $originalData = null;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var Sql
     */
    protected $sql = null;

    /**
     * @var Feature\FeatureSet
     */
    protected $featureSet = null;

    /**
     * initialize()
     */
    public function initialize()
    {
        if ($this->isInitialized) {
            return;
        }

        if (!$this->featureSet instanceof Feature\FeatureSet) {
            $this->featureSet = new Feature\FeatureSet;
        }

        $this->featureSet->setRowGateway($this);
        $this->featureSet->apply('preInitialize', array());

        if (!is_string($this->table) && !$this->table instanceof TableIdentifier) {
            throw new Exception\RuntimeException('This row object does not have a valid table set.');
        }

        if ($this->primaryKeyColumn == null) {
            throw new Exception\RuntimeException('This row object does not have a primary key column set.');
        }

        if (!$this->sql instanceof Sql) {
            throw new Exception\RuntimeException('This row object does not have a Sql object set.');
        }

        $this->featureSet->apply('postInitialize', array());

        $this->isInitialized = true;
    }

    /**
     * Populate Original Data
     *
     * @param  array $originalData
     * @param  boolean $originalDataIsCurrent
     * @return RowGateway
     */
    public function populateOriginalData(array $originalData)
    {
        $this->originalData = $originalData;
        return $this;
    }

    /**
     * Populate Data
     *
     * @param  array $currentData
     * @return RowGateway
     */
    public function populate(array $rowData, $isOriginal = null)
    {
        $this->data = $rowData;
        if ($isOriginal == true || ($isOriginal == null && empty($this->originalData))) {
            $this->populateOriginalData($rowData);
        }

        return $this;
    }

    /**
     * @param mixed $array
     * @return array|void
     */
    public function exchangeArray($array)
    {
        return $this->populate($array, true);
    }

    /**
     * Save
     *
     * @return integer
     */
    public function save()
    {
        if (is_array($this->primaryKeyColumn)) {
            // @todo compound primary keys
            throw new Exception\RuntimeException('Compound primary keys are currently not supported, but are on the TODO list.');
        }

        if (isset($this->originalData[$this->primaryKeyColumn])) {

            // UPDATE
            $where = array($this->primaryKeyColumn => $this->originalData[$this->primaryKeyColumn]);
            $data = $this->data;
            unset($data[$this->primaryKeyColumn]);

            $statement = $this->sql->prepareStatementForSqlObject($this->sql->update()->set($data)->where($where));
            $result = $statement->execute();
            $rowsAffected = $result->getAffectedRows();
            unset($statement, $result); // cleanup

        } else {

            // INSERT
            $insert = $this->sql->insert();
            $insert->values($this->data);

            $statement = $this->sql->prepareStatementForSqlObject($insert);

            $result = $statement->execute();
            $primaryKeyValue = $result->getGeneratedValue();
            $rowsAffected = $result->getAffectedRows();
            unset($statement, $result); // cleanup

            $where = array($this->primaryKeyColumn => $primaryKeyValue);
        }

        // refresh data
        $statement = $this->sql->prepareStatementForSqlObject($this->sql->select()->where($where));
        $result = $statement->execute();
        $rowData = $result->current();
        unset($statement, $result); // cleanup

        // make sure data and original data are in sync after save
        $this->populate($rowData, true);

        // return rows affected
        return $rowsAffected;
    }

    /**
     * Delete
     *
     * @return void
     */
    public function delete()
    {
        if (is_array($this->primaryKeyColumn)) {
            // @todo compound primary keys
        }

        $where = array($this->primaryKeyColumn => $this->originalData[$this->primaryKeyColumn]);
        //return $this->tableGateway->delete($where);
    }

    /**
     * Offset Exists
     *
     * @param  string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * Offset get
     *
     * @param  string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * Offset set
     *
     * @param  string $offset
     * @param  mixed $value
     * @return RowGateway
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
        return $this;
    }

    /**
     * Offset unset
     *
     * @param  string $offset
     * @return RowGateway
     */
    public function offsetUnset($offset)
    {
        $this->data[$offset] = null;
        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * To array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * __get
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            throw new \InvalidArgumentException('Not a valid column in this row: ' . $name);
        }
    }

    /**
     * __set
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * __isset
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * __unset
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        $this->offsetUnset($name);
    }
}
