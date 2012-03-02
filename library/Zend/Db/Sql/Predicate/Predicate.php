<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\Sql\Predicate;

/**
 * @property Predicate $and
 * @property Predicate $or
 * @property Predicate $AND
 * @property Predicate $OR
 * @property Predicate $NEST
 * @property Predicate $UNNEST
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 */
class Predicate extends PredicateSet
{
    protected $unnest = null;
    protected $nextPredicateCombineOperator = null;

    /**
     * Begin nesting predicates
     * 
     * @return PredicateSet
     */
    public function nest()
    {
        $predicateSet = new Predicate();
        $predicateSet->setUnnest($this);
        $this->addPredicate($predicateSet, ($this->nextPredicateCombineOperator) ?: $this->defaultCombination);
        $this->nextPredicateCombineOperator = null;
        return $predicateSet;
    }

    /**
     * Indicate what predicate will be unnested
     * 
     * @param  Predicate $predicate 
     * @return void
     */
    public function setUnnest(Predicate $predicate)
    {
        $this->unnest = $predicate;
    }

    /**
     * Indicate end of nested predicate
     *
     * @return Predicate
     * @throws \RuntimeException
     */
    public function unnest()
    {
        if ($this->unnest == null) {
            throw new \RuntimeException('Not nested');
        }
        $unnset       = $this->unnest;
        $this->unnest = null;
        return $unnset;
    }

    /**
     * Create "Equal To" predicate
     *
     * Utilizes Operator predicate
     * 
     * @param  scalar $left 
     * @param  scalar $right 
     * @param  TYPE_IDENTIFIER|TYPE_VALUE $leftType 
     * @param  TYPE_IDENTIFIER|TYPE_VALUE $rightType 
     * @return Predicate
     */
    public function equalTo($left, $right, $leftType = self::TYPE_IDENTIFIER, $rightType = self::TYPE_VALUE)
    {
        $this->addPredicate(
            new Operator($left, Operator::OPERATOR_EQUAL_TO, $right, $leftType, $rightType),
            ($this->nextPredicateCombineOperator) ?: $this->defaultCombination
        );
        $this->nextPredicateCombineOperator = null;

        return $this;
    }

    /**
     * Create "Less Than" predicate
     *
     * Utilizes Operator predicate
     * 
     * @param  scalar $left 
     * @param  scalar $right 
     * @param  TYPE_IDENTIFIER|TYPE_VALUE $leftType 
     * @param  TYPE_IDENTIFIER|TYPE_VALUE $rightType 
     * @return Predicate
     */
    public function lessThan($left, $right, $leftType = self::TYPE_IDENTIFIER, $rightType = self::TYPE_VALUE)
    {
        $this->addPredicate(
            new Operator($left, Operator::OPERATOR_LESS_THAN, $right, $leftType, $rightType),
            ($this->nextPredicateCombineOperator) ?: $this->defaultCombination
        );
        $this->nextPredicateCombineOperator = null;

        return $this;
    }

    /**
     * Create "Greater Than" predicate
     *
     * Utilizes Operator predicate
     * 
     * @param  scalar $left 
     * @param  scalar $right 
     * @param  TYPE_IDENTIFIER|TYPE_VALUE $leftType 
     * @param  TYPE_IDENTIFIER|TYPE_VALUE $rightType 
     * @return Predicate
     */
    public function greaterThan($left, $right, $leftType = self::TYPE_IDENTIFIER, $rightType = self::TYPE_VALUE)
    {
        $this->addPredicate(
            new Operator($left, Operator::OPERATOR_GREATER_THAN, $right, $leftType, $rightType),
            ($this->nextPredicateCombineOperator) ?: $this->defaultCombination
        );
        $this->nextPredicateCombineOperator = null;

        return $this;
    }

    /**
     * Create "Less Than Or Equal To" predicate
     *
     * Utilizes Operator predicate
     * 
     * @param  scalar $left 
     * @param  scalar $right 
     * @param  TYPE_IDENTIFIER|TYPE_VALUE $leftType 
     * @param  TYPE_IDENTIFIER|TYPE_VALUE $rightType 
     * @return Predicate
     */
    public function lessThanOrEqualTo($left, $right, $leftType = self::TYPE_IDENTIFIER, $rightType = self::TYPE_VALUE)
    {
        $this->addPredicate(
            new Operator($left, Operator::OPERATOR_LESS_THAN_OR_EQUAL_TO, $right, $leftType, $rightType),
            ($this->nextPredicateCombineOperator) ?: $this->defaultCombination
        );
        $this->nextPredicateCombineOperator = null;

        return $this;
    }

