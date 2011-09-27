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
namespace Zend\Code\Generator;

use Zend\Code\Reflection\PropertyReflection;

/**
 * @uses       \Zend\Code\Generator\Exception
 * @uses       \Zend\Code\Generator\PhpMember\AbstractMember
 * @uses       \Zend\Code\Generator\PhpPropertyValue
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PropertyGenerator extends AbstractMemberGenerator
{

    const FLAG_CONSTANT = 0x08;

    /**
     * @var bool
     */
    protected $isConst = null;

    /**
     * @var PropertyValueGenerator
     */
    protected $defaultValue = null;

    /**
     * fromReflection()
     *
     * @param ReflectionProperty $reflectionProperty
     * @return PropertyGenerator
     */
    public static function fromReflection(PropertyReflection $reflectionProperty)
    {
        $property = new self();

        $property->setName($reflectionProperty->getName());

        $allDefaultProperties = $reflectionProperty->getDeclaringClass()->getDefaultProperties();

        $property->setDefaultValue($allDefaultProperties[$reflectionProperty->getName()]);

        if ($reflectionProperty->getDocComment() != '') {
            $property->setDocblock(DocblockGenerator::fromReflection($reflectionProperty->getDocComment()));
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

    public function __construct($name = null, $defaultValue = null, $flags = self::FLAG_PUBLIC)
    {
        if ($name !== null) {
            $this->setName($name);
        }
        if ($defaultValue !== null) {
            $this->setDefaultValue($defaultValue);
        }
        if ($flags !== self::FLAG_PUBLIC) {
            $this->setFlags($flags);
        }
    }

    /**
     * setConst()
     *
     * @param bool $const
     * @return PropertyGenerator
     */
    public function setConst($const)
    {
        if ($const) {
            $this->removeFlag(self::FLAG_PUBLIC | self::FLAG_PRIVATE | self::FLAG_PROTECTED);
        }
    }

    /**
     * isConst()
     *
     * @return bool
     */
    public function isConst()
    {
        return ($this->flags & self::FLAG_CONSTANT);
    }

    /**
     * setDefaultValue()
     *
     * @param \PropertyValueGenerator\Code\Generator\PhpPropertyValue|string|array $defaultValue
     * @return \PropertyGenerator\Code\Generator\PhpProperty
     */
    public function setDefaultValue($defaultValue)
    {
        // if it looks like
        if (is_array($defaultValue)
            && array_key_exists('value', $defaultValue)
            && array_key_exists('type', $defaultValue)) {
            $defaultValue = new PropertyValueGenerator($defaultValue);
        }

        if (!($defaultValue instanceof PropertyValueGenerator)) {
            $defaultValue = new PropertyValueGenerator($defaultValue);
        }

        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * getDefaultValue()
     *
     * @return PropertyValueGenerator
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
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
                throw new Exception\RuntimeException('The property ' . $this->name . ' is said to be '
                    . 'constant but does not have a valid constant value.');
            }
            $output .= $this->indentation . 'const ' . $name . ' = '
                . (($defaultValue !== null) ? $defaultValue->generate() : 'null;');
        } else {
            $output .= $this->indentation
                . $this->getVisibility()
                . (($this->isStatic()) ? ' static' : '')
                . ' $' . $name . ' = '
                . (($defaultValue !== null) ? $defaultValue->generate() : 'null;');
        }
        return $output;
    }

}
