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

use Zend\Code\Reflection\ReflectionClass;

/**
 * @uses       \Zend\Code\Generator\AbstractPhp
 * @uses       \Zend\Code\GeneratorDocblock
 * @uses       \Zend\Code\Generator\Exception
 * @uses       \Zend\Code\Generator\PhpMember\MemberContainer
 * @uses       \Zend\Code\Generator\PhpMethod
 * @uses       \Zend\Code\Generator\PhpProperty
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ClassGenerator extends AbstractGenerator
{

    const FLAG_ABSTRACT  = 0x01;
    const FLAG_FINAL     = 0x02;

    /**
     * @var \FileGenerator\Code\Generator\PhpFile
     */
    protected $containingFileGenerator = null;

    /**
     * @var string
     */
    protected $namespaceName = null;

    /**
     * @var DocblockGenerator
     */
    protected $docblock = null;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var bool
     */
    protected $flags = 0x00;

    /**
     * @var string
     */
    protected $extendedClass = null;

    /**
     * @var array Array of string names
     */
    protected $implementedInterfaces = array();

    /**
     * @var PropertyGenerator[] Array of properties
     */
    protected $properties = array();

    /**
     * @var MethodGenerator[] Array of methods
     */
    protected $methods = array();

    /**
     * fromReflection() - build a Code Generation Php Object from a Class Reflection
     *
     * @param ReflectionClass $reflectionClass
     * @return ClassGenerator
     */
    public static function fromReflection(ReflectionClass $reflectionClass)
    {
        // class generator
        $cg = new static($reflectionClass->getName());

        $cg->setSourceContent($cg->getSourceContent());
        $cg->setSourceDirty(false);

        if ($reflectionClass->getDocComment() != '') {
            $cg->setDocblock(DocblockGenerator::fromReflection($reflectionClass->getDocblock()));
        }

        $cg->setAbstract($reflectionClass->isAbstract());

        // set the namespace
        if ($reflectionClass->inNamespace()) {
            $cg->setNamespaceName($reflectionClass->getNamespaceName());
        }

        /* @var $parentClass \Zend\Code\Reflection\ReflectionClass */
        if ($parentClass = $reflectionClass->getParentClass()) {
            $cg->setExtendedClass($parentClass->getName());
            $interfaces = array_diff($reflectionClass->getInterfaces(), $parentClass->getInterfaces());
        } else {
            $interfaces = $reflectionClass->getInterfaces();
        }

        $interfaceNames = array();
        foreach ($interfaces AS $interface) {
            /* @var $interface \Zend\Code\Reflection\ReflectionClass */
            $interfaceNames[] = $interface->getName();
        }

        $cg->setImplementedInterfaces($interfaceNames);

        $properties = array();
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            /* @var $reflectionProperty \Zend\Code\Reflection\ReflectionProperty */
            if ($reflectionProperty->getDeclaringClass()->getName() == $cg->getName()) {
                $properties[] = PropertyGenerator::fromReflection($reflectionProperty);
            }
        }
        $cg->setProperties($properties);

        $methods = array();
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            /* @var $reflectionMethod \Zend\Code\Reflection\ReflectionMethod */
            if ($reflectionMethod->getDeclaringClass()->getName() == $cg->getName()) {
                $methods[] = MethodGenerator::fromReflection($reflectionMethod);
            }
        }
        $cg->setMethods($methods);

        return $cg;
    }

    public static function fromArray(array $array)
    {
        if (!isset($array['name'])) {
            throw new Exception\InvalidArgumentException('Class generator requires that a name is provided for this object');
        }
        $cg = new static($array['name']);
        foreach ($array as $name => $value) {
            switch ($name) {
                case 'containingFile':
                    $cg->setContainingFileGenerator($value);
                    break;
                case 'namespaceName':
                    $cg->setNamespaceName($value);
                    break;
                case 'docblock':
                    $cg->setDocblock((!$value instanceof DocblockGenerator) ?: DocblockGenerator::fromArray($value));
                    break;
                case 'flag':
                    $cg->setFlags($value);
                    break;
                case 'extendedClass':
                    $cg->setExtendedClass($value);
                    break;
                case 'implementedInterfaces':
                    $cg->setImplementedInterfaces($value);
                    break;
                case 'properties':
                    foreach ($value as $pValue) {
                        $cg->setProperty((!$pValue instanceof PropertyGenerator) ?: PropertyGenerator::fromArray($pValue));
                    }
                    break;
                case 'methods':
                    foreach ($value as $mValue) {
                        $cg->setMethod((!$mValue instanceof MethodGenerator) ?: MethodGenerator::fromArray($mValue));
                    }
                    break;
                    

            }
        }
        return $cg;
    }

    public function __construct($name, $namespaceName = null, $flags = null, $extends = null, $interfaces = array(), $properties = array(), $methods = array(), $docblock = null)
    {
        $this->setName($name);

        if ($namespaceName !== null) {
            $this->setNamespaceName($namespaceName);
        }
        if ($flags !== null) {
            $this->setFlags($flags);
        }
        if ($properties !== array()) {
            $this->setProperties($properties);
        }
        if ($methods !== array()) {
            $this->setMethods($methods);
        }
        if ($docblock !== null) {
            $this->setDocblock($docblock);
        }
    }

    /**
     * setName()
     *
     * @param string $name
     * @return ClassGenerator
     */
    public function setName($name)
    {
        if (strstr($name, '\\')) {
            $namespace = substr($name, 0, strrpos($name, '\\'));
            $name      = substr($name, strrpos($name, '\\') + 1);
            $this->setNamespaceName($namespace);
        }

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
     * getNamespaceName()
     *
     * @return string
     */
    public function getNamespaceName()
    {
        return $this->namespaceName;
    }

    /**
     * setNamespaceName()
     *
     * @param $namespaceName
     * @return Zend\Code\Generator\PhpClass
     */
    public function setNamespaceName($namespaceName)
    {
        $this->namespaceName = $namespaceName;
        return $this;
    }

    /**
     * setPhpFile()
     *
     * @param ClassGenerator $phpFile
     */
    public function setContainingFileGenerator(FileGenerator $fileGenerator)
    {
        $this->containingFileGenerator = $fileGenerator;
        return $this;
    }

    /**
     * getPhpFile()
     *
     * @return Zend\Code\Generator\PhpFile
     */
    public function getContainingFileGenerator()
    {
        return $this->containingFileGenerator;
    }
    
    /**
     * setDocblock() Set the docblock
     *
     * @param \Zend\Code\GeneratorDocblock|array|string $docblock
     * @return \FileGenerator\Code\Generator\PhpFile
     */
    public function setDocblock($docblock)
    {
        if (is_string($docblock)) {
            $docblock = array('shortDescription' => $docblock);
        }

        if (is_array($docblock)) {
            $docblock = new DocblockGenerator($docblock);
        } elseif (!$docblock instanceof DocblockGenerator) {
            throw new Exception\InvalidArgumentException('setDocblock() is expecting either a string, array or an instance of Zend_CodeGenerator_Php_Docblock');
        }

        $this->docblock = $docblock;
        return $this;
    }

    
    /**
     * getDocblock()
     *
     * @return DocblockGenerator
     */
    public function getDocblock()
    {
        return $this->docblock;
    }

    public function setFlags($flags)
    {
        if (is_array($flags)) {
            $flagsArray = $flags;
            $flags = 0x00;
            foreach ($flagsArray as $flag) {
                $flags |= $flag;
            }
        }
        // check that visibility is one of three
        $this->flags = $flags;
        return $this;
    }

    public function addFlag($flag)
    {
        $this->setFlags($this->flags | $flag);
        return $this;
    }

    public function removeFlag($flag)
    {
        $this->setFlags($this->flags & ~$flag);
        return $this;
    }

    /**
     * setAbstract()
     *
     * @param bool $isAbstract
     * @return \AbstractMemberGenerator\Code\Generator\PhpMember\AbstractMember
     */
    public function setAbstract($isAbstract)
    {
        return (($isAbstract) ? $this->addFlag(self::FLAG_ABSTRACT) : $this->removeFlag(self::FLAG_ABSTRACT));
    }

    /**
     * isAbstract()
     *
     * @return bool
     */
    public function isAbstract()
    {
        return ($this->flags & self::FLAG_ABSTRACT);
    }

    /**
     * setFinal()
     *
     * @param bool $isFinal
     * @return \AbstractMemberGenerator\Code\Generator\PhpMember\AbstractMember
     */
    public function setFinal($isFinal)
    {
        return (($isFinal) ? $this->addFlag(self::FLAG_FINAL) : $this->removeFlag(self::FLAG_FINAL));
    }

    /**
     * isFinal()
     *
     * @return bool
     */
    public function isFinal()
    {
        return ($this->flags & self::FLAG_FINAL);
    }

    /**
     * setExtendedClass()
     *
     * @param string $extendedClass
     * @return \ClassGenerator\Code\Generator\PhpClass
     */
    public function setExtendedClass($extendedClass)
    {
        $this->extendedClass = $extendedClass;
        return $this;
    }

    /**
     * getExtendedClass()
     *
     * @return string
     */
    public function getExtendedClass()
    {
        return $this->extendedClass;
    }

    /**
     * setImplementedInterfaces()
     *
     * @param array $implementedInterfaces
     * @return \ClassGenerator\Code\Generator\PhpClass
     */
    public function setImplementedInterfaces(array $implementedInterfaces)
    {
        $this->implementedInterfaces = $implementedInterfaces;
        return $this;
    }

    /**
     * getImplementedInterfaces
     *
     * @return array
     */
    public function getImplementedInterfaces()
    {
        return $this->implementedInterfaces;
    }

    /**
     * setProperties()
     *
     * @param array $properties
     * @return ClassGenerator
     */
    public function setProperties(array $properties)
    {
        foreach ($properties as $property) {
            $this->setProperty($property);
        }

        return $this;
    }

    /**
     * setProperty()
     *
     * @param array|\PropertyGenerator\Code\Generator\PhpProperty $property
     * @return \ClassGenerator\Code\Generator\PhpClass
     */
    public function setProperty(PropertyGenerator $property)
    {
        //if (is_string($property)) {
        //    $property = new PropertyGenerator($property);
        //} elseif (!$property instanceof PropertyGenerator) {
        //    throw new Exception\InvalidArgumentException('setProperty() expects either a string or an instance of Zend\Code\Generator\PropertyGenerator');
        //}
        
        $propertyName = $property->getName();

        if (isset($this->properties[$propertyName])) {
            throw new Exception\InvalidArgumentException('A property by name ' . $propertyName . ' already exists in this class.');
        }

        $this->properties[$propertyName] = $property;
        return $this;
    }

    /**
     * getProperties()
     *
     * @return PropertyGenerator[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * getProperty()
     *
     * @param string $propertyName
     * @return \PropertyGenerator\Code\Generator\PhpProperty
     */
    public function getProperty($propertyName)
    {
        foreach ($this->getProperties() as $property) {
            if ($property->getName() == $propertyName) {
                return $property;
            }
        }
        return false;
    }

    /**
     * hasProperty()
     *
     * @param string $propertyName
     * @return bool
     */
    public function hasProperty($propertyName)
    {
        return isset($this->properties[$propertyName]);
    }

    /**
     * setMethods()
     *
     * @param array $methods
     * @return \ClassGenerator\Code\Generator\PhpClass
     */
    public function setMethods(array $methods)
    {
        foreach ($methods as $method) {
            $this->setMethod($method);
        }
        return $this;
    }

    /**
     * setMethod()
     *
     * @param array|\MethodGenerator\Code\Generator\PhpMethod $method
     * @return \ClassGenerator\Code\Generator\PhpClass
     */
    public function setMethod(MethodGenerator $method)
    {
        //if (is_string($method)) {
        //    $method = new MethodGenerator($method);
        //} elseif (!$method instanceof MethodGenerator) {
        //    throw new Exception\InvalidArgumentException('setMethod() expects either a string method name or an instance of Zend\Code\Generator\MethodGenerator');
        //}
        $methodName = $method->getName();

        if (isset($this->methods[$methodName])) {
            throw new Exception\InvalidArgumentException('A method by name ' . $methodName . ' already exists in this class.');
        }

        $this->methods[$methodName] = $method;
        return $this;
    }

    /**
     * getMethods()
     *
     * @return MethodGenerator[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * getMethod()
     *
     * @param string $methodName
     * @return \MethodGenerator\Code\Generator\PhpMethod
     */
    public function getMethod($methodName)
    {
        foreach ($this->getMethods() as $method) {
            if ($method->getName() == $methodName) {
                return $method;
            }
        }
        return false;
    }

    /**
     * hasMethod()
     *
     * @param string $methodName
     * @return bool
     */
    public function hasMethod($methodName)
    {
        return isset($this->methods[$methodName]);
    }

    /**
     * isSourceDirty()
     *
     * @return bool
     */
    public function isSourceDirty()
    {
        if (($docblock = $this->getDocblock()) && $docblock->isSourceDirty()) {
            return true;
        }

        foreach ($this->getProperties() as $property) {
            if ($property->isSourceDirty()) {
                return true;
            }
        }

        foreach ($this->getMethods() as $method) {
            if ($method->isSourceDirty()) {
                return true;
            }
        }

        return parent::isSourceDirty();
    }

    /**
     * generate()
     *
     * @return string
     */
    public function generate()
    {
        if (!$this->isSourceDirty()) {
            $output = $this->getSourceContent();
            if (!empty($output)) {
                return $output;
            }
        }

        $output = '';

        if (null !== ($namespace = $this->getNamespaceName())) {
            $output .= "/** @namespace */" . self::LINE_FEED;
            $output .= 'namespace ' . $namespace . ';' . self::LINE_FEED . self::LINE_FEED;
        }

        if (null !== ($docblock = $this->getDocblock())) {
            $docblock->setIndentation('');
            $output .= $docblock->generate();
        }

        if ($this->isAbstract()) {
            $output .= 'abstract ';
        }

        $output .= 'class ' . $this->getName();

        if ( !empty( $this->extendedClass) ) {
            $output .= ' extends ' . $this->extendedClass;
        }

        $implemented = $this->getImplementedInterfaces();
        if (!empty($implemented)) {
            $output .= ' implements ' . implode(', ', $implemented);
        }

        $output .= self::LINE_FEED . '{' . self::LINE_FEED . self::LINE_FEED;

        $properties = $this->getProperties();
        if (!empty($properties)) {
            foreach ($properties as $property) {
                $output .= $property->generate() . self::LINE_FEED . self::LINE_FEED;
            }
        }

        $methods = $this->getMethods();
        if (!empty($methods)) {
            foreach ($methods as $method) {
                $output .= $method->generate() . self::LINE_FEED;
            }
        }

        $output .= self::LINE_FEED . '}' . self::LINE_FEED;

        return $output;
    }

}
