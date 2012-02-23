<?php

namespace Zend\Db\Sql\Predicate;

class PredicateSet implements Predicate
{
    const COMBINED_BY_AND = 'and';
    const COMBINED_BY_OR = 'or';
    const ANDed = 'and';
    const ORed = 'or';
    
    protected $combination = self::LOGICAL_AND;
    
    protected $predicates = array();
    
    public function __construct(array $predicates = null, $combination = self::COMBINED_BY_AND)
    {
        if ($predicates) {
            $this->predicates = $predicates;
        }
    }
    
    public function toPreparedString($type = null)
    {
        
    }
    
    public function toString()
    {
        
    }
    
}