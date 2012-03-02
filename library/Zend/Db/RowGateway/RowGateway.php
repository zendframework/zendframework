<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage RowGateway
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\RowGateway;

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\Row,
    Zend\Db\ResultSet\RowObjectInterface,
    Zend\Db\TableGateway\TableGateway;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage RowGateway
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RowGateway implements RowGatewayInterface, RowObjectInterface
{
    /**
     *
     * @var type 
     */
    protected $tableGateway = null;
    /**
     *
     * @var string
     */
    protected $primaryKey = null;
    /**
     *
     * @var type 
     */
    protected $originalData = null;
    /**
     *
     * @var type 
     */
    protected $currentData = null;

    /**
     * Constructor
     * 
     * @param TableGateway $tableGateway
     * @param type $primaryKey 
     */
    public function __construct(TableGateway $tableGateway, $primaryKey)
    {
        $this->tableGateway = clone $tableGateway;
        $this->tableGateway->getSelectResultPrototype()->setRowObjectPrototype(new Row());
        $this->primaryKey = $primaryKey;
    }

    /**
     * Populate Original Data
     * 
     * @param  type $originalData
     * @param  boolean $originalDataIsCurrent
     * @return RowGateway 
     */
    public function populateOriginalData($originalData, $originalDataIsCurrent = true)
    {
        $this->originalData = $originalData;
        if ($originalDataIsCurrent) {
            $this->populateCurrentData($originalData);
        }
        return $this;
    }
    /**
     * Populate current data
     * 
     * @param  type $currentData
     * @return RowGateway 
     */
    public function populateCurrentData($currentData)
    {
        $this->currentData = $currentData;
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
        return array_key_exists($offset, $this->currentData);
    }
    /**
     * Offset get
     * 
     * @param  string $offset
     * @return type 
     */
    public function offsetGet($offset)
    {
        return $this->currentData[$offset];
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
        $this->currentData[$offset] = $value;
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
        $this->currentData[$offset] = null;
        return $this;
    }
    /**
     * Exchange array
     * 
     * @param  string $input
     * @return RowGateway 
     */
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
    /**
     * To array
     * 
     * @return array 
     */
    public function toArray()
    {
        return $this->currentData;
    }
    /**
     * __get
     * 
     * @param  string $name
     * @return type 
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->currentData)) {
            return $this->currentData[$name];
        } else {
            throw new \InvalidArgumentException('Not a valid column in this row: ' . $name);
        }
    }
}