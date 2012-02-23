<?php

namespace Zend\Db\Metadata\Object;

class ConstraintKeyObject
{
    const FK_CASCADE = 'CASCADE';
    const FK_SET_NULL = 'SET NULL';
    const FK_NO_ACTION = 'NO ACTION';
    const FK_RESTRICT = 'RESTRICT';
    const FK_SET_DEFAULT = 'SET DEFAULT';

    protected $columnName = null;
    protected $ordinalPosition = null;
    protected $positionInUniqueConstraint = null;
    protected $referencedTableSchema = null;
    protected $referencedTableName = null;
    protected $referencedColumnName = null;
    protected $foreignKeyUpdateRule = null;
    protected $foreignKeyDeleteRule = null;

    public function __construct($column)
    {
        $this->setColumnName($column);
    }

    public function getColumnName()
    {
        return $this->columnName;
    }
    
    public function setColumnName($columnName)
    {
        $this->columnName = $columnName;
        return $this;
    }
    
    public function getOrdinalPosition()
    {
        return $this->ordinalPosition;
    }
    
    public function setOrdinalPosition($ordinalPosition)
    {
        $this->ordinalPosition = $ordinalPosition;
        return $this;
    }
    
    public function getPositionInUniqueConstraint()
    {
        return $this->positionInUniqueConstraint;
    }
    
    public function setPositionInUniqueConstraint($positionInUniqueConstraint)
    {
        $this->positionInUniqueConstraint = $positionInUniqueConstraint;
        return $this;
    }
    
    public function getReferencedTableSchema()
    {
        return $this->referencedTableSchema;
    }

    public function setReferencedTableSchema($referencedTableSchema)
    {
        $this->referencedTableSchema = $referencedTableSchema;
        return $this;
    }
    
    public function getReferencedTableName()
    {
        return $this->referencedTableName;
    }
    
    public function setReferencedTableName($referencedTableName)
    {
        $this->referencedTableName = $referencedTableName;
        return $this;
    }
    
    public function getReferencedColumnName()
    {
        return $this->referencedColumnName;
    }
    
    public function setReferencedColumnName($referencedColumnName)
    {
        $this->referencedColumnName = $referencedColumnName;
        return $this;
    }

    public function setForeignKeyUpdateRule($foreignKeyUpdateRule)
    {
        $this->foreignKeyUpdateRule = $foreignKeyUpdateRule;
    }

    public function getForeignKeyUpdateRule()
    {
        return $this->foreignKeyUpdateRule;
    }

    public function setForeignKeyDeleteRule($foreignKeyDeleteRule)
    {
        $this->foreignKeyDeleteRule = $foreignKeyDeleteRule;
    }

    public function getForeignKeyDeleteRule()
    {
        return $this->foreignKeyUpdateRule;
    }

}
