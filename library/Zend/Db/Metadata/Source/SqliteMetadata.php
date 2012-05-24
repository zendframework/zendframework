<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Metadata\Source;

use Zend\Db\Metadata\MetadataInterface,
    Zend\Db\Adapter\Adapter,
    Zend\Db\Metadata\Object;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Metadata
 */
class SqliteMetadata implements MetadataInterface
{

    /**
     *
     * @var Adapter
     */
    protected $adapter = null;

    /**
     *
     * @var string
     */
    protected $defaultSchema = null;

    /**
     *
     * @var array
     */
    protected $tableData = array();

    /**
     *
     * @var array
     */
    protected $constraintData = array();

    /**
     * Constructor
     * 
     * @param Adapter $adapter 
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Get schemas
     * 
     * @return null 
     */
    public function getSchemas()
    {
        return null;
    }

    /**
     * Get table names
     * 
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getTableNames($schema = null)
    {
        if ($this->tableData == null) {
            $this->loadTableColumnData();
        }

        $tables = array();
        foreach ($this->tableData as $tableName => $columns) {
            $tables[] = $tableName;
        }
        return $tables;
    }

    /**
     * Get tables
     * 
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getTables($schema = null)
    {
        $tables = array();
        foreach ($this->getTableNames() as $table) {
            $tables[] = $this->getTable($table);
        }
        return $tables;
    }

    /**
     * Get table
     * 
     * @param  string $table
     * @param  string $schema
     * @param  string $database
     * @return Object\TableObject 
     */
    public function getTable($table, $schema = null)
    {
        $tableObj = new Object\TableObject($table);
        $tableObj->setColumns($this->getColumns($table));
        $tableObj->setConstraints($this->getConstraints($table, $schema));

        return $tableObj;
    }

    /**
     * Get views
     * 
     * @param string $schema
     * @param string $database 
     */
    public function getViews($schema = null)
    {

    }

    /**
     * Get column names
     * 
     * @param string $table
     * @param string $schema
     * @param string $database 
     */
    public function getColumnNames($table, $schema = null)
    {
        if ($this->tableData == null) {
            $this->loadTableColumnData();
        }

        if (!isset($this->tableData[$table])) {
            throw new Exception\InvalidArgumentException('Invalid table name provided');
        }

        $names = array();
        foreach ($this->tableData[$table] as $columnData) {
            $names[] = $columnData['name'];
        }
        return $names;
    }

    /**
     * Get column
     * 
     * @param  string $columnName
     * @param  string $table
     * @param  string $schema
     * @param  string $database
     * @return Object\ColumnObject 
     */
    public function getColumn($columnName, $table, $schema = null)
    {
        $sql = 'PRAGMA table_info("' . $table . '")';
        $rows = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $found = false;
        foreach ($rows->toArray() as $row) {
            if ($row['name'] == $columnName) {
                $found = $row;
                break;
            }
        }

        if ($found == false) {
            throw new \Exception('Column not found');
        }

        $column = new Object\ColumnObject($found['name'], $table);
        $column->setOrdinalPosition($found['cid']);
        $column->setDataType($found['type']);
        $column->setIsNullable(!((bool) $found['notnull']));
        $column->setColumnDefault($found['dflt_value']);
        return $column;
    }

    /**
     * Get columns
     * 
     * @param  string $table
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getColumns($table, $schema = null)
    {
        $sql = 'PRAGMA table_info("' . $table . '")';
        $rows = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        $columns = array();
        foreach ($rows as $row) {
            $columns[] = $this->getColumn($row['name'], $table, $schema);
        }
        return $columns;
    }

    /**
     * Get constraints
     * 
     * @param  string $table
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getConstraints($table, $schema = null)
    {
        if ($this->constraintData == null) {
            $this->loadConstraintData();
        }

        $constraints = array();
        foreach ($this->constraintData[$table] as $constraintData) {
            $constraints[] = $this->getConstraint($constraintData['name'], $table, $schema);
        }

        return $constraints;
    }

    /**
     * Get constraint
     * 
     * @param  string $constraintName
     * @param  string $table
     * @param  string $schema
     * @param  string $database
     * @return Object\ConstraintObject 
     */
    public function getConstraint($constraintName, $table, $schema = null)
    {
        if ($this->constraintData == null) {
            $this->loadConstraintData();
        }

        $found = false;
        foreach ($this->constraintData as $tableName => $constraints) {
            foreach ($constraints as $constraintData) {
                if ($tableName == $table && $constraintData['name'] == $constraintName) {
                    $found = $constraintData;
                    break 2;
                }
            }
        }

        if (!$found) {
            throw new \Exception('invalid constraint, or constraint not found');
        }

        $constraint = new Object\ConstraintObject($found['name'], $table);
        $constraint->setType($found['type']);
        $constraint->setKeys($this->getConstraintKeys($found['name'], $table, $schema));
        return $constraint;
    }

