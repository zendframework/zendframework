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
 * @subpackage TableGateway
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\TableGateway;

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage TableGateway
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @property Adapter $adapter
 * @property int $lastInsertValue
 * @property string $tableName
 */
class TableGateway implements TableGatewayInterface
{

    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $adapter = null;

    /**
     * @var string
     */
    protected $table = null;

    /**
     * @var ResultSet
     */
    protected $selectResultPrototype = null;

    /**
     * @var Sql\Sql
     */
    protected $sql = null;

    /**
     *
     * @var integer
     */
    protected $lastInsertValue = null;

    /**
     * Constructor
     * 
     * @param string $table
     * @param Adapter $adapter
     * @param ResultSet $selectResultPrototype
     * @param Sql\Sql $selectResultPrototype
     */
    public function __construct($table, Adapter $adapter, ResultSet $selectResultPrototype = null, Sql\Sql $sql = null)
    {
        if (!(is_string($table) || $table instanceof Sql\TableIdentifier)) {
            throw new \InvalidArgumentException('Table name must be a string or an instance of Zend\Db\Sql\TableIdentifier');
        }
        $this->table = $table;
        $this->adapter = $adapter;

        $this->setSelectResultPrototype(($selectResultPrototype) ?: new ResultSet);
        $this->sql = ($sql) ?: new Sql\Sql($this->adapter, $this->table);
        if ($this->sql->getTable() != $this->table) {
            throw new Exception\InvalidArgumentException('The table inside the provided Sql object must match the table of this TableGateway');
        }
    }

    /**
     * Get table name
     * 
     * @return string 
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Get adapter
     * 
     * @return Adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Set select result prototype
     * 
     * @param null $selectResultPrototype
     */
    public function setSelectResultPrototype($selectResultPrototype)
    {
        $this->selectResultPrototype = $selectResultPrototype;
    }

    /**
     * Get select result prototype
     * 
     * @return ResultSet
     */
    public function getSelectResultPrototype()
    {
        return $this->selectResultPrototype;
    }

    /**
     * Select
     * 
     * @param string|array|\Closure $where
     * @return ResultSet
     */
    public function select($where = null)
    {
        $select = $this->sql->select();

        if ($where instanceof \Closure) {
            $where($select);
        } elseif ($where !== null) {
            $select->where($where);
        }

        return $this->selectWith($select);
    }

    /**
     * @param Sql\Select $select
     * @return null|ResultSet
     * @throws \RuntimeException
     */
    public function selectWith(Sql\Select $select)
    {
        $selectState = $select->getRawState();
        if ($selectState['table'] != $this->table) {
            throw new \RuntimeException('The table name of the provided select object must match that of the table');
        }

        $statement = $this->sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        $resultSet = clone $this->selectResultPrototype;
        $resultSet->setDataSource($result);
        return $resultSet;
    }

    /**
     * Insert
     * 
     * @param  array $set
     * @return int
     */
    public function insert($set)
    {
        $insert = $this->sql->insert();
        $insert->values($set);

        $statement = $this->sql->prepareStatementForSqlObject($insert);

        $result = $statement->execute();
        $this->lastInsertValue = $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        return $result->getAffectedRows();
    }

    /**
     * Update
     * 
     * @param  array $set
     * @param  string|array|closure $where
     * @return int
     */
    public function update($set, $where = null)
    {
        $sql = $this->sql;
        $update = $sql->update();
        $update->set($set);
        $update->where($where);

        $statement = $this->sql->prepareStatementForSqlObject($update);

        $result = $statement->execute();
        return $result->getAffectedRows();
    }

    /**
     * Delete
     * 
     * @param  Closure $where
     * @return int
     */
    public function delete($where)
    {
        $delete = $this->sql->delete();
        if ($where instanceof \Closure) {
            $where($delete);
        } else {
            $delete->where($where);
        }

        $statement = $this->sql->prepareStatementForSqlObject($delete);

        $result = $statement->execute();
        return $result->getAffectedRows();
    }

    /**
     * Get last insert value
     * 
     * @return integer 
     */
    public function getLastInsertValue()
    {
        return $this->lastInsertValue;
    }

    /**
     * __get
     * 
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        switch (strtolower($name)) {
            case 'lastinsertvalue':
                return $this->lastInsertValue;
            case 'adapter':
                return $this->adapter;
            case 'table':
                return $this->table;
        }
        throw new \Exception('Invalid magic property on adapter');
    }

    /**
     * __clone
     * 
     */
    public function __clone()
    {
        $this->selectResultPrototype = (isset($this->selectResultPrototype)) ? clone $this->selectResultPrototype : null;
        $this->sql = clone $this->sql;
        if (is_object($this->table)) {
            $this->table = clone $this->table;
        }
    }

}
