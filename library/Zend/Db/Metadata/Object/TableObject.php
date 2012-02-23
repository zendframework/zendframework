<?php

namespace Zend\Db\Metadata\Object;

class TableObject
{

    /*
    protected $catalogName = null;
    protected $schemaName = null;
    */

    protected $name = null;
    protected $type = null;
    protected $columns = null;
    protected $constraints = null;

    /*
    public function getCatalogName()
    {
        return $this->catalogName;
    }

    public function setCatalogName($catalogName)
    {
        $this->catalogName = $catalogName;
        return $this;
    }

    public function getSchemaName()
    {
        return $this->schemaName;
    }

    public function setSchemaName($schemaName)
    {
        $this->schemaName = $schemaName;
        return $this;
    }
    */

    public function __construct($name)
    {
        if ($name) {
            $this->setName($name);
        }
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;
    }

    public function getConstraints()
    {
        return $this->columns;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

}
