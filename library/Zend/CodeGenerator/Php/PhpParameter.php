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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\CodeGenerator\Php;

/**
 * @uses       \Zend\CodeGenerator\Php\AbstractPhp
 * @uses       Zend_CodeGenerator_Php_ParameterDefaultValue
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PhpParameter extends \Zend\CodeGenerator\Php\AbstractPhp
{
    /**
     * @var string
     */
    protected $_type = null;

    /**
     * @var string
     */
    protected $_name = null;

    /**
     * @var string
     */
    protected $_defaultValue = null;

    /**
     * @var int
     */
    protected $_position = null;

    /**
     * @var bool
     */
    protected $_passedByReference = false;

    /**
     * fromReflection()
     *
     * @param \Zend\Reflection\ReflectionParameter $reflectionParameter
     * @return \Zend\CodeGenerator\Php\PhpParameter\Parameter
     */
    public static function fromReflection(\Zend\Reflection\ReflectionParameter $reflectionParameter)
    {
        $param = new PhpParameter();
        $param->setName($reflectionParameter->getName());

        if($reflectionParameter->isArray()) {
            $param->setType('array');
        } else {
            $typeClass = $reflectionParameter->getClass();
            if($typeClass !== null) {
                $param->setType($typeClass->getName());
            }
        }

        $param->setPosition($reflectionParameter->getPosition());

        if($reflectionParameter->isOptional()) {
            $param->setDefaultValue($reflectionParameter->getDefaultValue());
        }
        $param->setPassedByReference($reflectionParameter->isPassedByReference());

        return $param;
    }

    /**
     * setType()
     *
     * @param string $type
     * @return \Zend\CodeGenerator\Php\PhpParameter\Parameter
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * getType()
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
     * @param string $name
     * @return \Zend\CodeGenerator\Php\PhpParameter\Parameter
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set the default value of the parameter.
     *
     * Certain variables are difficult to expres
     *
     * @param null|bool|string|int|float|\Zend\CodeGenerator\Php\PhpParameter\DefaultValue $defaultValue
     * @return \Zend\CodeGenerator\Php\PhpParameter\Parameter
     */
    public function setDefaultValue($defaultValue)
    {
        if ($defaultValue === null) {
            $this->_defaultValue = new PhpParameterDefaultValue(array('value' => null));
        } elseif(is_string($defaultValue)) {
            $this->_defaultValue = new PhpParameterDefaultValue(array('value' => $defaultValue));
        } elseif(is_array($defaultValue)) {
            $defaultValue = str_replace(array("\r", "\n"), "", var_export($defaultValue, true));
            $this->_defaultValue = new PhpParameterDefaultValue(array('value' => $defaultValue));
        } elseif(is_bool($defaultValue)) {
            if($defaultValue == true) {
                $this->_defaultValue = new PhpParameterDefaultValue(array('value' => "true"));
            } else {
                $this->_defaultValue = new PhpParameterDefaultValue(array('value' => "false"));
            }
        } else {
            $this->_defaultValue = $defaultValue;
        }
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
     * setPosition()
     *
     * @param int $position
     * @return \Zend\CodeGenerator\Php\PhpParameter\Parameter
     */
    public function setPosition($position)
    {
        $this->_position = $position;
        return $this;
    }

    /**
     * getPosition()
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->_position;
    }

    /**
     * @return bool
     */
    public function getPassedByReference()
    {
        return $this->_passedByReference;
    }

    /**
     * @param bool $passedByReference
     * @return \Zend\CodeGenerator\Php\PhpParameter\Parameter
     */
    public function setPassedByReference($passedByReference)
    {
        $this->_passedByReference = $passedByReference;
        return $this;
    }

    /**
     * generate()
     *
     * @return string
     */
    public function generate()
    {
        $output = '';

        if ($this->_type) {
            $output .= $this->_type . ' ';
        }

        if($this->_passedByReference === true) {
            $output .= '&';
        }

        $output .= '$' . $this->_name;

        if ($this->_defaultValue !== null) {
            $output .= ' = ';
            if (is_string($this->_defaultValue)) {
                $output .= '\'' . $this->_defaultValue . '\'';
            } else if($this->_defaultValue instanceof \Zend\CodeGenerator\Php\PhpParameterDefaultValue) {
                $output .= (string)$this->_defaultValue;
            } else {
                $output .= $this->_defaultValue;
            }
        }

        return $output;
    }

}
