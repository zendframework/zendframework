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

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\Platform\PlatformInterface,
    Zend\Db\Adapter\Platform\Sql92;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Where extends Predicate\Predicate implements PreparableSqlInterface
{
    protected $specification = ' WHERE %s';

    /**
     * Prepare statement
     *
     * @param \Zend\Db\Adapter\Adapter $adapter
     * @param \Zend\Db\Adapter\Driver\StatementInterface $statement
     * @return void
     */
    public function prepareStatement(Adapter $adapter, StatementInterface $statement)
    {
        $driver = $adapter->getDriver();
        $platform = $adapter->getPlatform();
        $parameterContainer = $statement->getParameterContainer();
        $prepareType = $driver->getPrepareType();

        $parts = parent::getWhereParts();
        $wherePart = '';
        $whereParamIndex = 1;
        foreach ($parts as $part) {
            if (is_string($part)) {
                $wherePart .= $part;
            } elseif (is_array($part)) {
                $values = $part[1];
                $types = (isset($part[2])) ? $part[2] : array();
                foreach ($values as $vIndex => $value) {
                    if (isset($types[$vIndex]) && $types[$vIndex] == self::TYPE_IDENTIFIER) {
                        $values[$vIndex] = $platform->quoteIdentifierInFragment($value);
                    } elseif (isset($types[$vIndex]) && $types[$vIndex] == self::TYPE_VALUE) {
                        if ($prepareType == 'positional') {
                            $parameterContainer->offsetSet(null, $value);
                            $values[$vIndex] = $driver->formatParameterName(null);
                        } elseif ($prepareType == 'named') {
                            $name = 'where' . $whereParamIndex++;
                            $values[$vIndex] = $driver->formatParameterName($name);
                            $parameterContainer->offsetSet($name, $value);
                        }
                    }
                }
                $wherePart .= vsprintf($part[0], $values);
            }
        }

        $sql = $statement->getSql();
        $sql .= sprintf($this->specification, $wherePart);
        $statement->setSql($sql);
    }

    /**
     * Get SQL string for statement
     * 
     * @param  null|PlatformInterface $platform If null, defaults to Sql92
     * @return string
     */
    public function getSqlString(PlatformInterface $platform = null)
    {
        $platform = ($platform) ?: new Sql92;
        $parts = parent::getWhereParts();
        $wherePart = '';
        foreach ($parts as $part) {
            if (is_string($part)) {
                $wherePart .= $part;
            } elseif (is_array($part)) {
                $values = $part[1];
                $types = (isset($part[2])) ? $part[2] : array();
                foreach ($values as $index => $value) {
                    if (isset($types[$index]) && $types[$index] == self::TYPE_IDENTIFIER) {
                        $values[$index] = $platform->quoteIdentifierInFragment($value);
                    } elseif (isset($types[$index]) && $types[$index] == self::TYPE_VALUE) {
                        $values[$index] = $platform->quoteValue($value);
                    }
                }
                $wherePart .= vsprintf($part[0], $values);
            }
        }

        return sprintf($this->specification, $wherePart);
    }
}
