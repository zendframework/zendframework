<?php

namespace Zend\Db\Sql\Predicate;

class Between implements PredicateInterface
{
    protected $specification = '%1$s BETWEEN %2$s AND %3$s';
    protected $subject = null;
    protected $leftValue = null;
    protected $rightValue = null;
    
    public function __construct($subject = null, $leftValue = null, $rightValue = null)
    {
        if ($subject) {
            $this->setSubject($subject);
        }
        if ($leftValue) {
            $this->setLeftValue($leftValue);
        }
        if ($rightValue) {
            $this->setRightValue($rightValue);
        }
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setLeftValue($leftValue)
    {
        $this->leftValue = $leftValue;
    }

    public function getLeftValue()
    {
        return $this->leftValue;
    }

    public function setRightValue($rightValue)
    {
        $this->rightValue = $rightValue;
    }

    public function getRightValue()
    {
        return $this->rightValue;
    }

    public function setSpecification($specification)
    {
        $this->specification = $specification;
    }

    public function getSpecification()
    {
        return $this->specification;
    }


    /**
     * @return array
     */
    public function getWhereParts()
    {
        // TODO: Implement getWhereParts() method.
    }
}
