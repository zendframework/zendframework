<?php

namespace Zend\Db\TableGateway;

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\ResultSet\ResultSetInterface,
    Zend\Db\Sql\Insert,
    Zend\Db\Sql\Update,
    Zend\Db\Sql\Delete,
    Zend\Db\Sql\Select;

class TableGateway implements TableGatewayInterface
{
    const USE_STATIC_ADAPTER = null;

    /**
     * @var \Zend\Db\Adapter\Adapter[]
     */
    protected static $staticAdapters = array();

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

    public static function setStaticAdapter(Adapter $adapter)
    {
        $class = get_called_class();

        static::$staticAdapters[$class] = $adapter;
        if ($class === __CLASS__) {
            static::$staticAdapters[__CLASS__] = $adapter;
        }
    }

    public static function getStaticAdapter()
    {
        $class = get_called_class();

        // class specific adapter
        if (isset(static::$staticAdapters[$class])) {
            return static::$staticAdapters[$class];
        }

        // default adapter
        if (isset(static::$staticAdapters[__CLASS__])) {
            return static::$staticAdapters[__CLASS__];
        }

        throw new \Exception('No database adapter was found.');
    }

    public function __construct($tableName, Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        $this->setTableName($tableName);

        if ($adapter === self::USE_STATIC_ADAPTER) {
            $adapter = static::getStaticAdapter();
        }

        $this->setAdapter($adapter);

        if (is_string($databaseSchema)) {
            $this->databaseSchema = $databaseSchema;
        }
        $this->setSelectResultPrototype(($selectResultPrototype) ?: new ResultSet);

        $this->sqlSelect = new Select();
        $this->sqlInsert = new Insert();
        $this->sqlUpdate = new Update();
        $this->sqlDelete = new Delete();
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    public function getAdapter()
    {
        return $this->adapter;
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

    public function select($where)
    {
        $select = clone $this->sqlSelect;
        $select->from($this->tableName, $this->databaseSchema);
        $select->where($where);

        $statement = $select->getParameterizedSqlString($this->adapter);
        $result = $statement->execute($select->getParameterContainer());
        $resultSet = clone $this->selectResultPrototype;
        $resultSet->setDataSource($result);
        return $resultSet;
    }

    public function insert($set)
    {
        $insert = clone $this->sqlInsert;
        $insert->into($this->tableName, $this->databaseSchema);
        $insert->values($set);

        $statement = $insert->getParameterizedSqlString($this->adapter);
        $result = $statement->execute($insert->getParameterContainer());
        return $result->getAffectedRows();
    }

    public function update($set, $where)
    {
        $update = clone $this->sqlUpdate;
        $update->table($this->tableName, $this->databaseSchema);
        $update->set($set);
        $update->where($where);

        $statement = $update->getParameterizedSqlString($this->adapter);
        $result = $statement->execute($update->getParameterContainer());
        return $result->getAffectedRows();
    }

    public function delete($where)
    {
        $delete = clone $this->sqlDelete;
        $delete->from($this->tableName, $this->databaseSchema);
        $delete->where($where);

        $statement = $delete->getParameterizedSqlString($this->adapter);
        $result = $statement->execute($delete->getParameterContainer());
        return $result->getAffectedRows();
    }


}