    /**
     * Create "Greater Than Or Equal To" predicate
     *
     * Utilizes Operator predicate
     * 
     * @param  scalar $left 
     * @param  scalar $right 
     * @param  TYPE_IDENTIFIER|TYPE_VALUE $leftType 
     * @param  TYPE_IDENTIFIER|TYPE_VALUE $rightType 
     * @return Predicate
     */
    public function greaterThanOrEqualTo($left, $right, $leftType = self::TYPE_IDENTIFIER, $rightType = self::TYPE_VALUE)
    {
        $this->addPredicate(
            new Operator($left, Operator::OPERATOR_GREATER_THAN_OR_EQUAL_TO, $right, $leftType, $rightType),
            ($this->nextPredicateCombineOperator) ?: $this->defaultCombination
        );
        $this->nextPredicateCombineOperator = null;

        return $this;
    }

    /**
     * Create "Like" predicate
     *
     * Utilizes Like predicate
     * 
     * @param  string $identifier 
     * @param  string $like
     * @return Predicate
     */
    public function like($identifier, $like)
    {
        $this->addPredicate(
            new Like($identifier, $like),
            ($this->nextPredicateCombineOperator) ?: $this->defaultCombination
        );
        $this->nextPredicateCombineOperator = null;

        return $this;
    }

    /**
     * Create "Literal" predicate
     *
     * Utilizes Like predicate
     * 
     * @param  string $literal 
     * @param  scalar|array $parameter
     * @return Predicate
     */
    public function literal($literal, $parameter)
    {
        $this->addPredicate(
            new Literal($literal, $parameter),
            ($this->nextPredicateCombineOperator) ?: $this->defaultCombination
        );
        $this->nextPredicateCombineOperator = null;

        return $this;
    }

    /**
     * Create "IS NULL" predicate
     *
     * Utilizes IsNull predicate
     * 
     * @param  string $identifier 
     * @return Predicate
     */
    public function isNull($identifier)
    {
        $this->addPredicate(
            new IsNull($identifier),
            ($this->nextPredicateCombineOperator) ?: $this->defaultCombination
        );
        $this->nextPredicateCombineOperator = null;

        return $this;
    }

    /**
     * Create "IS NOT NULL" predicate
     *
     * Utilizes IsNotNull predicate
     * 
     * @param  string $identifier 
     * @return Predicate
     */
    public function isNotNull($identifier)
    {
        $this->addPredicate(
            new IsNotNull($identifier),
            ($this->nextPredicateCombineOperator) ?: $this->defaultCombination
        );
        $this->nextPredicateCombineOperator = null;

        return $this;
    }

    /**
     * Create "in" predicate
     *
     * Utilizes In predicate
     * 
     * @param  string $identifier 
     * @param  array $valueSet 
     * @return Predicate
     */
    public function in($identifier, array $valueSet = array())
    {
        $this->addPredicate(
            new In($identifier, $valueSet),
            ($this->nextPredicateCombineOperator) ?: $this->defaultCombination
        );
        $this->nextPredicateCombineOperator = null;

        return $this;
    }

    /**
     * Create "between" predicate
     *
     * Utilizes Between predicate
     * 
     * @param  string $identifier 
     * @param  scalar $minValue 
     * @param  scalar $maxValue 
     * @return Predicate
     */
    public function between($identifier, $minValue, $maxValue)
    {
        $this->addPredicate(
            new Between($identifier, $minValue, $maxValue),
            ($this->nextPredicateCombineOperator) ?: $this->defaultCombination
        );
        $this->nextPredicateCombineOperator = null;

        return $this;
    }

    /**
     * Overloading
     *
     * Overloads "or", "and", "nest", and "unnest"
     * 
     * @param  string $name 
     * @return Predicate
     */
    public function __get($name)
    {
        switch (strtolower($name)) {
            case 'or':
                $this->nextPredicateCombineOperator = self::OP_OR;
                break;
            case 'and':
                $this->nextPredicateCombineOperator = self::OP_AND;
                break;
            case 'nest':
                return $this->nest();
            case 'unnest':
                return $this->unnest();
        }
        return $this;
    }
}
