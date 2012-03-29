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
 * @property int $lastInsertId
 * @property string $tableName
 * @property Sql\Select $selectWhere
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
     * @var null|string
     */
    protected $schema = null;

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
     * @param string $schema
     * @param ResultSet $selectResultPrototype 
     */
    public function __construct($table, Adapter $adapter, ResultSet $selectResultPrototype = null, Sql\Sql $sql = null)
    {
        if (!(is_string($table) || $table instanceof Sql\TableIdentifier)) {
            throw new \InvalidArgumentException('Table name must be a string or an instance of Zend\Db\Sql\TableIdentifier');
        }
        $this->table = $table;
        $this->adapter = $adapter;

        $this->setSelectResultPrototype(($selectResultPrototype) ?: new ResultSet);
        $this->sql = ($sql) ?: new Sql\Sql($this->table);
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
     * @return type 
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
        if ($selectState['table'] != $this->tableName || $selectState['schema'] != $this->schema) {
            throw new \RuntimeException('The table name and schema of the provided select object must match that of the table');
        }

        $statement = $this->adapter->createStatement();
        $select->prepareStatement($this->adapter, $statement);

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
        $insert->into($this->tableName, $this->schema);
        $insert->values($set);

        $statement = $this->adapter->createStatement();
        $insert->prepareStatement($this->adapter, $statement);

        $result = $statement->execute();
        $this->lastInsertValue = $this->adapter->getDriver()->getConnection()->getLastGeneratedId();
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
        $update = $this->sql->update();
        $update->table($this->tableName, $this->schema);
        $update->set($set);
        $update->where($where);

        $statement = $this->adapter->createStatement();
        $update->prepareStatement($this->adapter, $statement);

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
        $delete->from($this->tableName, $this->schema);
        if ($where instanceof \Closure) {
            $where($delete);
        } else {
            $delete->where($where);
        }

        $statement = $this->adapter->createStatement();
        $delete->prepareStatement($this->adapter, $statement);

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
            case 'lastinsertid':
                return $this->lastInsertId;
            case 'adapter':
                return $this->adapter;
            case 'tablename':
                return $this->tableName;
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
