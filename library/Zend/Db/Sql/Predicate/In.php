<?php

namespace Zend\Db\Sql\Predicate;

class In implements PredicateInterface
{
    protected $specification = '%1$s IN (%2$s)';
    protected $subject;
    protected $valueSet;

    public function __construct($subject = null, array $valueSet = array())
    {
        if ($subject) {
            $this->setSubject($subject);
        }
        if ($valueSet) {
            $this->setValueSet($valueSet);
        }
    }

    public function getParameterizedSqlString(Adapter $adapter)
    {
        // TODO: Implement getParameterizedSqlString() method.
    }

    public function getParameterContainer()
    {
        // TODO: Implement getParameterContainer() method.
    }

    public function setSpecification($specification)
    {
        $this->specification = $specification;
    }

    public function getSpecification()
    {
        return $this->specification;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setValueSet($valueSet)
    {
        $this->valueSet = $valueSet;
    }

    public function getValueSet()
    {
        return $this->valueSet;
    }

    /**
     * @return array
     */
    public function getWhereParts()
    {
        // TODO: Implement getWhereParts() method.
    }
}
