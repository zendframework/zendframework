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

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\Row,
    Zend\Db\ResultSet\RowObjectInterface,
    Zend\Db\Sql;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage RowGateway
 */
class RowGateway implements RowGatewayInterface, RowObjectInterface
{

    protected $table = null;

    /**
     *
     * @var string
     */
    protected $primaryKey = null;

    /**
     *
     * @var array
     */
    protected $originalData = null;

    /**
     *
     * @var array
     */
    protected $data = null;

    /**
     * @var Sql
     */
    protected $sql = null;

    /**
     * Constructor
     * 
     * @param string $tableGateway
     * @param string|Sql\TableIdentifier $table
     * @param Adapter $adapter
     * @param Sql\Sql $sql
     */
    public function __construct($primaryKey, $table, Adapter $adapter = null, Sql\Sql $sql = null)
    {
        $this->primaryKey = $primaryKey;
        $this->table = $table;
        $this->sql = $sql ?: new Sql\Sql($adapter, $this->table);
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
     * Populate current data
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
     * Save
     * 
     * @return integer 
     */
    public function save()
    {
        if (is_array($this->primaryKey)) {
            // @todo compound primary keys
        }

        if (isset($this->originalData[$this->primaryKey])) {

            // UPDATE
            $where = array($this->primaryKey => $this->originalData[$this->primaryKey]);
            $data = $this->data;
            unset($data[$this->primaryKey]);

            $uStatement = $this->sql->prepareStatementFromSqlObject($this->sql->update()->set($data)->where($where));
            $result = $uStatement->execute();
            $rowsAffected = $result->getAffectedRows();

        } else {

            // INSERT
            $insert = $this->sql->insert();
            $insert->values($this->data);

            $statement = $this->sql->prepareStatementFromSqlObject($insert);

            $result = $statement->execute();
            $primaryKey = $result->getGeneratedValue();
            $rowsAffected = $result->getAffectedRows();
            $where = array($this->primaryKey => $primaryKey);
        }

        // refresh data
        $statement = $this->sql->prepareStatementFromSqlObject($this->sql->select()->where($where));
        $result = $statement->execute();
        $rowData = $result->current();
        $this->populateOriginalData($rowData);

        return $rowsAffected;
    }

    /**
     * Delete
     * 
     * @return type 
     */
    public function delete()
    {
        if (is_array($this->primaryKey)) {
            // @todo compound primary keys
        }

        $where = array($this->primaryKey => $this->originalData[$this->primaryKey]);
        return $this->tableGateway->delete($where);
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
     * @return type 
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * Offset set
     * 
     * @param  string $offset
     * @param  type $value
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
     * @return type 
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            throw new \InvalidArgumentException('Not a valid column in this row: ' . $name);
        }
    }
}
