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
     * @return void
     */
    public function __construct($identifier = null, array $valueSet = array())
    {
        if ($identifier) {
            $this->setIdentifier($identifier);
        }
        if (!empty($valueSet)) {
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
    public function setValueSet(array $valueSet)
    {
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
        $specification = '%s IN (' . implode(', ', array_fill(0, count($values), '%s')) . ')';
        $types  = array_fill(0, count($values), self::TYPE_VALUE);

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
