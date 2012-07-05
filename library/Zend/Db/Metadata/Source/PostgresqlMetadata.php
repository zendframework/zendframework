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

use Zend\Db\Adapter\Adapter;
use Zend\Db\Metadata\Object;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Metadata
 */
class PostgresqlMetadata extends AbstractSource
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
            array('t','table_name'),
            array('t','table_type'),
            array('v','view_definition'),
            array('v','check_option'),
            array('v','is_updatable'),
        );

        array_walk($isColumns, function (&$c) use ($p) { $c = $p->quoteIdentifierChain($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
            . ' FROM ' . $p->quoteIdentifierChain(array('information_schema', 'tables')) . ' t'

            . ' LEFT JOIN ' . $p->quoteIdentifierChain(array('information_schema', 'views')) . ' v'
            . ' ON ' . $p->quoteIdentifierChain(array('t','table_schema'))
            . '  = ' . $p->quoteIdentifierChain(array('v','table_schema'))
            . ' AND ' . $p->quoteIdentifierChain(array('t','table_name'))
            . '  = ' . $p->quoteIdentifierChain(array('v','table_name'))

            . ' WHERE ' . $p->quoteIdentifierChain(array('t','table_type'))
            . ' IN (' . $p->quoteValueList(array('BASE TABLE', 'VIEW')) . ')';

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= ' AND ' . $p->quoteIdentifierChain(array('t','table_schema'))
                . ' = ' . $p->quoteValue($schema);
        } else {
            $sql .= ' AND ' . $p->quoteIdentifierChain(array('t','table_schema'))
                . ' != ' . $p->quoteValue('information_schema');
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $tables = array();
        foreach ($results->toArray() as $row) {
            $tables[$row['table_name']] = array(
                'table_type' => $row['table_type'],
                'view_definition' => $row['view_definition'],
                'check_option' => $row['check_option'],
                'is_updatable' => ('YES' == $row['is_updatable']),
            );
        }

        $this->data['table_names'][$schema] = $tables;
    }

    protected function loadColumnData($table, $schema)
    {
        $platform = $this->adapter->getPlatform();

        $isColumns = array(
            'table_name',
            'column_name',
            'ordinal_position',
            'column_default',
            'is_nullable',
            'data_type',
            'character_maximum_length',
            'character_octet_length',
            'numeric_precision',
            'numeric_scale',
        );

        array_walk($isColumns, function (&$c) use ($platform) { $c = $platform->quoteIdentifier($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
            . ' FROM ' . $platform->quoteIdentifier('information_schema')
            . $platform->getIdentifierSeparator() . $platform->quoteIdentifier('columns')
            . ' WHERE ' . $platform->quoteIdentifier('table_schema')
            . ' != ' . $platform->quoteValue('information');

        if ($schema != '__DEFAULT_SCHEMA__') {
            $sql .= ' AND ' . $platform->quoteIdentifier('table_schema')
                . ' = ' . $platform->quoteValue($schema);
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        $columns = array();
        foreach ($results->toArray() as $row) {
            $columns[$row['column_name']] = array(
                'ordinal_position'          => $row['ordinal_position'],
                'column_default'            => $row['column_default'],
                'is_nullable'               => ('YES' == $row['is_nullable']),
                'data_type'                 => $row['data_type'],
                'character_maximum_length'  => $row['character_maximum_length'],
                'character_octet_length'    => $row['character_octet_length'],
                'numeric_precision'         => $row['numeric_precision'],
                'numeric_scale'             => $row['numeric_scale'],
                'numeric_unsigned'          => null,
                'erratas'                   => array(),
            );
        }

        $this->prepareDataHierarchy('columns', $schema, $table);
        $this->data['columns'][$schema][$table] = $columns;;
    }

    protected function loadConstraintData($table, $schema)
    {
        $platform = $this->adapter->getPlatform();

        // LOAD CONSTRAINT NAMES

        $isColumns = array(
            'constraint_name',
            'table_schema',
            'table_name',
            'constraint_type'
        );

        array_walk($isColumns, function (&$c) use ($platform) { $c = $platform->quoteIdentifier($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
            . ' FROM ' . $platform->quoteIdentifier('information_schema')
            . $platform->getIdentifierSeparator() . $platform->quoteIdentifier('table_constraints')
            . ' WHERE ' . $platform->quoteIdentifier('table_schema')
            . ' != ' . $platform->quoteValue('information_schema');

        if ($schema !== '__DEFAULT_SCHEMA__') {
            $sql .= ' AND ' . $platform->quoteIdentifier('table_schema')
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
            'constraint_name',
            'table_schema',
            'table_name',
            'column_name',
            'ordinal_position'
        );

        array_walk($isColumns, function (&$c) use ($platform) { $c = $platform->quoteIdentifier($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
            . ' FROM ' . $platform->quoteIdentifier('information_schema')
            . $platform->getIdentifierSeparator() . $platform->quoteIdentifier('key_column_usage')
            . ' WHERE ' . $platform->quoteIdentifier('table_schema')
            . ' != ' . $platform->quoteValue('information_schema');

        if ($schema != null || $this->defaultSchema != null) {
            if ($schema == null) {
                $schema = $this->defaultSchema;
            }
            $sql .= ' AND ' . $platform->quoteIdentifier('table_schema')
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
                'rc.constraint_name', 'rc.update_rule', 'rc.delete_rule',
                'tc1.table_name', 'ck.table_name AS referenced_table_name', 'ck.column_name AS referenced_column_name'
                ))
            . ' FROM ' . $platform->quoteIdentifierInFragment('information_schema.referential_constraints rc')
            . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('information_schema.table_constraints tc1')
            . ' ON ' . $platform->quoteIdentifierInFragment('rc.constraint_name')
            . ' = ' . $platform->quoteIdentifierInFragment('tc1.constraint_name')
            . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('information_schema.table_constraints tc2')
            . ' ON ' . $platform->quoteIdentifierInFragment('rc.unique_constraint_name')
            . ' = ' . $platform->quoteIdentifierInFragment('tc2.constraint_name')
            . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('information_schema.key_column_usage ck')
            . ' ON ' . $platform->quoteIdentifierInFragment('tc2.constraint_name')
            . ' = ' . $platform->quoteIdentifierInFragment('ck.constraint_name');

        if ($schema != '__DEFAULT_SCHEMA__') {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('rc.constraint_schema')
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
            'trigger_name',
            'event_manipulation',
            'event_object_catalog',
            'event_object_schema',
            'event_object_table',
            'action_order',
            'action_condition',
            'action_statement',
            'action_orientation',
            'action_timing',
            'action_reference_old_table',
            'action_reference_new_table',
            'action_reference_old_row',
            'action_reference_new_row',
            'created',
        );

        array_walk($isColumns, function (&$c) use ($p) {
            $c = $p->quoteIdentifier($c);
        });

        $sql = 'SELECT ' . implode(', ', $isColumns)
            . ' FROM ' . $p->quoteIdentifierChain(array('information_schema','triggers'))
            . ' WHERE ';

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= $p->quoteIdentifier('trigger_schema')
                . ' = ' . $p->quoteValue($schema);
        } else {
            $sql .= $p->quoteIdentifier('trigger_schema')
                . ' != ' . $p->quoteValue('information_schema');
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
