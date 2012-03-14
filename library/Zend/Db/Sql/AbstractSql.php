<?php

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Platform\PlatformInterface,
    Zend\Db\Adapter\Driver\DriverInterface;

abstract class AbstractSql
{
    protected function processExpression(ExpressionInterface $expression, PlatformInterface $platform, DriverInterface $driver = null)
    {
        $return = array(
            'sql' => '',
            'parameters' => array()
        );

        if ($driver !== null) {
            $prepareType = $driver->getPrepareType();
        }

        $parts = $expression->getExpressionData();
        $expressionPart = '';
        $expressionParamIndex = 1;
        foreach ($parts as $part) {
            if (is_string($part)) {
                $expressionPart .= $part;
            } elseif (is_array($part)) {
                $values = $part[1];
                $types = (isset($part[2])) ? $part[2] : array();
                foreach ($values as $vIndex => $value) {
                    if (isset($types[$vIndex]) && $types[$vIndex] == ExpressionInterface::TYPE_IDENTIFIER) {
                        $values[$vIndex] = $platform->quoteIdentifierInFragment($value);
                    } elseif (isset($types[$vIndex]) && $types[$vIndex] == ExpressionInterface::TYPE_VALUE) {
                        if (isset($prepareType)) {
                            if ($prepareType == 'positional') {
                                $return['parameters'][] = $value;
                                $values[$vIndex] = $driver->formatParameterName(null);
                            } elseif ($prepareType == 'named') {
                                // @todo use expression specific name
                                $name = 'exprParam' . $expressionParamIndex++;
                                $return['parameters'][$name] = $value;
                                $values[$vIndex] = $driver->formatParameterName($name);
                            }
                        } else {
                            $values[$vIndex] = $platform->quoteValue($value);
                        }
                    }
                }
                $expressionPart .= vsprintf($part[0], $values);
            }
        }

        $return['sql'] = $expressionPart;
        return $return;
    }
}