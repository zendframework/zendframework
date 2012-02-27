<?php

namespace Zend\Db\Sql\Predicate;

class PredicateSet implements PredicateInterface
{
    const COMBINED_BY_AND = 'AND';
    const OP_AND = 'AND';

    const COMBINED_BY_OR = 'OR';
    const OP_OR = 'OR';
    
    protected $defaultCombination = self::COMBINED_BY_AND;

    protected $predicates = array();
    
    public function __construct(array $predicates = null, $defaultCombination = self::COMBINED_BY_AND)
    {
        $this->defaultCombination = $defaultCombination;
        if ($predicates) {
            foreach ($this->predicates as $predicate) {
                $this->addPredicate($predicate);
            }
        }
    }

    public function addPredicate(PredicateInterface $predicate, $combination = null)
    {
        if ($combination === null || !in_array($combination, array(self::OP_AND, self::OP_OR))) {
            $combination = $this->defaultCombination;
        }
        if ($combination == self::OP_OR) {
            $this->orPredicate($predicate);
        } else {
            $this->andPredicate($predicate);
        }
    }

    public function orPredicate(PredicateInterface $predicate)
    {
        $this->predicates[] = array(self::OP_OR, $predicate);
    }

    public function andPredicate(PredicateInterface $predicate)
    {
        $this->predicates[] = array(self::OP_AND, $predicate);
    }

    /**
     * @return array
     */
    public function getWhereParts()
    {
        $parts = array();
        for ($i = 0; $i < count($this->predicates); $i++) {

            /** @var $predicate PredicateInterface */
            $predicate = $this->predicates[$i][1];

            if ($predicate instanceof PredicateSet) {
                $parts[] = '(';
            }

            $parts = array_merge($parts, $predicate->getWhereParts());

            if ($predicate instanceof PredicateSet) {
                $parts[] = ')';
            }

            if (isset($this->predicates[$i+1])) {
                $parts[] .= ' ' . $this->predicates[$i+1][0] . ' ';
            }
        }
        return $parts;
    }

    public function count()
    {
        return count($this->predicates);
    }
}
