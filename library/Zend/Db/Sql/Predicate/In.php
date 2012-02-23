<?php

namespace Zend\Db\Sql\Predicate;

class In extends AbstractPredicate
{
    protected $valueSet;
    
    public function __construct($subject = null, array $valueSet = array())
    {
        (empty($subject)) ?: $this->setSubject($subject);
        (empty($valueSet)) ?: $this->setValueSet($valueSet);
    }
    
    public function setValueSet($valueSet)
    {
        $this->valueSet = $valueSet;
        return $this;
    }
    
    public function toPreparedString($type = null) {}
    public function getValues($type = null) {}
    
    public function toString()
    {
        $sqlPredicate = $this->subject . ' IN (';
        $sqlPredicateValues = array();
        foreach ($this->valueSet as $value) {
            $sqlPredicateValues[] = null;
        }
    }
    
}
