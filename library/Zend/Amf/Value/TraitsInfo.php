<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Value;

/**
 * Zend_Amf_Value_TraitsInfo
 *
 * @package    Zend_Amf
 * @subpackage Value
 */
class TraitsInfo
{
    /**
     * @var string Class name
     */
    protected $_className;

    /**
     * @var bool Whether or not this is a dynamic class
     */
    protected $_dynamic;

    /**
     * @var bool Whether or not the class is externalizable
     */
    protected $_externalizable;

    /**
     * @var array Class properties
     */
    protected $_properties;

    /**
     * Used to keep track of all class traits of an AMF3 object
     *
     * @param  string $className
     * @param  boolean $dynamic
     * @param  boolean $externalizable
     * @param  boolean $properties
     * @return void
     */
    public function __construct($className, $dynamic=false, $externalizable=false, $properties=null)
    {
        $this->_className      = $className;
        $this->_dynamic        = $dynamic;
        $this->_externalizable = $externalizable;
        $this->_properties     = $properties;
    }

    /**
     * Test if the class is a dynamic class
     *
     * @return boolean
     */
    public function isDynamic()
    {
        return $this->_dynamic;
    }

    /**
     * Test if class is externalizable
     *
     * @return boolean
     */
    public function isExternalizable()
    {
        return $this->_externalizable;
    }

    /**
     * Return the number of properties in the class
     *
     * @return int
     */
    public function length()
    {
        return count($this->_properties);
    }

    /**
     * Return the class name
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->_className;
    }

    /**
     * Add an additional property
     *
     * @param  string $name
     * @return \Zend\Amf\Value\TraitsInfo
     */
    public function addProperty($name)
    {
        $this->_properties[] = $name;
        return $this;
    }

    /**
     * Add all properties of the class.
     *
     * @param  array $props
     * @return \Zend\Amf\Value\TraitsInfo
     */
    public function addAllProperties(array $props)
    {
        $this->_properties = $props;
        return $this;
    }

    /**
     * Get the property at a given index
     *
     * @param  int $index
     * @return string
     */
    public function getProperty($index)
    {
        return $this->_properties[(int) $index];
    }

    /**
     * Return all properties of the class.
     *
     * @return array
     */
    public function getAllProperties()
    {
        return $this->_properties;
    }
}