    /**
     * Get constraint keys
     * 
     * @param  string $constraint
     * @param  string $table
     * @param  string $schema
     * @param  string $database
     * @return Object\ConstraintKeyObject 
     */
    public function getConstraintKeys($constraint, $table, $schema = null)
    {
        if ($this->constraintData == null) {
            $this->loadConstraintData();
        }

        $found = false;
        foreach ($this->constraintData as $tableName => $constraints) {
            foreach ($constraints as $constraintData) {
                if ($tableName == $table && $constraintData['name'] == $constraint) {
                    $found = $constraintData;
                    break 2;
                }
            }
        }

        if (!$found) {
            throw new \Exception('invalid constraint, or constraint not found');
        }

        $keys = array();
        foreach ($found['keys'] as $keyData) {
            $keys[] = $key = new Object\ConstraintKeyObject($keyData['column']);
            if ($found['type'] == 'FOREIGN KEY') {
                $key->setReferencedTableName($keyData['referenced_table']);
                $key->setReferencedColumnName($keyData['referenced_column']);
                $key->setForeignKeyUpdateRule($keyData['update_rule']);
                $key->setForeignKeyDeleteRule($keyData['delete_rule']);
            }
        }

        return $keys;
    }

    /**
     * Get view names
     * 
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getViewNames($schema = null)
    {
        return array();
    }

    /**
     * Get view
     * 
     * @param  string $viewName
     * @param  string $schema
     * @param  string $database
     * @return array
     */
    public function getView($viewName, $schema = null)
    {
        return array();
    }

    /**
     * Get triggers
     * 
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getTriggers($schema = null)
    {
        return array();
    }

    /**
     * Get trigger names
     * 
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getTriggerNames($schema = null)
    {
        return array();
    }

    /**
     * Get trigger
     * 
     * @param  string $triggerName
     * @param  string $schema
     * @param  string $database
     * @return array
     */
    public function getTrigger($triggerName, $schema = null)
    {
        return array();
    }

    /**
     * Load table column data
     */
    protected function loadTableColumnData()
    {
        $sql = 'SELECT "name" FROM "sqlite_master" WHERE "type" = \'table\'';
        $tables = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        $tables = $tables->toArray();

        foreach ($tables as $table) {
            $sql = 'PRAGMA table_info("' . $table['name'] . '")';
            $columns = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
            $this->tableData[$table['name']] = $columns->toArray();
        }
    }

    /**
     * Load constraint data
     */
    protected function loadConstraintData()
    {
        if ($this->tableData == null) {
            $this->loadTableColumnData();
        }

        foreach ($this->tableData as $tableName => $columns) {
            $this->constraintData[$tableName] = array();
            foreach ($columns as $column) {
                if ($column['pk'] == '1') {
                    $constraint = array(
                        'name' => 'PRIMARY',
                        'type' => 'PRIMARY KEY',
                        'keys' => array(0 => array('column' => $column['name']))
                    );
                    $this->constraintData[$tableName][] = $constraint;
                    break;
                }
            }

            $indexSql = 'PRAGMA index_list("' . $tableName . '")';
            $indexes = $this->adapter->query($indexSql, Adapter::QUERY_MODE_EXECUTE);

            foreach ($indexes as $index) {

                $constraint = array(
                    'name' => $index['name'],
                    'type' => 'UNIQUE KEY',
                    'keys' => array()
                );

                $indexInfoSql = 'PRAGMA index_info("' . $index['name'] . '")';
                $indexInfos = $this->adapter->query($indexInfoSql, Adapter::QUERY_MODE_EXECUTE);
                foreach ($indexInfos as $indexInfo) {
                    $constraint['keys'][] = array('column' => $indexInfo['name']);
                }

                $this->constraintData[$tableName][] = $constraint;
            }

            $foreignSql = 'PRAGMA foreign_key_list("' . $tableName . '");';
            $foreignKeys = $this->adapter->query($foreignSql, Adapter::QUERY_MODE_EXECUTE);

            foreach ($foreignKeys as $fkIndex => $foreignKey) {
                $constraint = array(
                    'name' => 'fk_' . $tableName . '_' . ($fkIndex+1),
                    'type' => 'FOREIGN KEY',
                    'keys' => array(0 => array(
                        'column' => $foreignKey['from'],
                        'referenced_table' => $foreignKey['table'],
                        'referenced_column' => $foreignKey['to'],
                        'update_rule' => $foreignKey['on_update'],
                        'delete_rule' => $foreignKey['on_delete']
                    ))
                );

                $this->constraintData[$tableName][] = $constraint;
            }

        }
    }

}
