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
class SqlServerMetadata extends AbstractSource
{


    protected function loadSchemaData()
    {
        if (isset($this->data['schemas'])) {
            return;
        }
        $this->prepareDataHierarchy('schemas');

        $p = $this->adapter->getPlatform();

        $sql = 'SELECT ' . $p->quoteIdentifier('SCHEMA_NAME')
            . ' FROM ' . $p->quoteIdentifierChain(array('INFORMATION_SCHEMA', 'SCHEMATA'))
            . ' WHERE ' . $p->quoteIdentifier('SCHEMA_NAME')
            . ' != ' . $p->quoteValue('INFORMATION_SCHEMA');

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

        $p = $this->adapter->getPlatform();

        $isColumns = array(
            array('T','TABLE_NAME'),
            array('T','TABLE_TYPE'),
            array('V','VIEW_DEFINITION'),
            array('V','CHECK_OPTION'),
            array('V','IS_UPDATABLE'),
        );

        array_walk($isColumns, function (&$c) use ($p) { $c = $p->quoteIdentifierChain($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
            . ' FROM ' . $p->quoteIdentifierChain(array('INFORMATION_SCHEMA', 'TABLES')) . ' t'

            . ' LEFT JOIN ' . $p->quoteIdentifierChain(array('INFORMATION_SCHEMA', 'VIEWS')) . ' v'
            . ' ON ' . $p->quoteIdentifierChain(array('T','TABLE_SCHEMA'))
            . '  = ' . $p->quoteIdentifierChain(array('V','TABLE_SCHEMA'))
            . ' AND ' . $p->quoteIdentifierChain(array('T','TABLE_NAME'))
            . '  = ' . $p->quoteIdentifierChain(array('V','TABLE_NAME'))

            . ' WHERE ' . $p->quoteIdentifierChain(array('T','TABLE_TYPE'))
            . ' IN (' . $p->quoteValueList(array('BASE TABLE', 'VIEW')) . ')';

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= ' AND ' . $p->quoteIdentifierChain(array('T','TABLE_SCHEMA'))
                . ' = ' . $p->quoteValue($schema);
        } else {
            $sql .= ' AND ' . $p->quoteIdentifierChain(array('T','TABLE_SCHEMA'))
                . ' != ' . $p->quoteValue('INFORMATION_SCHEMA');
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
        $p = $this->adapter->getPlatform();

        $isColumns = array(
            array('C','ORDINAL_POSITION'),
            array('C','COLUMN_DEFAULT'),
            array('C','IS_NULLABLE'),
            array('C','DATA_TYPE'),
            array('C','CHARACTER_MAXIMUM_LENGTH'),
            array('C','CHARACTER_OCTET_LENGTH'),
            array('C','NUMERIC_PRECISION'),
            array('C','NUMERIC_SCALE'),
            array('C','COLUMN_NAME'),
        );

        array_walk($isColumns, function (&$c) use ($p) { $c = $p->quoteIdentifierChain($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
            . ' FROM ' . $p->quoteIdentifierChain(array('INFORMATION_SCHEMA','TABLES')) . 'T'
            . ' INNER JOIN ' . $p->quoteIdentifierChain(array('INFORMATION_SCHEMA','COLUMNS')) . 'C'
            . ' ON ' . $p->quoteIdentifierChain(array('T','TABLE_SCHEMA'))
            . '  = ' . $p->quoteIdentifierChain(array('C','TABLE_SCHEMA'))
            . ' AND ' . $p->quoteIdentifierChain(array('T','TABLE_NAME'))
            . '  = ' . $p->quoteIdentifierChain(array('C','TABLE_NAME'))
            . ' WHERE ' . $p->quoteIdentifierChain(array('T','TABLE_TYPE'))
            . ' IN (' . $p->quoteValueList(array('BASE TABLE', 'VIEW')) . ')'
            . ' AND ' . $p->quoteIdentifierChain(array('T','TABLE_NAME'))
            . '  = ' . $p->quoteValue($table);

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= ' AND ' . $p->quoteIdentifierChain(array('T','TABLE_SCHEMA'))
                . ' = ' . $p->quoteValue($schema);
        } else {
            $sql .= ' AND ' . $p->quoteIdentifierChain(array('T','TABLE_SCHEMA'))
                . ' != ' . $p->quoteValue('INFORMATION_SCHEMA');
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
                'numeric_unsigned'          => null,
                'erratas'                   => array(),
            );
        }

        $this->data['columns'][$schema][$table] = $columns;
    }

    protected function loadConstraintData($table, $schema)
    {
        $platform = $this->adapter->getPlatform();

        // LOAD CONSTRAINT NAMES

        $isColumns = array(
            'CONSTRAINT_NAME',
            'TABLE_SCHEMA',
            'TABLE_NAME',
            'CONSTRAINT_TYPE'
        );

        array_walk($isColumns, function (&$c) use ($platform) { $c = $platform->quoteIdentifier($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
            . ' FROM ' . $platform->quoteIdentifier('INFORMATION_SCHEMA')
            . $platform->getIdentifierSeparator() . $platform->quoteIdentifier('TABLE_CONSTRAINTS')
            . ' WHERE ' . $platform->quoteIdentifier('TABLE_SCHEMA')
            . ' != ' . $platform->quoteValue('INFORMATION_SCHEMA');

        if ($schema !== '__DEFAULT_SCHEMA__') {
            $sql .= ' AND ' . $platform->quoteIdentifier('TABLE_SCHEMA')
                . ' = ' . $platform->quoteValue($schema);
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $constraintData = array();
        foreach ($results->toArray() as $row) {
            $constraintData[] = array_change_key_case($row, CASE_LOWER);
        }

        $this->prepareDataHierarchy('constraint_names', $schema);
        $this->data['constraint_names'][$schema] = $constraintData;

        // LOAD CONSTRAINT KEYS

        $isColumns = array(
            'CONSTRAINT_NAME',
            'TABLE_SCHEMA',
            'TABLE_NAME',
            'COLUMN_NAME',
            'ORDINAL_POSITION'
        );

        array_walk($isColumns, function (&$c) use ($platform) { $c = $platform->quoteIdentifier($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
            . ' FROM ' . $platform->quoteIdentifier('INFORMATION_SCHEMA')
            . $platform->getIdentifierSeparator() . $platform->quoteIdentifier('KEY_COLUMN_USAGE')
            . ' WHERE ' . $platform->quoteIdentifier('TABLE_SCHEMA')
            . ' != ' . $platform->quoteValue('INFORMATION_SCHEMA');

        if ($schema != null || $this->defaultSchema != null) {
            if ($schema == null) {
                $schema = $this->defaultSchema;
            }
            $sql .= ' AND ' . $platform->quoteIdentifier('TABLE_SCHEMA')
                . ' = ' . $platform->quoteValue($schema);
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $constraintKeyData = array();
        foreach ($results->toArray() as $row) {
            $constraintKeyData[] = array_change_key_case($row, CASE_LOWER);
        }

        $this->prepareDataHierarchy('constraint_keys', $schema);
        $this->data['constraint_keys'][$schema] = $constraintKeyData;

        // LOAD CONSTRAINT REFERENCES

        $quoteIdentifierForWalk = function (&$c) use ($platform) { $c = $platform->quoteIdentifierInFragment($c); };
        $quoteSelectList = function (array $identifierList) use ($platform, $quoteIdentifierForWalk) {
            array_walk($identifierList, $quoteIdentifierForWalk);
            return implode(', ', $identifierList);
        };

        // target: CONSTRAINT_SCHEMA, CONSTRAINT_NAME, UPDATE_RULE, DELETE_RULE, REFERENCE_CONSTRAINT_NAME

        $sql = 'SELECT ' . $quoteSelectList(array(
            'RC.CONSTRAINT_NAME', 'RC.UPDATE_RULE', 'RC.DELETE_RULE',
            'TC1.TABLE_NAME', 'CK.TABLE_NAME AS REFERENCED_TABLE_NAME', 'CK.COLUMN_NAME AS REFERENCED_COLUMN_NAME'
        ))
            . ' FROM ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS RC')
            . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.TABLE_CONSTRAINTS TC1')
            . ' ON ' . $platform->quoteIdentifierInFragment('RC.CONSTRAINT_NAME')
            . ' = ' . $platform->quoteIdentifierInFragment('TC1.CONSTRAINT_NAME')
            . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.TABLE_CONSTRAINTS TC2')
            . ' ON ' . $platform->quoteIdentifierInFragment('RC.UNIQUE_CONSTRAINT_NAME')
            . ' = ' . $platform->quoteIdentifierInFragment('TC2.CONSTRAINT_NAME')
            . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.KEY_COLUMN_USAGE CK')
            . ' ON ' . $platform->quoteIdentifierInFragment('TC2.CONSTRAINT_NAME')
            . ' = ' . $platform->quoteIdentifierInFragment('CK.CONSTRAINT_NAME');

        if ($schema != '__DEFAULT_SCHEMA__') {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('RC.CONSTRAINT_SCHEMA')
                . ' = ' . $platform->quoteValue($schema);
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $constraintRefData = array();
        foreach ($results->toArray() as $row) {
            $constraintRefData[] = array_change_key_case($row, CASE_LOWER);
        }

        $this->prepareDataHierarchy('constraint_references', $schema);
        $this->data['constraint_references'][$schema] = $constraintRefData;

    }

    protected function loadTriggerData($schema)
    {
        if (isset($this->data['triggers'][$schema])) {
            return;
        }

        $this->prepareDataHierarchy('triggers', $schema);

        $p = $this->adapter->getPlatform();

        $isColumns = array(
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

        array_walk($isColumns, function (&$c) use ($p) {
            $c = $p->quoteIdentifier($c);
        });

        $sql = 'SELECT ' . implode(', ', $isColumns)
            . ' FROM ' . $p->quoteIdentifierChain(array('INFORMATION_SCHEMA','TRIGGERS'))
            . ' WHERE ';

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= $p->quoteIdentifier('TRIGGER_SCHEMA')
                . ' = ' . $p->quoteValue($schema);
        } else {
            $sql .= $p->quoteIdentifier('TRIGGER_SCHEMA')
                . ' != ' . $p->quoteValue('INFORMATION_SCHEMA');
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
