<?php

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Platform\PlatformInterface,
    Zend\Db\Adapter\Driver\DriverInterface;

abstract class AbstractSql
{
    protected $specifications = array();

    protected function processExpression(ExpressionInterface $expression, PlatformInterface $platform, DriverInterface $driver = null, $namedParameterPrefix = null)
    {
        // static counter for the number of times this method was invoked across the PHP runtime
        static $runtimeExpressionPrefix = 0;

        $return = array(
            'sql' => '',
            'parameters' => array()
        );

        // get the prepare type from the driver,
        if ($driver !== null) {
            $prepareType = $driver->getPrepareType();
            if ((!is_string($namedParameterPrefix) || $namedParameterPrefix == '') && $prepareType == 'named') {
                $namedParameterPrefix = sprintf('expr%04dParam', ++$runtimeExpressionPrefix);
            }
        }

        // initialize variables
        $parts = $expression->getExpressionData();
        $expressionParamIndex = 1;

        foreach ($parts as $part) {

            // if it is a string, simply tack it onto the return sql "specification" string
            if (is_string($part)) {
                $return['sql'] .= $part;
                continue;
            }

            if (!is_array($part)) {
                throw new Exception\RuntimeException('Elements returned from getExpressionData() array must be a string or array.');
            }

            // process values and types (the middle and last position of the expression data)
            $values = $part[1];
            $types = (isset($part[2])) ? $part[2] : array();
            foreach ($values as $vIndex => $value) {
                if (isset($types[$vIndex]) && $types[$vIndex] == ExpressionInterface::TYPE_IDENTIFIER) {
                    $values[$vIndex] = $platform->quoteIdentifierInFragment($value);
                } elseif (isset($types[$vIndex]) && $types[$vIndex] == ExpressionInterface::TYPE_VALUE) {

                    // if prepareType is set, it means that this particular value must be
                    // passed back to the statement in a way it can be used as a placeholder value
                    if (isset($prepareType)) {
                        if ($prepareType == 'positional') {
                            $return['parameters'][] = $value;
                            $values[$vIndex] = $driver->formatParameterName(null);
                        } elseif ($prepareType == 'named') {
                            $name = $namedParameterPrefix . $expressionParamIndex++;
                            $return['parameters'][$name] = $value;
                            $values[$vIndex] = $driver->formatParameterName($name);
                        }
                        continue;
                    }

                    // if not a preparable statement, simply quote the value and move on
                    $values[$vIndex] = $platform->quoteValue($value);
                }
            }

            // after looping the values, interpolate them into the sql string (they might be placeholder names, or values)
            $return['sql'] .= vsprintf($part[0], $values);
        }

        return $return;
    }

    protected function applySpecification($name, array $vArgs)
    {
        if (!array_key_exists($name, $this->specifications)) {
            throw new \RuntimeException('Invalid specification index');
        }

        return vsprintf($this->specifications[$name], $vArgs);
    }
}