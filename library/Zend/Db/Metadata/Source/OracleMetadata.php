<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Metadata\Source;

use Zend\Db\Adapter\Adapter;

/**
 * Metadata source for Oracle
 */
class OracleMetadata extends AbstractSource
{
    /**
     * @var array
     */
    protected $constraintTypeMap = array(
        'C' => 'CHECK',
        'P' => 'PRIMARY KEY',
        'R' => 'FOREIGN_KEY'
    );

    /**
     * {@inheritdoc}
     * @see \Zend\Db\Metadata\Source\AbstractSource::loadColumnData()
     */
    protected function loadColumnData($table, $schema)
    {
        if (isset($this->data['columns'][$schema][$table])) {
            return;
        }

        $isColumns = array(
            'COLUMN_ID',
            'COLUMN_NAME',
            'DATA_DEFAULT',
            'NULLABLE',
            'DATA_TYPE',
            'DATA_LENGTH',
            'DATA_PRECISION',
            'DATA_SCALE'
        );

        $this->prepareDataHierarchy('columns', $schema, $table);
        $parameters = array(
            ':ownername' => $schema,
            ':tablename' => $table
        );

        $sql = 'SELECT ' . implode(', ', $isColumns)
             . ' FROM all_tab_columns'
             . ' WHERE owner = :ownername AND table_name = :tablename';

        $result = $this->adapter->query($sql)->execute($parameters);
        $columns = array();

        foreach ($result as $row) {
            $columns[$row['COLUMN_NAME']] = array(
                'ordinal_position'          => $row['COLUMN_ID'],
                'column_default'            => $row['DATA_DEFAULT'],
                'is_nullable'               => ('Y' == $row['NULLABLE']),
                'data_type'                 => $row['DATA_TYPE'],
                'character_maximum_length'  => $row['DATA_LENGTH'],
                'character_octet_length'    => null,
                'numeric_precision'         => $row['DATA_PRECISION'],
                'numeric_scale'             => $row['DATA_SCALE'],
                'numeric_unsigned'          => false,
                'erratas'                   => array(),
            );
        }

        $this->data['columns'][$schema][$table] = $columns;
        return $this;
    }

    /**
     * Constraint type
     *
     * @param string $type
     * @return string
     */
    protected function getConstraintType($type)
    {
        if (isset($this->constraintTypeMap[$type])) {
            return $this->constraintTypeMap[$type];
        }

        return $type;
    }

    /**
     * {@inheritdoc}
     * @see \Zend\Db\Metadata\Source\AbstractSource::loadConstraintData()
     */
    protected function loadConstraintData($table, $schema)
    {
        if (isset($this->data['constraints'][$schema][$table])) {
            return;
        }

        $this->prepareDataHierarchy('constraints', $schema, $table);
        $sql = '
            SELECT
                ac.owner,
                ac.constraint_name,
                ac.constraint_type,
                ac.search_condition check_clause,
                ac.table_name,
                ac.delete_rule,
                cc1.column_name,
                cc2.table_name as ref_table,
                cc2.column_name as ref_column,
                cc2.owner as ref_owner
            FROM all_constraints ac
            INNER JOIN all_cons_columns cc1
                ON cc1.constraint_name = ac.constraint_name
            LEFT JOIN all_cons_columns cc2
                ON cc2.constraint_name = ac.r_constraint_name
                AND cc2.position = cc1.position

            WHERE
                ac.owner = :schema AND ac.table_name = :table

            ORDER BY ac.constraint_name;
        ';

        $parameters = array(
            ':schema' => $schema,
            ':table' => $table
        );

        $results = $this->adapter->query($sql)->execute($parameters);
        $isFK = false;
        $name = null;
        $constraints = array();

        foreach ($results as $row) {
            if ($row['CONSTRAINT_NAME'] !== $name) {
                $name = $row['CONSTRAINT_NAME'];
                $constraints[$name] = array(
                    'constraint_name' => $name,
                    'constraint_type' => $this->getConstraintType($row['CONSTRAINT_TYPE']),
                    'table_name'      => $row['TABLE_NAME'],
                );

                if ('C' == $row['CONSTRAINT_TYPE']) {
                    $constraints[$name]['CHECK_CLAUSE'] = $row['CHECK_CLAUSE'];
                    continue;
                }

                $constraints[$name]['columns'] = array();

                $isFK = ('R' == $row['CONSTRAINT_TYPE']);
                if ($isFK) {
                    $constraints[$name]['referenced_table_schema'] = $row['REF_OWNER'];
                    $constraints[$name]['referenced_table_name']   = $row['REF_TABLE'];
                    $constraints[$name]['referenced_columns']      = array();
                    $constraints[$name]['match_option']            = 'NONE';
                    $constraints[$name]['update_rule']             = null;
                    $constraints[$name]['delete_rule']             = $row['DELETE_RULE'];
                }
            }

            $constraints[$name]['columns'][] = $row['COLUMN_NAME'];
            if ($isFK) {
                $constraints[$name]['referenced_columns'][] = $row['REF_COLUMN'];
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \Zend\Db\Metadata\Source\AbstractSource::loadSchemaData()
     */
    protected function loadSchemaData()
    {
        if (isset($this->data['schemas'])) {
            return;
        }

        $this->prepareDataHierarchy('schemas');
        $sql = 'SELECT USERNAME FROM ALL_USERS';
        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $schemas = array();
        foreach ($results->toArray() as $row) {
            $schemas[] = $row['USERNAME'];
        }

        $this->data['schemas'] = $schemas;
    }

    /**
     * {@inheritdoc}
     * @see \Zend\Db\Metadata\Source\AbstractSource::loadTableNameData()
     */
    protected function loadTableNameData($schema)
    {
        if (isset($this->data['table_names'][$schema])) {
            return $this;
        }

        $this->prepareDataHierarchy('table_names', $schema);
        $tables = array();

        // Tables
        $bind = array(':OWNER' => strtoupper($schema));
        $result = $this->adapter->query('SELECT TABLE_NAME FROM ALL_TABLES WHERE OWNER=:OWNER')->execute($bind);

        foreach ($result as $row) {
            $tables[$row['TABLE_NAME']] = array(
                'table_type' => 'BASE TABLE',
                'view_definition' => null,
                'check_option' => null,
                'is_updatable' => false,
            );
        }

        // Views
        $result = $this->adapter->query('SELECT VIEW_NAME, TEXT FROM ALL_VIEWS WHERE OWNER=:OWNER', $bind);
        foreach ($result as $row) {
            $tables[$row['VIEW_NAME']] = array(
                'table_type' => 'VIEW',
                'view_definition' => null,
                'check_option' => 'NONE',
                'is_updatable' => false,
            );
        }

        $this->data['table_names'][$schema] = $tables;
        return $this;
    }

    /**
     * FIXME: load trigger data
     *
     * {@inheritdoc}
     *
     * @see \Zend\Db\Metadata\Source\AbstractSource::loadTriggerData()
     */
    protected function loadTriggerData($schema)
    {
        if (isset($this->data['triggers'][$schema])) {
            return;
        }

        $this->prepareDataHierarchy('triggers', $schema);
    }
}
