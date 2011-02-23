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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\CodeGenerator\Php;

/**
 * @uses       \Zend\CodeGenerator\Php\Exception
 * @uses       \Zend\CodeGenerator\Php\PhpMember\AbstractMember
 * @uses       \Zend\CodeGenerator\Php\PhpPropertyValue
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PhpProperty extends PhpMember\AbstractMember
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
     * @param \Zend\Reflection\ReflectionProperty $reflectionProperty
     * @return \Zend\CodeGenerator\Php\PhpProperty
     */
    public static function fromReflection(\Zend\Reflection\ReflectionProperty $reflectionProperty)
    {
        $property = new self();

        $property->setName($reflectionProperty->getName());

        $allDefaultProperties = $reflectionProperty->getDeclaringClass()->getDefaultProperties();

        $property->setDefaultValue($allDefaultProperties[$reflectionProperty->getName()]);

        if ($reflectionProperty->getDocComment() != '') {
            $property->setDocblock(Php\PhpDocblock::fromReflection($reflectionProperty->getDocComment()));
        }

        if ($reflectionProperty->isStatic()) {
            $property->setStatic(true);
        }

        if ($reflectionProperty->isPrivate()) {
            $property->setVisibility(self::VISIBILITY_PRIVATE);
        } elseif ($reflectionProperty->isProtected()) {
            $property->setVisibility(self::VISIBILITY_PROTECTED);
        } else {
            $property->setVisibility(self::VISIBILITY_PUBLIC);
        }

        $property->setSourceDirty(false);

        return $property;
    }

    /**
     * setConst()
     *
     * @param bool $const
     * @return \Zend\CodeGenerator\Php\PhpProperty
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
     * @param \Zend\CodeGenerator\Php\PhpPropertyValue|string|array $defaultValue
     * @return \Zend\CodeGenerator\Php\PhpProperty
     */
    public function setDefaultValue($defaultValue)
    {
        // if it looks like
        if (is_array($defaultValue)
            && array_key_exists('value', $defaultValue)
            && array_key_exists('type', $defaultValue)) {
            $defaultValue = new PhpPropertyValue($defaultValue);
        }

        if (!($defaultValue instanceof PhpPropertyValue)) {
            $defaultValue = new PhpPropertyValue(array('value' => $defaultValue));
        }

        $this->_defaultValue = $defaultValue;
        return $this;
    }

    /**
     * getDefaultValue()
     *
     * @return \Zend\CodeGenerator\Php\PhpPropertyValue
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

        $output = '';

        if (($docblock = $this->getDocblock()) !== null) {
            $docblock->setIndentation('    ');
            $output .= $docblock->generate();
        }

        if ($this->isConst()) {
            if ($defaultValue != null && !$defaultValue->isValidConstantType()) {
                throw new Exception\RuntimeException('The property ' . $this->_name . ' is said to be '
                    . 'constant but does not have a valid constant value.');
            }
            $output .= $this->_indentation . 'const ' . $name . ' = '
                . (($defaultValue !== null) ? $defaultValue->generate() : 'null;');
        } else {
            $output .= $this->_indentation
                . $this->getVisibility()
                . (($this->isStatic()) ? ' static' : '')
                . ' $' . $name . ' = '
                . (($defaultValue !== null) ? $defaultValue->generate() : 'null;');
        }
        return $output;
    }

}
