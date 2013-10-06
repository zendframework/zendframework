<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql;

use Zend\Db\Sql\Platform\PlatformDecoratorInterface;

abstract class AbstractExpression implements ExpressionInterface
{
    protected $allowedTypes = array(
        self::TYPE_IDENTIFIER,
        self::TYPE_LITERAL,
        self::TYPE_SELECT,
        self::TYPE_VALUE,
    );

    protected function normalizeArgument($argument, $defaultType = self::TYPE_VALUE)
    {
        $argumentType = null;
        if ($argument instanceof ExpressionInterface || $argument instanceof SqlInterface) {
            $argumentType = self::TYPE_VALUE;
        } elseif (is_scalar($argument) || $argument === null) {
            $argumentType = $defaultType;
        } elseif (is_array($argument)) {
            $k = key($argument);
            $v = current($argument);
            if ($v instanceof ExpressionInterface || $v instanceof SqlInterface) {
                $argument = $v;
                $argumentType = self::TYPE_VALUE;
            } elseif (is_integer($k) && !in_array($v, $this->allowedTypes)) {
                $argument = $v;
                $argumentType = $defaultType;
            } else {
                $argument = $k;
                $argumentType = $v;
            }
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                '$argument should be %s or %s or %s or %s or %s',
                'null',
                'scalar',
                'array',
                'Zend\Db\Sql\ExpressionInterface',
                'Zend\Db\Sql\SqlInterface'
            ));
        }
        if (!in_array($argumentType, $this->allowedTypes)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Argument type should be in array(%s)',
                implode(',', $this->allowedTypes)
            ));
        }
        return array(
            $argument,
            $argumentType,
        );
    }

    protected function localizeVariablesForDecorator($source, array $excludeVariables = array())
    {
        if (!$this instanceof PlatformDecoratorInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                'function AbstractExpression::localizeVariablesForDecorator can only be called for %s.',
                'Zend\Db\Sql\Platform\PlatformDecoratorInterface'
            ));
        }
        // localize variables
        foreach (get_object_vars($source) as $name => $value) {
            if (!in_array($name, $excludeVariables)) {
                $this->{$name} = $value;
            }
        }
        return $this;
    }
}
