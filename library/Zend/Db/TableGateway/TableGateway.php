<?php

namespace Zend\Db\TableGateway;

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Insert,
    Zend\Db\Sql\Update,
    Zend\Db\Sql\Delete,
    Zend\Db\Sql\Select;

/**
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


    protected $lastInsertId = null;


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

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @return null|string
     */
    public function getDatabaseSchema()
    {
        return $this->databaseSchema;
    }

    /**
     * @param Delete $sqlDelete
     */
    public function setSqlDelete(Delete $sqlDelete)
    {
        $this->sqlDelete = $sqlDelete;
    }

    /**
     * @return Delete
     */
    public function getSqlDelete()
    {
        return $this->sqlDelete;
    }

    /**
     * @param Insert $sqlInsert
     */
    public function setSqlInsert(Insert $sqlInsert)
    {
        $this->sqlInsert = $sqlInsert;
    }

    /**
     * @return Insert
     */
    public function getSqlInsert()
    {
        return $this->sqlInsert;
    }

    /**
     * @param Select $sqlSelect
     */
    public function setSqlSelect(Select $sqlSelect)
    {
        $this->sqlSelect = $sqlSelect;
    }

    /**
     * @return Select
     */
    public function getSqlSelect()
    {
        return $this->sqlSelect;
    }

    /**
     * @param Update $sqlUpdate
     */
    public function setSqlUpdate(Update $sqlUpdate)
    {
        $this->sqlUpdate = $sqlUpdate;
    }

    /**
     * @return Update
     */
    public function getSqlUpdate()
    {
        return $this->sqlUpdate;
    }

    /**
     * @param null $selectResultPrototype
     */
    public function setSelectResultPrototype($selectResultPrototype)
    {
        $this->selectResultPrototype = $selectResultPrototype;
    }

    public function getSelectResultPrototype()
    {
        return $this->selectResultPrototype;
    }

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

    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }

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

    public function __clone()
    {
        $this->selectResultPrototype = clone $this->selectResultPrototype;
        $this->sqlSelect = clone $this->sqlSelect;
        $this->sqlInsert = clone $this->sqlInsert;
        $this->sqlUpdate = clone $this->sqlUpdate;
        $this->sqlDelete = clone $this->sqlDelete;
    }

}
