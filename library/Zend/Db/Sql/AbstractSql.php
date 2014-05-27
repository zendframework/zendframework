<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\ParameterContainer;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Adapter\StatementContainer;
use Zend\Db\Sql\Platform\PlatformDecoratorInterface;

abstract class AbstractSql
{
    /**
     * @var array
     */
    protected $specifications = array();

    /**
     * @var string
     */
    protected $processInfo = array('paramPrefix' => '', 'subselectCount' => 0);

    /**
     * @var array
     */
    protected $instanceParameterIndex = array();

    protected function processExpression(ExpressionInterface $expression, PlatformInterface $platform, DriverInterface $driver = null, ParameterContainer $parameterContainer = null, $namedParameterPrefix = null)
    {
        // static counter for the number of times this method was invoked across the PHP runtime
        static $runtimeExpressionPrefix = 0;

        if ($parameterContainer && ((!is_string($namedParameterPrefix) || $namedParameterPrefix == ''))) {
            $namedParameterPrefix = sprintf('expr%04dParam', ++$runtimeExpressionPrefix);
        }

        $sql = '';
        $statementContainer = new StatementContainer;
        $parameterContainerInternal = $parameterContainer
                ? $statementContainer->getParameterContainer()
                : null;

        // initialize variables
        $parts = $expression->getExpressionData();

        if (!isset($this->instanceParameterIndex[$namedParameterPrefix])) {
            $this->instanceParameterIndex[$namedParameterPrefix] = 1;
        }

        $expressionParamIndex = &$this->instanceParameterIndex[$namedParameterPrefix];

        foreach ($parts as $part) {

            // if it is a string, simply tack it onto the return sql "specification" string
            if (is_string($part)) {
                $sql .= $part;
                continue;
            }

            if (!is_array($part)) {
                throw new Exception\RuntimeException('Elements returned from getExpressionData() array must be a string or array.');
            }

            // process values and types (the middle and last position of the expression data)
            $values = $part[1];
            $types = isset($part[2]) ? $part[2] : array();
            foreach ($values as $vIndex => $value) {
                if (!isset($types[$vIndex])) {
                    continue;
                }
                $type = $types[$vIndex];
                if ($value instanceof Select) {
                    // process sub-select
                    $values[$vIndex] = '(' . $this->processSubSelect($value, $platform, $driver, $parameterContainer) . ')';
                } elseif($value instanceof ExpressionInterface) {
                    // recursive call to satisfy nested expressions
                    $innerStatementContainer = $this->processExpression($value, $platform, $driver, $parameterContainer, $namedParameterPrefix . $vIndex . 'subpart');
                    $values[$vIndex] = $innerStatementContainer->getSql();
                    if ($parameterContainerInternal) {
                        $parameterContainerInternal->merge($innerStatementContainer->getParameterContainer());
                    }
                } elseif ($type == ExpressionInterface::TYPE_IDENTIFIER) {
                    $values[$vIndex] = $platform->quoteIdentifierInFragment($value);
                } elseif ($type == ExpressionInterface::TYPE_VALUE) {
                    // if prepareType is set, it means that this particular value must be
                    // passed back to the statement in a way it can be used as a placeholder value
                    if ($parameterContainerInternal) {
                        $name = $namedParameterPrefix . $expressionParamIndex++;
                        $parameterContainerInternal->offsetSet($name, $value);
                        $values[$vIndex] = $driver->formatParameterName($name);
                        continue;
                    }

                    // if not a preparable statement, simply quote the value and move on
                    $values[$vIndex] = $platform->quoteValue($value);
                } elseif ($type == ExpressionInterface::TYPE_LITERAL) {
                    $values[$vIndex] = $value;
                }
            }

            // after looping the values, interpolate them into the sql string (they might be placeholder names, or values)
            $sql .= vsprintf($part[0], $values);
        }

        $statementContainer->setSql($sql);
        return $statementContainer;
    }

    /**
     * @param $specifications
     * @param $parameters
     * @return string
     * @throws Exception\RuntimeException
     */
    protected function createSqlFromSpecificationAndParameters($specifications, $parameters)
    {
        if (is_string($specifications)) {
            return vsprintf($specifications, $parameters);
        }

        $parametersCount = count($parameters);
        foreach ($specifications as $specificationString => $paramSpecs) {
            if ($parametersCount == count($paramSpecs)) {
                break;
            }
            unset($specificationString, $paramSpecs);
        }

        if (!isset($specificationString)) {
            throw new Exception\RuntimeException(
                'A number of parameters was found that is not supported by this specification'
            );
        }

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
        return vsprintf($specificationString, $topParameters);
    }

