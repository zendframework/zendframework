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

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Metadata
 */
class MysqlMetadata extends AbstractSource
{
    protected function loadSchemaData()
    {
        if (isset($this->data['schemas'])) {
            return;
        }
        $this->prepareDataHierarchy('schemas');
        
        $platform = $this->adapter->getPlatform();
        
        $isColumns = array(
            'SCHEMA_NAME',
        );

        array_walk($isColumns, function (&$c) use ($platform) {
            $c = $platform->quoteIdentifier($c);
        });
        
        $sql = 'SELECT ' . implode(', ', $isColumns)
        . ' FROM ' . $platform->quoteIdentifierChain(array('INFORMATION_SCHEMA', 'SCHEMATA'))
        . ' WHERE ' . $platform->quoteIdentifier('SCHEMA_NAME')
        . ' != ' . $platform->quoteValue('INFORMATION_SCHEMA');
        
        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        
        $schemas = array();
        foreach ($results->toArray() as $row) {
            $schemas[] = $row['SCHEMA_NAME'];
        }

        $this->data['schemas'] = $schemas;
    }

    protected function loadTableNameData($schema)
    {
        if (isset($this->data['table_names'][$schema])) {
            return;
        }
        $this->prepareDataHierarchy('table_names', $schema);

        $platform = $this->adapter->getPlatform();

        $isColumns = array(
            'T.TABLE_NAME',
            'T.TABLE_TYPE',
            'V.VIEW_DEFINITION',
            'V.CHECK_OPTION',
            'V.IS_UPDATABLE',
        );

        array_walk($isColumns, function (&$c) use ($platform) { $c = $platform->quoteIdentifierInFragment($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
             . ' FROM ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.TABLES T')

             . ' LEFT JOIN ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.VIEWS V')
             . ' ON ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
             . '  = ' . $platform->quoteIdentifierInFragment('V.TABLE_SCHEMA')
             . ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_NAME')
             . '  = ' . $platform->quoteIdentifierInFragment('V.TABLE_NAME')

             . ' WHERE ' . $platform->quoteIdentifier('TABLE_TYPE')
             . ' IN (' . $platform->quoteValueList(array('BASE TABLE', 'VIEW')) . ')';

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
                  . ' = ' . $platform->quoteValue($schema);
        } else {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
                  . ' != ' . $platform->quoteValue('INFORMATION_SCHEMA');
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $tables = array();
        foreach ($results->toArray() as $row) {
            $tables[$row['TABLE_NAME']] = array(
                'table_type' => $row['TABLE_TYPE'],
                'view_definition' => $row['VIEW_DEFINITION'],
                'check_option' => $row['CHECK_OPTION'],
                'is_updatable' => ('YES' == $row['IS_UPDATABLE']),
            );
        }
        
        $this->data['table_names'][$schema] = $tables;
    }

    protected function loadColumnData($table, $schema)
    {
        if (isset($this->data['columns'][$schema][$table])) {
            return;
        }
        $this->prepareDataHierarchy('columns', $schema, $table);
        $platform = $this->adapter->getPlatform();

        $isColumns = array(
            'C.ORDINAL_POSITION',
            'C.COLUMN_DEFAULT',
            'C.IS_NULLABLE',
            'C.DATA_TYPE',
            'C.CHARACTER_MAXIMUM_LENGTH',
            'C.CHARACTER_OCTET_LENGTH',
            'C.NUMERIC_PRECISION',
            'C.NUMERIC_SCALE',
            'C.COLUMN_NAME',
            'C.COLUMN_TYPE',
        );

        array_walk($isColumns, function (&$c) use ($platform) { $c = $platform->quoteIdentifierInFragment($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
             . ' FROM ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.TABLES T')
             . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.COLUMNS C')
             . ' ON ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
             . '  = ' . $platform->quoteIdentifierInFragment('C.TABLE_SCHEMA')
             . ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_NAME')
             . '  = ' . $platform->quoteIdentifierInFragment('C.TABLE_NAME')
             . ' WHERE ' . $platform->quoteIdentifierInFragment('T.TABLE_TYPE')
             . ' IN (' . $platform->quoteValueList(array('BASE TABLE', 'VIEW')) . ')'
             . ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_NAME')
             . '  = ' . $platform->quoteValue($table);

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
                  . ' = ' . $platform->quoteValue($schema);
        } else {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
                  . ' != ' . $platform->quoteValue('INFORMATION_SCHEMA');
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        $columns = array();
        foreach ($results->toArray() as $row) {
            $columns[$row['COLUMN_NAME']] = array(
                'ordinal_position'          => $row['ORDINAL_POSITION'],
                'column_default'            => $row['COLUMN_DEFAULT'],
                'is_nullable'               => ('YES' == $row['IS_NULLABLE']),
                'data_type'                 => $row['DATA_TYPE'],
                'character_maximum_length'  => $row['CHARACTER_MAXIMUM_LENGTH'],
                'character_octet_length'    => $row['CHARACTER_OCTET_LENGTH'],
                'numeric_precision'         => $row['NUMERIC_PRECISION'],
                'numeric_scale'             => $row['NUMERIC_SCALE'],
                'numeric_unsigned'          => (false !== strpos($row['COLUMN_TYPE'], 'unsigned')),
                'erratas'                   => array(),
            );
        }

        $this->data['columns'][$schema][$table] = $columns;
    }

    protected function loadConstraintData($table, $schema)
    {
        $this->loadConstraintDataNames($schema);
        $this->loadConstraintDataKeys($schema);
        $this->loadConstraintReferences($schema);
    }

    protected function loadConstraintDataNames($schema)
    {
        if (isset($this->data['constraint_names'][$schema])) {
            return;
        }

        $this->prepareDataHierarchy('constraint_names', $schema);

        $platform = $this->adapter->getPlatform();

        $isColumns = array(
            'TC.TABLE_NAME',
            'TC.CONSTRAINT_NAME',
            'TC.CONSTRAINT_TYPE',
        );

        array_walk($isColumns, function (&$c) use ($platform) {
            $c = $platform->quoteIdentifierInFragment($c);
        });

        $sql = 'SELECT ' . implode(', ', $isColumns)
        . ' FROM ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.TABLES T')
        . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.TABLE_CONSTRAINTS TC')
        . ' ON ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
        . '  = ' . $platform->quoteIdentifierInFragment('TC.TABLE_SCHEMA')
        . ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_NAME')
        . '  = ' . $platform->quoteIdentifierInFragment('TC.TABLE_NAME')
        . ' WHERE ' . $platform->quoteIdentifierInFragment('T.TABLE_TYPE')
        . ' IN (' . $platform->quoteValueList(array('BASE TABLE', 'VIEW')) . ')';

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
            . ' = ' . $platform->quoteValue($schema);
        } else {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
            . ' != ' . $platform->quoteValue('INFORMATION_SCHEMA');
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $data = array();
        foreach ($results->toArray() as $row) {
            $data[] = array_change_key_case($row, CASE_LOWER);
        }

        $this->data['constraint_names'][$schema] = $data;
    }

    protected function loadConstraintDataKeys($schema)
    {
        if (isset($this->data['constraint_keys'][$schema])) {
            return;
        }

        $this->prepareDataHierarchy('constraint_keys', $schema);

        $platform = $this->adapter->getPlatform();

        $isColumns = array(
            'T.TABLE_NAME',
            'KCU.CONSTRAINT_NAME',
            'KCU.COLUMN_NAME',
            'KCU.ORDINAL_POSITION',
        );

        array_walk($isColumns, function (&$c) use ($platform) {
            $c = $platform->quoteIdentifierInFragment($c);
        });

        $sql = 'SELECT ' . implode(', ', $isColumns)
        . ' FROM ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.TABLES T')
        
        . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.KEY_COLUMN_USAGE KCU')
        . ' ON ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
        . '  = ' . $platform->quoteIdentifierInFragment('KCU.TABLE_SCHEMA')
        . ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_NAME')
        . '  = ' . $platform->quoteIdentifierInFragment('KCU.TABLE_NAME')
        
        . ' WHERE ' . $platform->quoteIdentifierInFragment('T.TABLE_TYPE')
        . ' IN (' . $platform->quoteValueList(array('BASE TABLE', 'VIEW')) . ')';

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
            . ' = ' . $platform->quoteValue($schema);
        } else {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
            . ' != ' . $platform->quoteValue('INFORMATION_SCHEMA');
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $data = array();
        foreach ($results->toArray() as $row) {
            $data[] = array_change_key_case($row, CASE_LOWER);
        }

        $this->data['constraint_keys'][$schema] = $data;
    }

    protected function loadConstraintReferences($schema)
    {
        if (isset($this->data['constraint_references'][$schema])) {
            return;
        }

        $this->prepareDataHierarchy('constraint_references', $schema);

        $platform = $this->adapter->getPlatform();

        $isColumns = array(
            'RC.TABLE_NAME',
            'RC.CONSTRAINT_NAME',
            'RC.UPDATE_RULE',
            'RC.DELETE_RULE',
            'KCU.REFERENCED_TABLE_SCHEMA',
            'KCU.REFERENCED_TABLE_NAME',
            'KCU.REFERENCED_COLUMN_NAME'
        );

        array_walk($isColumns, function (&$c) use ($platform) {
            $c = $platform->quoteIdentifierInFragment($c);
        });

        $sql = 'SELECT ' . implode(', ', $isColumns)
        . 'FROM ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.TABLES T')

        . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS RC')
        . ' ON ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
        . '  = ' . $platform->quoteIdentifierInFragment('RC.CONSTRAINT_SCHEMA')
        . ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_NAME')
        . '  = ' . $platform->quoteIdentifierInFragment('RC.TABLE_NAME')

        . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.KEY_COLUMN_USAGE KCU')
        . ' ON ' . $platform->quoteIdentifierInFragment('RC.CONSTRAINT_SCHEMA')
        . '  = ' . $platform->quoteIdentifierInFragment('KCU.TABLE_SCHEMA')
        . ' AND ' . $platform->quoteIdentifierInFragment('RC.TABLE_NAME')
        . '  = ' . $platform->quoteIdentifierInFragment('KCU.TABLE_NAME')
        . ' AND ' . $platform->quoteIdentifierInFragment('RC.CONSTRAINT_NAME')
        . '  = ' . $platform->quoteIdentifierInFragment('KCU.CONSTRAINT_NAME')

        . 'WHERE ' . $platform->quoteIdentifierInFragment('T.TABLE_TYPE')
        . ' IN (' . $platform->quoteValueList(array('BASE TABLE', 'VIEW')) . ')';

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
            . ' = ' . $platform->quoteValue($schema);
        } else {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
            . ' != ' . $platform->quoteValue('INFORMATION_SCHEMA');
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $data = array();
        foreach ($results->toArray() as $row) {
            $data[] = array_change_key_case($row, CASE_LOWER);
        }

        $this->data['constraint_references'][$schema] = $data;
    }

