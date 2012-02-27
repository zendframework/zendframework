<?php

namespace Zend\Db\Sql\Predicate;


class Operator implements PredicateInterface
{
    const OPERATOR_EQUAL_TO = '=';
    const OP_EQ = '=';

    const OPERATOR_NOT_EQUAL_TO = '!=';
    const OP_NE = '!=';

    const OPERATOR_LESS_THAN = '<';
    const OP_LT = '<';

    const OPERATOR_LESS_THAN_OR_EQUAL_TO = '<=';
    const OP_LTE = '<=';

    const OPERATOR_GREATER_THAN = '>';
    const OP_GT = '>';

    const OPERATOR_GREATER_THAN_OR_EQUAL_TO = '>=';
    const OP_GTE = '>=';

    protected $specification = '%1$s %2$s %3$s';
    protected $left = null;
    protected $leftType = self::TYPE_IDENTIFIER;
    protected $operator = null;
    protected $right = null;
    protected $rightType = self::TYPE_VALUE;

    public function __construct($left = null, $operator = self::OPERATOR_EQUAL_TO, $right = null, $leftType = self::TYPE_IDENTIFIER, $rightType = self::TYPE_VALUE)
    {
        if ($left) {
            $this->setLeft($left);
        }
        if ($operator) {
            $this->setOperator($operator);
        }
        if ($right) {
            $this->setRight($right);
        }
    }

    public function setLeft($identifier)
    {
        $this->left = $identifier;
    }

    public function getLeft()
    {
        return $this->left;
    }

    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function setRight($value)
    {
        $this->right = $value;
    }

    public function getRight()
    {
        return $this->right;
    }

    public function setSpecification($specification)
    {
        $this->specification = $specification;
    }

    public function getSpecification()
    {
        return $this->specification;
    }

    public function getWhereParts()
    {
        return array(
            array('%s ' . $this->operator . ' %s', array($this->left, $this->right), array($this->leftType, $this->rightType))
        );
    }


}
