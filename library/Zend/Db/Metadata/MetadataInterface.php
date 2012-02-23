<?php

namespace Zend\Db\Metadata;

interface MetadataInterface
{
    public function getSchemas();

    public function getTableNames($schema = null, $database = null);
    public function getTables($schema = null, $database = null);
    public function getTable($tableName, $schema = null, $database = null);

    public function getViewNames($schema = null, $database = null);
    public function getViews($schema = null, $database = null);
    public function getView($viewName, $schema = null, $database = null);

    public function getColumnNames($table, $schema = null, $database = null);
    public function getColumns($table, $schema = null, $database = null);
    public function getColumn($columnName, $table, $schema = null, $database = null);

    public function getConstraints($table, $schema = null, $database = null);
    public function getConstraint($constraintName, $table, $schema = null, $database = null);
    public function getConstraintKeys($constraint, $table, $schema = null, $database = null);

    public function getTriggerNames($schema = null, $database = null);
    public function getTriggers($schema = null, $database = null);
    public function getTrigger($triggerName, $schema = null, $database = null);

}
