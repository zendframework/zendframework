<?php

namespace Zend\Db\Metadata\Object;

class ReferentialConstraint
{
    /*
    protected $catalogName = null;
    protected $schemaName = null;
    */

    protected $uniqueConstraintCatalogName = null;
    protected $uniqueConstraintSchemaName = null;
    protected $uniqueConstraintName = null;
    protected $matchOption = null;
    protected $updateRule = null;
    protected $deleteRule = null;

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
    
    public function getUniqueConstraintCatalogName()
    {
        return $this->uniqueConstraintCatalogName;
    }
    
    public function setUniqueConstraintCatalogName($uniqueConstraintCatalogName)
    {
        $this->uniqueConstraintCatalogName = $uniqueConstraintCatalogName;
        return $this;
    }
    
    public function getUniqueConstraintSchemaName()
    {
        return $this->uniqueConstraintSchemaName;
    }
    
    public function setUniqueConstraintSchemaName($uniqueConstraintSchemaName)
    {
        $this->uniqueConstraintSchemaName = $uniqueConstraintSchemaName;
        return $this;
    }
    
    public function getUniqueConstraintName()
    {
        return $this->uniqueConstraintName;
    }
    
    public function setUniqueConstraintName($uniqueConstraintName)
    {
        $this->uniqueConstraintName = $uniqueConstraintName;
        return $this;
    }
    
    public function getMatchOption()
    {
        return $this->matchOption;
    }
    
    public function setMatchOption($matchOption)
    {
        $this->matchOption = $matchOption;
        return $this;
    }
    
    public function getUpdateRule()
    {
        return $this->updateRule;
    }
    
    public function setUpdateRule($updateRule)
    {
        $this->updateRule = $updateRule;
        return $this;
    }
    
    public function getDeleteRule()
    {
        return $this->deleteRule;
    }
    
    public function setDeleteRule($deleteRule)
    {
        $this->deleteRule = $deleteRule;
        return $this;
    }
    
}
