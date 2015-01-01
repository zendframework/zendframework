<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Ddl\Constraint;

class Check extends AbstractConstraint
{
    /**
     * @var string|\Zend\Db\Sql\ExpressionInterface
     */
    protected $expression;

    /**
     * {@inheritDoc}
     */
    protected $specification = 'CHECK (%s)';

    /**
     * @param  string|\Zend\Db\Sql\ExpressionInterface $expression
     * @param  null|string $name
     */
    public function __construct($expression, $name)
    {
        $this->expression = $expression;
        $this->name       = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getExpressionData()
    {
        $newSpecTypes = array(self::TYPE_LITERAL);
        $values       = array($this->expression);
        $newSpec      = '';

        if ($this->name) {
            $newSpec .= $this->namedSpecification;

            array_unshift($values, $this->name);
            array_unshift($newSpecTypes, self::TYPE_IDENTIFIER);
        }

        return array(array(
            $newSpec . $this->specification,
            $values,
            $newSpecTypes,
        ));
    }
}
