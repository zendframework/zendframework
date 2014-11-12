<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\StatementContainerInterface;
use Zend\Db\Adapter\Platform\Sql92 as AdapterSql92Platform;
use Zend\Db\Adapter\ParameterContainer;

/**
 * Combine SQL statement - allows combining multiple select statements into one
 */
class Combine extends AbstractSql implements SqlInterface, PreparableSqlInterface
{
    const COLUMNS = 'columns';
    const COMBINE = 'combine';
    const COMBINE_UNION = 'union';
    const COMBINE_EXCEPT = 'except';
    const COMBINE_INTERSECT = 'intersect';

    protected $specifications = array(
        self::COMBINE => '%1$s (%2$s) ',
    );

    /**
     * @var array[]
     */
    protected $combine = array();

    /**
     * @param Select|array|null $select
     * @param string            $type
     * @param string            $modifier
     */
    public function __construct($select = null, $type = self::COMBINE_UNION, $modifier = '')
    {
        if ($select) {
            $this->combine($select, $type, $modifier);
        }
    }

    /**
     * Create combine clause
     *
     * @param Select|array $select
     * @param string $type
     * @param string $modifier
     *
     * @return self
     */
    public function combine($select, $type = self::COMBINE_UNION, $modifier = '')
    {
        if (is_array($select)) {
            foreach($select as $combine) {
                if ($combine instanceof Select) {
                    $combine = array($combine);
                }
                $this->combine(
                    $combine[0],
                    isset($combine[1]) ? $combine[1] : $type,
                    isset($combine[2]) ? $combine[2] : $modifier
                );
            }
            return $this;
        }
        if (!$select instanceof Select) {
            throw new Exception\InvalidArgumentException('$select must be a array or instance of Select.');
        }
        $this->combine[] = array(
            'select' => $select,
            'type' => $type,
            'modifier' => $modifier
        );
        return $this;
    }

    /**
     * Create union clause
     *
     * @param Select|array $select
     * @param string       $modifier
     *
     * @return self
     */
    public function union($select, $modifier = '')
    {
        return $this->combine($select, self::COMBINE_UNION, $modifier);
    }

    /**
     * Create except clause
     *
     * @param Select|array $select
     * @param string       $modifier
     *
     * @return self
     */
    public function except($select, $modifier = '')
    {
        return $this->combine($select, self::COMBINE_EXCEPT, $modifier);
    }

    /**
     * Create intersect clause
     *
     * @param Select|array $select
     * @param string $modifier
     * @return self
     */
    public function intersect($select, $modifier = '')
    {
        return $this->combine($select, self::COMBINE_INTERSECT, $modifier);
    }

    /**
     * Get SQL string for statement
     *
     * @param PlatformInterface $adapterPlatform
     *
     * @return string
     */
    public function getSqlString(PlatformInterface $adapterPlatform = null)
    {
        $adapterPlatform = ($adapterPlatform) ?: new AdapterSql92Platform;
        return $this->buildSqlString($adapterPlatform);
    }

    /**
     * Prepare statement
     *
     * @param AdapterInterface            $adapter
     * @param StatementContainerInterface $statementContainer
     *
     * @return void
     */
    public function prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer = null)
    {
        $statementContainer = ($statementContainer) ?: $adapter->getDriver()->createStatement();
        $parameterContainer = $statementContainer->getParameterContainer();

        if (!$parameterContainer instanceof ParameterContainer) {
            $parameterContainer = new ParameterContainer();
            $statementContainer->setParameterContainer($parameterContainer);
        }

        $sql = $this->buildSqlString($adapter->getPlatform(), $adapter->getDriver(), $parameterContainer);
        return $statementContainer->setSql($sql);
    }

    /**
     * Build sql string
     *
     * @param PlatformInterface  $platform
     * @param DriverInterface    $driver
     * @param ParameterContainer $parameterContainer
     *
     * @return string
     */
    protected function buildSqlString(PlatformInterface $platform, DriverInterface $driver = null, ParameterContainer $parameterContainer = null)
    {
        if (!$this->combine) {
            return null;
        }

        $sql = '';
        foreach ($this->combine as $i => $combine) {
            $type = $i == 0
                    ? ''
                    : strtoupper($combine['type'] . ($combine['modifier'] ? ' ' . $combine['modifier'] : ''));
            $select = $this->processSubSelect($combine['select'], $platform, $driver, $parameterContainer);
            $sql .= sprintf(
                $this->specifications[self::COMBINE],
                $type,
                $select
            );
        }
        return trim($sql, ' ');
    }

    /**
     * @return $this
     */
    public function alignColumns()
    {
        if (!$this->combine) {
            return $this;
        }

        $allColumns = array();
        foreach($this->combine as $combine) {
            $allColumns = array_merge(
                $allColumns,
                $combine['select']->getRawState(self::COLUMNS)
            );
        }

        foreach($this->combine as $combine) {
            $combineColumns = $combine['select']->getRawState(self::COLUMNS);
            $aligned = array();
            foreach($allColumns as $alias => $column) {
                $aligned[$alias] = isset($combineColumns[$alias])
                    ? $combineColumns[$alias]
                    : new Predicate\Expression('NULL');
            }
            $combine['select']->columns($aligned, false);
        }
        return $this;
    }

    /**
     * Get raw state
     *
     * @return array
     */
    public function getRawState($key = null)
    {
        $rawState = array(
            self::COMBINE => $this->combine,
            self::COLUMNS => $this->combine
                                ? $this->combine[0]['select']->getRawState(self::COLUMNS)
                                : array(),
        );
        return (isset($key) && array_key_exists($key, $rawState)) ? $rawState[$key] : $rawState;
    }
}
