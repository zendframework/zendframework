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
 * @uses       \Zend\CodeGenerator\AbstractCodeGenerator
 * @uses       \Zend\CodeGenerator\Php\Docblock\Tag\Param
 * @uses       \Zend\CodeGenerator\Php\Docblock\Tag\Return
 * @uses       \Zend\Loader\PluginLoader
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PhpDocblockTag extends AbstractPhp
{

    protected static $_typeFormats = array(
        array(
            'param',
            '@param <type> <variable> <description>'
            ),
        array(
            'return',
            '@return <type> <description>'
            ),
        array(
            'tag',
            '@<name> <description>'
            )
        );
    
    /**
     * @var string
     */
    protected $_name = null;

    /**
     * fromReflection()
     *
     * @param \Zend\Reflection\ReflectionDocblockTag $reflectionTag
     * @return \Zend\CodeGenerator\Php\PhpDocblockTag
     */
    public static function fromReflection(\Zend\Reflection\ReflectionDocblockTag $reflectionTag)
    {
        $tagName = $reflectionTag->getName();

        $codeGenDocblockTag = new self();
        $codeGenDocblockTag->setName($tagName);

        // transport any properties via accessors and mutators from reflection to codegen object
        $reflectionClass = new \ReflectionClass($reflectionTag);
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (substr($method->getName(), 0, 3) == 'get') {
                $propertyName = substr($method->getName(), 3);
                if (method_exists($codeGenDocblockTag, 'set' . $propertyName)) {
                    $codeGenDocblockTag->{'set' . $propertyName}($reflectionTag->{'get' . $propertyName}());
                }
            }
        }

        return $codeGenDocblockTag;
    }

    /**
     * setName()
     *
     * @param string $name
     * @return \Zend\CodeGenerator\Php\PhpDocblockTag
     */
    public function setName($name)
    {
        $this->_name = ltrim($name, '@');
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
     * setDescription()
     *
     * @param string $description
     * @return \Zend\CodeGenerator\Php\PhpDocblockTag
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * getDescription()
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * generate()
     *
     * @return string
     */
    public function generate()
    {
        return '@' . $this->_name . ' ' . $this->_description;
    }

}
