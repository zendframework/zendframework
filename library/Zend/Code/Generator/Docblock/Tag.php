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
namespace Zend\Code\Generator\Docblock;

use Zend\Code\Reflection\ReflectionDocblockTag,
    Zend\Code\Generator\AbstractGenerator;

/**
 * @uses       \Zend\CodeGenerator\AbstractCodeGenerator
 * @uses       \Zend\Loader\PluginLoader
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Tag extends AbstractGenerator
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
    protected $name = null;

    /**
     * @var string
     */
    protected $description = null;

    /**
     * fromReflection()
     *
     * @param ReflectionDocblockTag $reflectionTag
     * @return Tag
     */
    public static function fromReflection(ReflectionDocblockTag $reflectionTag)
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
     * @return Tag
     */
    public function setName($name)
    {
        $this->name = ltrim($name, '@');
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
     * setDescription()
     *
     * @param string $description
     * @return Tag
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * getDescription()
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * generate()
     *
     * @return string
     */
    public function generate()
    {
        return '@' . $this->name . ' ' . $this->description;
    }

}
