<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Platform\PlatformInterface;

abstract class AbstractSql
{
    protected $specifications = array();

    protected function processExpression(ExpressionInterface $expression, PlatformInterface $platform, DriverInterface $driver = null, $namedParameterPrefix = null)
    {
        // static counter for the number of times this method was invoked across the PHP runtime
        static $runtimeExpressionPrefix = 0;

        if ($driver && ((!is_string($namedParameterPrefix) || $namedParameterPrefix == ''))) {
            $namedParameterPrefix = sprintf('expr%04dParam', ++$runtimeExpressionPrefix);
        }

        $return = array(
            'sql' => '',
            'parameters' => array()
        );

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
                    if ($driver) {
                        $name = $namedParameterPrefix . $expressionParamIndex++;
                        $return['parameters'][$name] = $value;
                        $values[$vIndex] = $driver->formatParameterName($name);
                        continue;
                    }

                    // if not a preparable statement, simply quote the value and move on
                    $values[$vIndex] = $platform->quoteValue($value);
                } elseif (isset($types[$vIndex]) && $types[$vIndex] == ExpressionInterface::TYPE_LITERAL) {
                    $values[$vIndex] = $value;
                }
            }

            // after looping the values, interpolate them into the sql string (they might be placeholder names, or values)
            $return['sql'] .= vsprintf($part[0], $values);
        }

        return $return;
    }

    /**
     * @param $specification
     * @param $parameters
     * @return string
     * @throws Exception\RuntimeException
     */
    protected function createSqlFromSpecificationAndParameters($specification, $parameters)
    {
        if (is_string($specification)) {
            return vsprintf($specification, $parameters);
        }

        $topSpec = key($specification);
        $paramSpecs = $specification[$topSpec];

        $topParameters = array();
        foreach ($parameters as $position => $paramsForPosition) {
            if (isset($paramSpecs[$position]['combinedby'])) {
                $multiParamValues = array();
                foreach ($paramsForPosition as $multiParamsForPosition) {
                    $ppCount = count($multiParamsForPosition);
                    if (!isset($paramSpecs[$position][$ppCount])) {
                        throw new Exception\RuntimeException('A number of parameters (' . $ppCount . ') was found that is not supported by this specification');
                    }
                    $multiParamValues[] = vsprintf($paramSpecs[$position][$ppCount], $multiParamsForPosition);
                }
                $topParameters[] = implode($paramSpecs[$position]['combinedby'], $multiParamValues);
            } elseif ($paramSpecs[$position] !== null) {
                $ppCount = count($paramsForPosition);
                if (!isset($paramSpecs[$position][$ppCount])) {
                    throw new Exception\RuntimeException('A number of parameters (' . $ppCount . ') was found that is not supported by this specification');
                }
                $topParameters[] = vsprintf($paramSpecs[$position][$ppCount], $paramsForPosition);
            } else {
                $topParameters[] = $paramsForPosition;
            }
        }
        return vsprintf($topSpec, $topParameters);
    }

}
