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

namespace Zend\Db\Sql\Predicate;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class In implements PredicateInterface
{
    protected $specification = '%1$s IN (%2$s)';
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
     * Set specification string to use in forming SQL predicate
     * 
     * @param  string $specification 
     * @return In
     */
    public function setSpecification($specification)
    {
        $this->specification = $specification;
        return $this;
    }

    /**
     * Get specification string to use in forming SQL predicate
     * 
     * @return string
     */
    public function getSpecification()
    {
        return $this->specification;
    }

    /**
     * Return array of parts for where statement
     *
     * @return array
     */
    public function getWhereParts()
    {
        $values = $this->getValueSet();
        $types  = array_fill(0, count($values), self::TYPE_VALUE);

        $identifier = $this->getIdentifier();
        array_unshift($values, $identifier);
        array_unshift($types, self::TYPE_IDENTIFIER);

        return array(array(
            $this->getSpecification(),
            $values,
            $types,
        ));
    }
}
