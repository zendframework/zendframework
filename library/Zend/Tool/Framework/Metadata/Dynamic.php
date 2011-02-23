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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework\Metadata;

use Zend\Tool\Framework\Metadata;

/**
 * @uses       \Zend\Tool\Framework\Metadata\Attributable
 * @uses       \Zend\Tool\Framework\Metadata
 * @uses       \Zend\Tool\Framework\Registry\Exception
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dynamic implements Metadata, Attributable
{

    /**
     * @var string
     */
    protected $_type = 'Dynamic';

    /**
     * @var string
     */
    protected $_name = null;

    /**
     * @var string
     */
    protected $_value = null;

    /**
     * @var array
     */
    protected $_dynamicAttributes = array();

    public function __construct($options = array())
    {
        if ($options) {
            $this->setOptions($options);
        }
    }
    
    public function setOptions(Array $options = array())
    {
        foreach ($options as $optName => $optValue) {
            $methodName = 'set' . $optName;
            $this->{$methodName}($optValue);
        }
    }
    
    /**
     * setType()
     * 
     * @param $type
     * @return \Zend\Tool\Framework\Metadata\Dynamic
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }
    
    /**
     * getType()
     *
     * The type of metadata this describes
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * setName()
     * 
     * @param $name
     * @return \Zend\Tool\Framework\Metadata\Dynamic
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }
    
    /**
     * getName()
     *
     * Metadata name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * setValue()
     * 
     * @param $value
     * @return \Zend\Tool\Framework\Metadata\Dynamic
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }
    
    /**
     * getValue()
     *
     * Metadata Value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    public function getAttributes()
    {
        return $this->_dynamicAttributes;
    }

    /**
     * __isset()
     *
     * Check if an attrbute is set
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_dynamicAttributes[$name]);
    }

    /**
     * __unset()
     *
     * @param string $name
     * @return null
     */
    public function __unset($name)
    {
        unset($this->_dynamicAttributes[$name]);
        return;
    }

    /**
     * __get() - Get a property via property call $metadata->foo
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (method_exists($this, 'get' . $name)) {
            return $this->{'get' . $name}();
        } elseif (array_key_exists($name, $this->_dynamicAttributes)) {
            return $this->_dynamicAttributes[$name];
        } else {
            throw new \Zend\Tool\Framework\Exception\InvalidArgumentException('Property ' . $name . ' was not located in this metadata.');
        }
    }

    /**
     * __set() - Set a property via the magic set $metadata->foo = 'foo'
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if (method_exists($this, 'set' . $name)) {
            $this->{'set' . $name}($value);
            return $this;
        } else {
            $this->_dynamicAttributes[$name] = $value;
            return $this;
        }
    }

}
