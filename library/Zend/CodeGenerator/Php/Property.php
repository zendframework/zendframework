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
 * @package    Zend_CodeGenerator
 * @subpackage PHP
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_CodeGenerator_Php_Member_Abstract
 */
require_once 'Zend/CodeGenerator/Php/Member/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_CodeGenerator_Php_Property extends Zend_CodeGenerator_Php_Member_Abstract 
{
    /**
     * @var bool
     */
    protected $_isConst = null;
    
    /**
     * @var string
     */
    protected $_defaultValue = null;

    /**
     * fromReflection()
     *
     * @param Zend_Reflection_Property $reflectionProperty
     * @return Zend_CodeGenerator_Php_Property
     */
    public static function fromReflection(Zend_Reflection_Property $reflectionProperty) {
        $property = new self();
        $property->setSourceDirty(false);
        
        return $property;
    }
    
    /**
     * setConst()
     *
     * @param bool $const
     * @return Zend_CodeGenerator_Php_Property
     */
    public function setConst($const)
    {
        $this->_isConst = $const;
        return $this;
    }
    
    /**
     * isConst()
     *
     * @return bool
     */
    public function isConst()
    {
        return ($this->_isConst) ? true : false;
    }
    
    /**
     * setDefaultValue()
     *
     * @param string $defaultValue
     * @return Zend_CodeGenerator_Php_Property
     */
    public function setDefaultValue($defaultValue)
    {
        $this->_defaultValue = $defaultValue;
        return $this;
    }
    
    /**
     * getDefaultValue()
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->_defaultValue;
    }
    
    /**
     * generate()
     *
     * @return string
     */
    public function generate()
    {
        $name         = $this->getName();
        $defaultValue = $this->getDefaultValue();
        if ($this->isConst()) {
            $string = '    ' . 'const ' . $name . ' = \'' . $defaultValue . '\';';
        } else {
            $string = '    ' . $this->getVisibility() . ' $' . $name . ' = ' . ((null !== $defaultValue) ? '\'' . $defaultValue . '\'' : 'null') . ';';
        }
        return $string; 
    }
    
}
