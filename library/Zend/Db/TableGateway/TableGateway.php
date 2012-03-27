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
    Zend\Db\Sql\Insert,
    Zend\Db\Sql\Update,
    Zend\Db\Sql\Delete,
    Zend\Db\Sql\Select;

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
 * @property Select $selectWhere
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
    protected $tableName = null;

    /**
     * @var null|string
     */
    protected $databaseSchema = null;

    /**
     * @var ResultSet
     */
    protected $selectResultPrototype = null;

    /**
     * @var Select
     */
    protected $sqlSelect = null;

    /**
     * @var Insert
     */
    protected $sqlInsert = null;

    /**
     * @var Update
     */
    protected $sqlUpdate = null;

    /**
     * @var Delete
     */
    protected $sqlDelete = null;

    /**
     *
     * @var integer
     */
    protected $lastInsertId = null;

    /**
     * Constructor
     * 
     * @param string $tableName
     * @param Adapter $adapter
     * @param string $databaseSchema
     * @param ResultSet $selectResultPrototype 
     */
    public function __construct($tableName, Adapter $adapter, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        if (!is_string($tableName)) {
            throw new \InvalidArgumentException('Table name must be a string');
        }
        $this->tableName = $tableName;
        $this->adapter = $adapter;

        // perhaps this might be useful, but not right now, the primary injection is ctor injection
        // $this->setTableName($tableName);
        // $this->setAdapter($adapter);

        if (is_string($databaseSchema)) {
            $this->databaseSchema = $databaseSchema;
        }
        $this->setSelectResultPrototype(($selectResultPrototype) ?: new ResultSet);
        $this->initializeSqlObjects();
    }
    /**
     * Get table name
     * 
     * @return string 
     */
    public function getTableName()
    {
        return $this->tableName;
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
     * Get database schema
     * 
     * @return null|string
     */
    public function getDatabaseSchema()
    {
        return $this->databaseSchema;
    }

    /**
     * Set sql delete
     * 
     * @param Delete $sqlDelete
     */
    public function setSqlDelete(Delete $sqlDelete)
    {
        $this->sqlDelete = $sqlDelete;
    }

    /**
     * Get sql delete
     * 
     * @return Delete
     */
    public function getSqlDelete()
    {
        return $this->sqlDelete;
    }

    /**
     * Set sql insert
     * 
     * @param Insert $sqlInsert
     */
    public function setSqlInsert(Insert $sqlInsert)
    {
        $this->sqlInsert = $sqlInsert;
    }

    /**
     * Get sql insert
     * 
     * @return Insert
     */
    public function getSqlInsert()
    {
        return $this->sqlInsert;
    }

    /**
     * Set sql select
     * 
     * @param Select $sqlSelect
     */
    public function setSqlSelect(Select $sqlSelect)
    {
        $this->sqlSelect = $sqlSelect;
    }

    /**
     * Get sql select
     * 
     * @return Select
     */
    public function getSqlSelect()
    {
        return $this->sqlSelect;
    }

    /**
     * Set sql update
     * 
     * @param Update $sqlUpdate
     */
    public function setSqlUpdate(Update $sqlUpdate)
    {
        $this->sqlUpdate = $sqlUpdate;
    }

    /**
     * Get sql update
     * 
     * @return Update
     */
    public function getSqlUpdate()
    {
        return $this->sqlUpdate;
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
     * @return type 
     */
    public function getSelectResultPrototype()
    {
        return $this->selectResultPrototype;
    }
    /**
     * Select
     * 
     * @param Closure $where
     * @return type 
     */
    public function select($where = null)
    {
        $select = clone $this->sqlSelect;
        $select->from($this->tableName, $this->databaseSchema);
        if ($where instanceof \Closure) {
            $where($select);
        } elseif ($where !== null) {
            $select->where($where);
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
     * @param  type $set
     * @return type 
     */
    public function insert($set)
    {
        $insert = clone $this->sqlInsert;
        $insert->into($this->tableName, $this->databaseSchema);
        $insert->values($set);

        $statement = $this->adapter->createStatement();
        $insert->prepareStatement($this->adapter, $statement);

        $result = $statement->execute();
        $this->lastInsertId = $this->adapter->getDriver()->getConnection()->getLastGeneratedId();
        return $result->getAffectedRows();
    }
    /**
     * Update
     * 
     * @param  type $set
     * @param  string $where
     * @return type 
     */
    public function update($set, $where = null)
    {
        $update = clone $this->sqlUpdate;
        $update->table($this->tableName, $this->databaseSchema);
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
     * @return type 
     */
    public function delete($where)
    {
        $delete = clone $this->sqlDelete;
        $delete->from($this->tableName, $this->databaseSchema);
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
     * Get last insert id
     * 
     * @return integer 
     */
    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }
    /**
     * __get
     * 
     * @param  string $name
     * @return type 
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
     * Initialize sql objects
     */
    protected function initializeSqlObjects()
    {
        if (!$this->sqlSelect) {
            $this->sqlSelect = new Select();
        }
        if (!$this->sqlInsert) {
            $this->sqlInsert = new Insert();
        }
        if (!$this->sqlUpdate) {
            $this->sqlUpdate = new Update();
        }
        if (!$this->sqlDelete) {
            $this->sqlDelete = new Delete();
        }
    }
    /**
     * __clone
     * 
     */
    public function __clone()
    {
        $this->selectResultPrototype = clone $this->selectResultPrototype;
        $this->sqlSelect = clone $this->sqlSelect;
        $this->sqlInsert = clone $this->sqlInsert;
        $this->sqlUpdate = clone $this->sqlUpdate;
        $this->sqlDelete = clone $this->sqlDelete;
    }

}
