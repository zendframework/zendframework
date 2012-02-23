<?php

namespace Zend\Db\Sql\Predicate;

class Between extends AbstractPredicate
{
    protected $leftValue;
    protected $rightValue;
    
    public function __construct($subject = null, $leftValue = null, $rightValue = null)
    {
        parent::__construct($subject);
        if ($leftValue) {
            $this->setLeftValue($leftValue);
        }
        if ($rightValue) {
            $this->setRightValue($rightValue);
        }
    }
    
    public function setLeftValue($value)
    {
        $this->leftValue = $value;
        return $this;
    }
    
    public function setRightValue($value)
    {
        $this->rightValue = $value;
        return $this;
    }
    
    public function toPreparedString($type = null)
    {
    }
    
    public function getValues($type = null)
    {
        
    }
    
    public function toString()
    {
        return $this->subject . ' BETWEEN ' . $this->leftValue . ' AND ' . $this->rightValue;
    }
    
}
