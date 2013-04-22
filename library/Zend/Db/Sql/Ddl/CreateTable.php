<?php

namespace Zend\Db\Sql\Ddl;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Sql\AbstractSql;
use Zend\Db\Sql\Exception;
use Zend\Db\Adapter\Platform\Sql92 as AdapterSql92Platform;

class CreateTable extends AbstractSql implements SqlInterface
{
    const TABLE = 'table';
    const COLUMNS = 'columns';
    const OPTIONS = 'options';

    protected $specifications = array(
        self::TABLE => array(
            'CREATE TABLE %1$s' => array(null)
        ),
        self::COLUMNS  => array(
            "(\n    %1\$s\n)" => array(
                array(1 => '%1$s', 'combinedby' => "\n    ")
            )
        ),
    );

    protected $isTemporary = false;
    protected $table = '';
    protected $columns = array();

    public function __construct($table = '', $isTemporary = false)
    {
        $this->table = $table;
    }

    public function setTemporary($temporary)
    {
        $this->isTemporary = (bool) $temporary;
    }

    public function setTable($name)
    {
        $this->table = $name;
    }

    public function addColumn(Column\ColumnInterface $column)
    {
        $this->columns[] = $column;
    }

    protected function processTable(PlatformInterface $adapterPlatform = null)
    {
        $ret = array();
        if ($this->isTemporary) {
            $ret[] = 'TEMPORARY';
        }
        $ret[] = $adapterPlatform->quoteIdentifier($this->table);
        return $ret;
    }

    protected function processColumns(PlatformInterface $adapterPlatform = null)
    {
        $sqls = array();
        foreach ($this->columns as $column) {
            $sqls[] = $this->processExpression($column, $adapterPlatform);
        }

        return array($sqls);
    }

    public function getSqlString(PlatformInterface $adapterPlatform = null)
    {
        // get platform, or create default
        $adapterPlatform = ($adapterPlatform) ?: new AdapterSql92Platform;

        $sqls = array();
        $parameters = array();

        foreach ($this->specifications as $name => $specification) {
            $parameters[$name] = $this->{'process' . $name}($adapterPlatform, null, null, $sqls, $parameters);
            if ($specification && is_array($parameters[$name])) {
                $sqls[$name] = $this->createSqlFromSpecificationAndParameters($specification, $parameters[$name]);
            }
        }

        $sql = implode(' ', $sqls);
        return $sql;
    }
//
//    /**
//     * @param $specifications
//     * @param $parameters
//     * @return string
//     * @throws Exception\RuntimeException
//     */
//    protected function createSqlFromSpecificationAndParameters($specifications, $parameters)
//    {
//        if (is_string($specifications)) {
//            return vsprintf($specifications, $parameters);
//        }
//
//        $parametersCount = count($parameters);
//        foreach ($specifications as $specificationString => $paramSpecs) {
//            if ($parametersCount == count($paramSpecs)) {
//                break;
//            }
//            unset($specificationString, $paramSpecs);
//        }
//
//        if (!isset($specificationString)) {
//            throw new Exception\RuntimeException(
//                'A number of parameters was found that is not supported by this specification'
//            );
//        }
//
//        $topParameters = array();
//        foreach ($parameters as $position => $paramsForPosition) {
//            if (isset($paramSpecs[$position]['combinedby'])) {
//                $multiParamValues = array();
//                foreach ($paramsForPosition as $multiParamsForPosition) {
//                    $ppCount = count($multiParamsForPosition);
//                    if (!isset($paramSpecs[$position][$ppCount])) {
//                        throw new Exception\RuntimeException('A number of parameters (' . $ppCount . ') was found that is not supported by this specification');
//                    }
//                    $multiParamValues[] = vsprintf($paramSpecs[$position][$ppCount], $multiParamsForPosition);
//                }
//                $topParameters[] = implode($paramSpecs[$position]['combinedby'], $multiParamValues);
//            } elseif ($paramSpecs[$position] !== null) {
//                $ppCount = count($paramsForPosition);
//                if (!isset($paramSpecs[$position][$ppCount])) {
//                    throw new Exception\RuntimeException('A number of parameters (' . $ppCount . ') was found that is not supported by this specification');
//                }
//                $topParameters[] = vsprintf($paramSpecs[$position][$ppCount], $paramsForPosition);
//            } else {
//                $topParameters[] = $paramsForPosition;
//            }
//        }
//        return vsprintf($specificationString, $topParameters);
//    }
}