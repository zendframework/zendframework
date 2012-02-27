<?php

namespace Zend\Db\Sql\Predicate;

class IsNull implements PredicateInterface
{

    public function __construct(array $valueSet = array())
    {
        if ($valueSet) {
            $this->setLeftValue($valueSet);
        }
    }
    
    public function setValueSet($valueSet)
    {

    }

    /**
     * @return array
     */
    public function getWhereParts()
    {
        // TODO: Implement getWhereParts() method.
    }
}