    protected function processSubSelect(Select $subselect, PlatformInterface $platform, DriverInterface $driver = null, ParameterContainer $parameterContainer = null)
    {
        if ($parameterContainer) {
            $stmtContainer = new StatementContainer;

            // Track subselect prefix and count for parameters
            $this->processInfo['subselectCount']++;
            $subselect->processInfo['subselectCount'] = $this->processInfo['subselectCount'];
            $subselect->processInfo['paramPrefix'] = 'subselect' . $subselect->processInfo['subselectCount'];

            // call subselect
            if ($this instanceof PlatformDecoratorInterface) {
                /** @var Select|PlatformDecoratorInterface $subselectDecorator */
                $subselectDecorator = clone $this;
                $subselectDecorator->setSubject($subselect);
                $subselectDecorator->prepareStatement(new \Zend\Db\Adapter\Adapter($driver, $platform), $stmtContainer);
            } else {
                $subselect->prepareStatement(new \Zend\Db\Adapter\Adapter($driver, $platform), $stmtContainer);
            }

            // copy count
            $this->processInfo['subselectCount'] = $subselect->processInfo['subselectCount'];

            $parameterContainer->merge($stmtContainer->getParameterContainer()->getNamedArray());
            $sql = $stmtContainer->getSql();
        } else {
            if ($this instanceof PlatformDecoratorInterface) {
                $subselectDecorator = clone $this;
                $subselectDecorator->setSubject($subselect);
                $sql = $subselectDecorator->getSqlString($platform);
            } else {
                $sql = $subselect->getSqlString($platform);
            }
        }
        return $sql;
    }

    /**
     * @param null|array|ExpressionInterface|Select $column
     * @param PlatformInterface $platform
     * @param null|DriverInterface $driver
     * @param null|string $namedParameterPrefix
     * @param null|ParameterContainer $parameterContainer
     * @return string
     */
    protected function resolveColumnValue($column, PlatformInterface $platform, DriverInterface $driver = null, $namedParameterPrefix = null, ParameterContainer $parameterContainer = null)
    {
        $isIdentifier = false;
        $fromTable = '';
        if (is_array($column)) {
            if (isset($column['isIdentifier'])) {
                $isIdentifier = (bool) $column['isIdentifier'];
            }
            if (isset($column['fromTable']) && $column['fromTable'] !== null) {
                $fromTable = $column['fromTable'];
            }
            $column = $column['column'];
        }

        if ($column instanceof ExpressionInterface) {
            $exprData = $this->processExpression($column, $platform, $driver, $parameterContainer, $namedParameterPrefix);
            if ($parameterContainer) {
                $parameterContainer->merge($exprData->getParameterContainer());
            }
            return $exprData->getSql();
        }
        if ($column instanceof Select) {
            return '(' . $this->processSubSelect($column, $platform, $driver, $parameterContainer) . ')';
        }
        if ($column === null) {
            return 'NULL';
        }
        return $isIdentifier
                ? $fromTable . $platform->quoteIdentifierInFragment($column)
                : $platform->quoteValue($column);
    }

    /**
     * @param string|TableIdentifier|Select $table
     * @param PlatformInterface $platform
     * @param DriverInterface $driver
     * @param ParameterContainer $parameterContainer
     * @return string
     */
    protected function resolveTable($table, PlatformInterface $platform, DriverInterface $driver = null, ParameterContainer $parameterContainer = null)
    {
        $schema = null;
        if ($table instanceof TableIdentifier) {
            list($table, $schema) = $table->getTableAndSchema();
        }

        if ($table instanceof Select) {
            $table = '(' . $this->processSubselect($table, $platform, $driver, $parameterContainer) . ')';
        } elseif ($table) {
            $table = $platform->quoteIdentifier($table);
        }

        if ($schema && $table) {
            $table = $platform->quoteIdentifier($schema) . $platform->getIdentifierSeparator() . $table;
        }
        return $table;
    }

    /**
     * @param \Zend\Db\Adapter\StatementContainerInterface $statementContainer
     * @return ParameterContainer
     */
    protected function resolveParameterContainer($statementContainer)
    {
        $parameterContainer = $statementContainer->getParameterContainer();
        if (!$parameterContainer instanceof ParameterContainer) {
            $parameterContainer = new ParameterContainer();
            $statementContainer->setParameterContainer($parameterContainer);
        }
        return $parameterContainer;
    }
}
