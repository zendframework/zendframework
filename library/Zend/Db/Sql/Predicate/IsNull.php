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
class IsNull implements PredicateInterface
{
    protected $specification = '%1$s IS NULL';
    protected $identifier;

    /**
     * Constructor
     * 
     * @param  string $identifier 
     * @return void
     */
    public function __construct($identifier = null)
    {
        if ($identifier) {
            $this->setIdentifier($identifier);
        }
    }
    
    /**
     * Set identifier for comparison
     * 
     * @param  string $identifier 
     * @return IsNull
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
     * Set specification string to use in forming SQL predicate
     * 
     * @param  string $specification 
     * @return IsNull
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
     * Get parts for where statement
     *
     * @return array
     */
    public function getWhereParts()
    {
        return array(array(
            $this->getSpecification(),
            array($this->identifier),
            array(self::TYPE_IDENTIFIER),
        ));
    }
}
