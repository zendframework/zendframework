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
class Between implements PredicateInterface
{
    protected $specification = '%1$s BETWEEN %2$s AND %3$s';
    protected $identifier    = null;
    protected $minValue      = null;
    protected $maxValue      = null;
    
    /**
     * Constructor
     * 
     * @param  string $identifier 
     * @param  string $minValue 
     * @param  string $maxValue 
     * @return void
     */
    public function __construct($identifier = null, $minValue = null, $maxValue = null)
    {
        if ($identifier) {
            $this->setIdentifier($identifier);
        }
        if ($minValue) {
            $this->setMinValue($minValue);
        }
        if ($maxValue) {
            $this->setMaxValue($maxValue);
        }
    }

    /**
     * Set identifier for comparison
     * 
     * @param  string $identifier 
     * @return Between
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
     * Set minimum boundary for comparison
     * 
     * @param  int|float|string $minValue 
     * @return Between
     */
    public function setMinValue($minValue)
    {
        $this->minValue = $minValue;
        return $this;
    }

    /**
     * Get minimum boundary for comparison
     * 
     * @return null|int|float|string
     */
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * Set maximum boundary for comparison
     * 
     * @param  int|float|string $maxValue 
     * @return Between
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $maxValue;
        return $this;
    }

    /**
     * Get maximum boundary for comparison
     * 
     * @return null|int|float|string
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * Set specification string to use in forming SQL predicate
     * 
     * @param  string $specification 
     * @return Between
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
     * Return "where" parts
     *
     * @return array
     */
    public function getWhereParts()
    {
        return array(
            array(
                $this->getSpecification(),
                array($this->identifier, $this->minValue, $this->maxValue),
                array(self::TYPE_IDENTIFIER, self::TYPE_VALUE, self::TYPE_VALUE),
            ),
        );
    }
}
