<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Sql\Predicate;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Exception;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 */
class In implements PredicateInterface
{
    protected $identifier;
    protected $valueSet;

    /**
     * Constructor
     *
     * @param  null|string $identifier
     * @param  array $valueSet
     */
    public function __construct($identifier = null, $valueSet = null)
    {
        if ($identifier) {
            $this->setIdentifier($identifier);
        }
        if ($valueSet) {
            $this->setValueSet($valueSet);
        }
    }

    /**
     * Set identifier for comparison
     *
     * @param  string $identifier
     * @return In
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * Get identifier of comparison
     *
     * @return null|string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set set of values for IN comparison
     *
     * @param  array $valueSet
     * @return In
     */
    public function setValueSet($valueSet)
    {
        if (!is_array($valueSet) && !$valueSet instanceof Select) {
            throw new Exception\InvalidArgumentException(
                '$valueSet must be either an array or a Zend\Db\Sql\Select object, ' . gettype($valueSet) . ' given'
            );
        }
        $this->valueSet = $valueSet;
        return $this;
    }

    public function getValueSet()
    {
        return $this->valueSet;
    }

    /**
     * Return array of parts for where statement
     *
     * @return array
     */
    public function getExpressionData()
    {
        $values = $this->getValueSet();
        if ($values instanceof Select) {
            $specification = '%s IN %s';
            $types = array(self::TYPE_SELECT);
            $values = array($values);
        } else {
            $specification = '%s IN (' . implode(', ', array_fill(0, count($values), '%s')) . ')';
            $types = array_fill(0, count($values), self::TYPE_VALUE);
        }

        $identifier = $this->getIdentifier();
        array_unshift($values, $identifier);
        array_unshift($types, self::TYPE_IDENTIFIER);

        return array(array(
            $specification,
            $values,
            $types,
        ));
    }

}
