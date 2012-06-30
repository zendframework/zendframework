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

use Zend\Db\Metadata\MetadataInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Metadata\Object;
use Zend\Db\ResultSet\ResultSetInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Metadata
 */
class SqliteMetadata extends AbstractSource
{
    /**
     * Get constraints
     * 
     * @param  string $table
     * @param  string $schema
     * @param  string $database
     * @return array 
     */
    public function getConstraintsOld($table, $schema = null)
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
    public function getConstraintOld($constraintName, $table, $schema = null)
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
    public function getConstraintKeysOld($constraint, $table, $schema = null)
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
     * Load constraint data
     */
    protected function loadConstraintDataOLD()
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

    protected function loadSchemaData()
    {
        if (isset($this->data['schemas'])) {
            return;
        }
        $this->prepareDataHierarchy('schemas');

        $results = $this->fetchPragma('database_list');
        foreach ($results as $row) {
            $schemas[] = $row['name'];
        }
        $this->data['schemas'] = $schemas;
    }

    protected function loadTableNameData($schema)
    {
        if (isset($this->data['table_names'][$schema])) {
            return;
        }
        $this->prepareDataHierarchy('table_names', $schema);

        // FEATURE: Filename?

        $p = $this->adapter->getPlatform();

        $sql = 'SELECT "name", "type", "sql" FROM ' . $p->quoteIdentifierChain(array($schema, 'sqlite_master'))
             . ' WHERE "type" IN (\'table\',\'view\') AND "name" NOT LIKE \'sqlite_%\'';

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        $tables = array();
        foreach ($results->toArray() as $row) {
            if ('table' == $row['type']) {
                $tables[$row['name']] = array(
                    'table_type' => 'BASE TABLE',
                    'view_definition' => null, // VIEW only
                    'check_option' => null,    // VIEW only
                    'is_updatable' => null,    // VIEW only
                );
            } else {
                $tables[$row['name']] = array(
                    'table_type' => 'VIEW',
                    // TODO: $row['sql'] is the CREATE VIEW statement, we might parse view_definition out
                    'view_definition' => null,
                    'check_option' => 'NONE',
                    'is_updatable' => false,
                );
            }
        }
        $this->data['table_names'][$schema] = $tables;
    }

    protected function loadColumnData($table, $schema)
    {
        if (isset($this->data['columns'][$schema][$table])) {
            return;
        }
        $this->prepareDataHierarchy('columns', $schema, $table);
        
        $p = $this->adapter->getPlatform();
        
        
        $results = $this->fetchPragma('table_info', $schema, $table);

        $columns = array();

        foreach ($results as $row) {
            $columns[$row['name']] = array(
                // cid appears to be zero-based, ordinal position needs to be one-based
                'ordinal_position'          => $row['cid'] + 1,
                'column_default'            => $row['dflt_value'],
                'is_nullable'               => !((bool) $row['notnull']),
                'data_type'                 => $row['type'],
                'character_maximum_length'  => null,
                'character_octet_length'    => null,
                'numeric_precision'         => null,
                'numeric_scale'             => null,
                'numeric_unsigned'          => null,
                'erratas'                   => array(),
            );
            // TODO: populate character_ and numeric_values with correct info
        }

        $this->data['columns'][$schema][$table] = $columns;
    }
    
//     protected function loadConstraintData($table, $schema)
//     {
//     }

    protected function loadTriggerData($schema)
    {
        if (isset($this->data['triggers'][$schema])) {
            return;
        }

        $this->prepareDataHierarchy('triggers', $schema);

        $p = $this->adapter->getPlatform();
        
        $sql = 'SELECT "name", "tbl_name", "sql" FROM ' . $p->quoteIdentifierChain(array($schema, 'sqlite_master'))
             . ' WHERE "type" = \'trigger\'';

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        $triggers = array();
        foreach ($results->toArray() as $row) {
            $triggers[$row['name']] = array(
                'trigger_name'               => $row['name'],
                'event_manipulation'         => null,
                'event_object_catalog'       => null,
                'event_object_schema'        => $schema,
                'event_object_table'         => $row['tbl_name'],
                'action_order'               => null,
                'action_condition'           => null,
                'action_statement'           => null,
                'action_orientation'         => 'ROW',
                'action_timing'              => null,
                'action_reference_old_table' => null,
                'action_reference_new_table' => null,
                'action_reference_old_row'   => 'OLD',
                'action_reference_new_row'   => 'NEW',
                'created'                    => null,
            );
/*
<trigger name>        ::= <identifier>
<trigger action time> ::= BEFORE|AFTER|INSTEAD OF
<trigger event>       ::= INSERT|DELETE|UPDATE [ OF <trigger column list>]

CREATE TRIGGER <trigger name> <trigger action time> <trigger event> ON <table name>
[ REFERENCING <transition table or variable list> ]
<triggered action>

CREATE TRIGGER [name] [action_timing] [event_manipulation] ON [event_object_table] FOR EACH [action_orientation] [action_statement]

 */
//             echo "CREATE TRIGGER {$trigger->getName()} {$trigger->getActionTiming()} {$trigger->getEventManipulation()} ON {$trigger->getEventObjectTable()}\n";
//             echo "  FOR EACH {$trigger->getActionOrientation()} {$trigger->getActionStatement()}\n";
        }

        $this->data['triggers'][$schema] = $triggers;
    }

    // Accessing TRIGGER data via SQL queries is not supported

    protected function fetchPragma($name, $schema = null, $value = null)
    {
        $p = $this->adapter->getPlatform();
        
        $sql = 'PRAGMA ';
        
        if (null !== $schema) {
            $sql .= $p->quoteIdentifier($schema) . '.';
        }
        $sql .= $name;

        if (null !== $value) {
            $sql .= '(' . $p->quoteValue($value) . ')';
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        if ($results instanceof ResultSetInterface) {
            return $results->toArray();
        }
        return array();
    }
}
