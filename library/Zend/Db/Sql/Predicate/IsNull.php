<?php

namespace Zend\Db\Sql\Predicate;

class IsNull implements \Zend\Db\Sql\SqlPreparedStatement
{
    //protected $;
    
    public function __construct(array $valueSet = array())
    {
        if ($valueSet) {
            $this->setLeftValue($valueSet);
        }
    }
    
    public function setValueSet($valueSet) {}
    
    public function toPreparedString($type = null) {}
    public function toString() {}
    
}
