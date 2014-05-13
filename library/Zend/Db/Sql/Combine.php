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

class Combine extends AbstractSql implements SqlInterface, PreparableSqlInterface
{
    const COMBINE_UNION = 'union';
    const COMBINE_EXCEPT = 'except';
    const COMBINE_INTERSECT = 'intersect';

    protected $combine = array();

    const SPECIFICATION_COMBINE = '%1$s (%2$s) ';

    /**
     * Create combine clause
     *
     * @param Select $select
     * @param string $type
     * @param string $modifier
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
     * @param Select $select
     * @param string $modifier
     * @return self
     */
    public function union(Select $select, $modifier = '')
    {
        return $this->combine($select, self::COMBINE_UNION, $modifier);
    }

    /**
     * Create except clause
     *
     * @param Select $select
     * @param string $modifier
     * @return self
     */
    public function except(Select $select, $modifier = '')
    {
        return $this->combine($select, self::COMBINE_EXCEPT, $modifier);
    }

    /**
     * Create intersect clause
     *
     * @param Select $select
     * @param string $modifier
     * @return self
     */
    public function intersect(Select $select, $modifier = '')
    {
        return $this->combine($select, self::COMBINE_INTERSECT, $modifier);
    }

    /**
     * Get SQL string for statement
     *
     * @param PlatformInterface $adapterPlatform
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
     * @param AdapterInterface $adapter
     * @param StatementContainerInterface $statementContainer
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
     * @param PlatformInterface $platform
     * @param DriverInterface $driver
     * @param ParameterContainer $parameterContainer
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
                self::SPECIFICATION_COMBINE,
                $type,
                $select
            );
        }
        return trim($sql, ' ');
    }

    /**
     * Get raw state
     *
     * @return array
     */
    public function getRawState()
    {
        return $this->combine;
    }
}
