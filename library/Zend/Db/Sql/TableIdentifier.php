<?php

namespace Zend\Db\Sql;

class TableIdentifier
{
    protected $table;

    protected $schema;

    public function __construct($table, $schema = null)
    {
        $this->table = $table;
        $this->schema = $schema;
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function hasSchema()
    {
        return ($this->schema != null);
    }

    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    public function getSchema()
    {
        return $this->schema;
    }

}
