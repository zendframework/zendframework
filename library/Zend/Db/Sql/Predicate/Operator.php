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
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Operator implements PredicateInterface
{
    const OPERATOR_EQUAL_TO                  = '=';
    const OP_EQ                              = '=';

    const OPERATOR_NOT_EQUAL_TO              = '!=';
    const OP_NE                              = '!=';

    const OPERATOR_LESS_THAN                 = '<';
    const OP_LT                              = '<';

    const OPERATOR_LESS_THAN_OR_EQUAL_TO     = '<=';
    const OP_LTE                             = '<=';

    const OPERATOR_GREATER_THAN              = '>';
    const OP_GT                              = '>';

    const OPERATOR_GREATER_THAN_OR_EQUAL_TO  = '>=';
    const OP_GTE                             = '>=';

    protected $allowedTypes  = array(
        self::TYPE_IDENTIFIER,
        self::TYPE_VALUE,
    );
    protected $left          = null;
    protected $leftType      = self::TYPE_IDENTIFIER;
    protected $operator      = null;
    protected $right         = null;
    protected $rightType     = self::TYPE_VALUE;

    /**
     * Constructor
     * 
     * @param  mixed $left 
     * @param  string $operator 
     * @param  mixed $right 
     * @param  TYPE_IDENTIFIER|TYPE_VALUE $leftType 
     * @param  TYPE_IDENTIFIER|TYPE_VALUE $rightType 
     * @return void
     */
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
        if ($leftType) {
            $this->setLeftType($leftType);
        }
        if ($rightType) {
            $this->setRightType($rightType);
        }
    }

    /**
     * Set left side of operator
     * 
     * @param  scalar $left 
     * @return Operator
     */
    public function setLeft($left)
    {
        $this->left = $left;
        return $this;
    }

    /**
     * Get left side of operator
     * 
     * @return scalar
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * Set parameter type for left side of operator
     * 
     * @param  TYPE_IDENTIFIER|TYPE_VALUE $type
     * @return Operator
     */
    public function setLeftType($type)
    {
        if (!in_array($type, $this->allowedTypes)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid type "%s" provided; must be of type "%s" or "%s"',
                $type,
                __CLASS__ . '::TYPE_IDENTIFIER',
                __CLASS__ . '::TYPE_VALUE'
            ));
        }
        $this->leftType = $type;
        return $this;
    }

    /**
     * Get parameter type on left side of operator
     * 
     * @return string
     */
    public function getLeftType()
    {
        return $this->leftType;
    }

    /**
     * Set operator string
     * 
     * @param  string $operator 
     * @return Operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
        return $this;
    }

    /**
     * Get operator string
     * 
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Set right side of operator
     * 
     * @param  scalar $value 
     * @return Operator
     */
    public function setRight($value)
    {
        $this->right = $value;
        return $this;
    }

    /**
     * Get right side of operator
     * 
     * @return scalar
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * Set parameter type for right side of operator
     * 
     * @param  TYPE_IDENTIFIER|TYPE_VALUE $type
     * @return Operator
     */
    public function setRightType($type)
    {
        if (!in_array($type, $this->allowedTypes)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid type "%s" provided; must be of type "%s" or "%s"',
                $type,
                __CLASS__ . '::TYPE_IDENTIFIER',
                __CLASS__ . '::TYPE_VALUE'
            ));
        }
        $this->rightType = $type;
        return $this;
    }

    /**
     * Get parameter type on right side of operator
     * 
     * @return string
     */
    public function getRightType()
    {
        return $this->rightType;
    }

    /**
     * Get predicate parts for where statement
     * 
     * @return array
     */
    public function getWhereParts()
    {
        return array(array(
            '%s ' . $this->operator . ' %s', 
            array($this->left, $this->right), 
            array($this->leftType, $this->rightType)
        ));
    }
}
