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
class Literal implements PredicateInterface
{
    protected $literal   = '';
    protected $parameter = null;
    
    /**
     * Constructor
     * 
     * @param  string $literal 
     * @param  mixed $parameter 
     * @return void
     */
    public function __construct($literal = null, $parameter = null)
    {
        if ($literal) {
            $this->setLiteral($literal);
        }
        if ($parameter) {
            $this->setParameter($parameter);
        }
    }
    
    /**
     * Set literal predicate
     * 
     * @param  string $literal 
     * @return Literal
     */
    public function setLiteral($literal)
    {
        $this->literal = $literal;
        return $this;
    }

    /**
     * Get literal predicate
     * 
     * @return string
     */
    public function getLiteral()
    {
        return $this->literal;
    }

    /**
     * Set one or more parameters for the predicate
     * 
     * @param  mixed $parameter 
     * @return Literal
     */
    public function setParameter($parameter)
    {
        if (!is_array($parameter)) {
            $parameter = array($parameter);
        }
        $this->parameter = $parameter;
        return $this;
    }

    /**
     * Get parameters
     * 
     * @return null|array
     */
    public function getParameter()
    {
        return $this->parameter;
    }


    /**
     * Get where statement parts
     * 
     * @return array
     */
    public function getWhereParts()
    {
        $spec = $this->literal;
        if (empty($this->parameter)) {
            return array($spec);
        }

        $types = array_fill(0, count($this->parameter), self::TYPE_VALUE);
        $spec  = str_replace('?', '%s', $spec);
        return array(
            array($spec, $this->parameter, $types)
        );
    }
}