    protected function loadTriggerData($schema)
    {
        if (isset($this->data['triggers'][$schema])) {
            return;
        }

        $this->prepareDataHierarchy('triggers', $schema);

        $platform = $this->adapter->getPlatform();

        $isColumns = array(
//            'TRIGGER_CATALOG',
//            'TRIGGER_SCHEMA',
            'TRIGGER_NAME',
            'EVENT_MANIPULATION',
            'EVENT_OBJECT_CATALOG',
            'EVENT_OBJECT_SCHEMA',
            'EVENT_OBJECT_TABLE',
            'ACTION_ORDER',
            'ACTION_CONDITION',
            'ACTION_STATEMENT',
            'ACTION_ORIENTATION',
            'ACTION_TIMING',
            'ACTION_REFERENCE_OLD_TABLE',
            'ACTION_REFERENCE_NEW_TABLE',
            'ACTION_REFERENCE_OLD_ROW',
            'ACTION_REFERENCE_NEW_ROW',
            'CREATED',
        );

        array_walk($isColumns, function (&$c) use ($platform) {
            $c = $platform->quoteIdentifier($c);
        });
        
        $sql = 'SELECT ' . implode(', ', $isColumns)
        . ' FROM ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.TRIGGERS')
        . ' WHERE ';
        
        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= $platform->quoteIdentifier('TRIGGER_SCHEMA')
            . ' = ' . $platform->quoteValue($schema);
        } else {
            $sql .= $platform->quoteIdentifier('TRIGGER_SCHEMA')
            . ' != ' . $platform->quoteValue('INFORMATION_SCHEMA');
        }
        
        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        
        $data = array();
        foreach ($results->toArray() as $row) {
            $row = array_change_key_case($row, CASE_LOWER);
            if (null !== $row['created']) {
                $row['created'] = new \DateTime($row['created']);
            }
            $data[$row['trigger_name']] = $row;
        }

        $this->data['triggers'][$schema] = $data;
    }
}
