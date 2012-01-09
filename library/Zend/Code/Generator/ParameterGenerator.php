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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Code\Generator;

use Zend\Code\Reflection\ParameterReflection;

/**
 *
 * @uses       \Zend\Code\Generator\AbstractPhp
 * @uses       Zend_CodeGenerator_Php_ParameterDefaultValue
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ParameterGenerator extends AbstractGenerator
{
    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $type = null;

    /**
     * @var string|ValueGenerator
     */
    protected $defaultValue = null;

    /**
     * @var int
     */
    protected $position = null;

    /**
     * @var bool
     */
    protected $passedByReference = false;

    /**
     * @var array
     */
    protected static $simple = array('int', 'bool', 'string', 'float', 'resource', 'mixed', 'object');

    /**
     * fromReflection()
     *
     * @param ReflectionParameter $reflectionParameter
     * @return ParameterGenerator
     */
    public static function fromReflection(ParameterReflection $reflectionParameter)
    {
        $param = new ParameterGenerator();
        $param->setName($reflectionParameter->getName());

        if ($reflectionParameter->isArray()) {
            $param->setType('array');
        } else {
            $typeClass = $reflectionParameter->getClass();
            if($typeClass !== null) {
                $param->setType($typeClass->getName());
            }
        }

        $param->setPosition($reflectionParameter->getPosition());

        if ($reflectionParameter->isOptional()) {
            $param->setDefaultValue($reflectionParameter->getDefaultValue());
        }
        $param->setPassedByReference($reflectionParameter->isPassedByReference());

        return $param;
    }

    public function __construct($name = null, $type = null, $defaultValue = null, $position = null, $passByReference = false)
    {
        if ($name !== null) {
            $this->setName($name);
        }
        if ($type !== null) {
            $this->setType($type);
        }
        if ($defaultValue !== null) {
            $this->setDefaultValue($defaultValue);
        }
        if ($position !== null) {
            $this->setPosition($position);
        }
        if ($passByReference !== false) {
            $this->setPassedByReference(true);
        }
    }

    /**
     * setType()
     *
     * @param string $type
     * @return \Zend\Code\Generator\PhpParameter\Parameter
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * getType()
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * setName()
     *
     * @param string $name
     * @return \Zend\Code\Generator\PhpParameter\Parameter
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the default value of the parameter.
     *
     * Certain variables are difficult to expres
     *
     * @param null|bool|string|int|float|\Zend\Code\Generator\PhpParameter\DefaultValue $defaultValue
     * @return \Zend\Code\Generator\PhpParameter\Parameter
     */
    public function setDefaultValue($defaultValue)
    {
        if (!$defaultValue instanceof ValueGenerator) {
            $this->defaultValue = new ValueGenerator($defaultValue);
        } else {
            $this->defaultValue = $defaultValue;
        }
        /*
        if ($defaultValue === null) {
            $this->defaultValue = new ValueGenerator();
        } elseif (is_string($defaultValue)) {
            $this->defaultValue = new ValueGenerator($defaultValue);
        } elseif (is_array($defaultValue)) {
            $defaultValue = str_replace(array("\r", "\n"), "", var_export($defaultValue, true));
            $this->defaultValue = new ValueGenerator($defaultValue);
        } elseif (is_bool($defaultValue)) {
            if($defaultValue == true) {
                $this->defaultValue = new ValueGenerator('true');
            } else {
                $this->defaultValue = new ValueGenerator('false');
            }
        } else {
            $this->defaultValue = $defaultValue;
        }
        */
        return $this;
    }

    /**
     * getDefaultValue()
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * setPosition()
     *
     * @param int $position
     * @return \Zend\Code\Generator\PhpParameter\Parameter
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * getPosition()
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function getPassedByReference()
    {
        return $this->passedByReference;
    }

    /**
     * @param bool $passedByReference
     * @return ParameterGenerator
     */
    public function setPassedByReference($passedByReference)
    {
        $this->passedByReference = $passedByReference;
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

        if ($this->type && !in_array($this->type, self::$simple)) {
            $output .= $this->type . ' ';
        }

        if($this->passedByReference === true) {
            $output .= '&';
        }

        $output .= '$' . $this->name;

        if ($this->defaultValue !== null) {
            $output .= ' = ';
            if (is_string($this->defaultValue)) {
                $output .= ValueGenerator::escape($this->defaultValue);
            } else if($this->defaultValue instanceof ValueGenerator) {
                $this->defaultValue->setOutputMode(ValueGenerator::OUTPUT_SINGLE_LINE);
                $output .= (string) $this->defaultValue;
            } else {
                $output .= $this->defaultValue;
            }
        }

        return $output;
    }

}
