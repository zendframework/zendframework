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
 * @uses       \Zend\CodeGenerator\Php\AbstractPhp
 * @uses       \Zend\CodeGenerator\PhpDocblock
 * @uses       \Zend\CodeGenerator\Php\Exception
 * @uses       \Zend\CodeGenerator\Php\PhpMember\MemberContainer
 * @uses       \Zend\CodeGenerator\Php\PhpMethod
 * @uses       \Zend\CodeGenerator\Php\PhpProperty
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PhpClass extends AbstractPhp
{

    /**
     * @var \Zend\CodeGenerator\Php\PhpFile
     */
    protected $_phpFile = null;

    /**
     * @var string
     */
    protected $_namespaceName = null;
    
    /**
     * @var \Zend\CodeGenerator\Php\PhpDocblock
     */
    protected $_docblock = null;

    /**
     * @var string
     */
    protected $_name = null;

    /**
     * @var bool
     */
    protected $_isAbstract = false;

    /**
     * @var string
     */
    protected $_extendedClass = null;

    /**
     * @var array Array of string names
     */
    protected $_implementedInterfaces = array();

    /**
     * @var array Array of properties
     */
    protected $_properties = null;

    /**
     * @var array Array of methods
     */
    protected $_methods = null;

    /**
     * fromReflection() - build a Code Generation Php Object from a Class Reflection
     *
     * @param \Zend\Reflection\ReflectionClass $reflectionClass
     * @return \Zend\CodeGenerator\Php\PhpClass
     */
    public static function fromReflection(\Zend\Reflection\ReflectionClass $reflectionClass)
    {
        $class = new self();

        $class->setSourceContent($class->getSourceContent());
        $class->setSourceDirty(false);

        if ($reflectionClass->getDocComment() != '') {
            $class->setDocblock(PhpDocblock::fromReflection($reflectionClass->getDocblock()));
        }

        $class->setAbstract($reflectionClass->isAbstract());
        
        // set the namespace
        if ($reflectionClass->inNamespace()) {
            $class->setNamespaceName($reflectionClass->getNamespaceName());
        }

        $class->setName($reflectionClass->getName());
        
        if ($parentClass = $reflectionClass->getParentClass()) {
            $class->setExtendedClass($parentClass->getName());
            $interfaces = array_diff($reflectionClass->getInterfaces(), $parentClass->getInterfaces());
        } else {
            $interfaces = $reflectionClass->getInterfaces();
        }

        $interfaceNames = array();
        foreach($interfaces AS $interface) {
            $interfaceNames[] = $interface->getName();
        }

        $class->setImplementedInterfaces($interfaceNames);

        $properties = array();
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->getDeclaringClass()->getName() == $class->getName()) {
                $properties[] = PhpProperty::fromReflection($reflectionProperty);
            }
        }
        $class->setProperties($properties);

        $methods = array();
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->getDeclaringClass()->getName() == $class->getName()) {
                $methods[] = PhpMethod::fromReflection($reflectionMethod);
            }
        }
        $class->setMethods($methods);

        return $class;
    }

    /**
     * setPhpFile()
     * 
     * @param Zend\CodeGenerator\Php\PhpFile $phpFile
     */
    public function setPhpFile(PhpFile $phpFile)
    {
        $this->_phpFile = $phpFile;
        return $this;
    }
    
    /**
     * getPhpFile()
     * 
     * @return Zend\CodeGenerator\Php\PhpFile
     */
    public function getPhpFile()
    {
        return $this->_phpFile;
    }
    
    /**
     * setDocblock() Set the docblock
     *
     * @param \Zend\CodeGenerator\PhpDocblock|array|string $docblock
     * @return \Zend\CodeGenerator\Php\PhpFile
     */
    public function setDocblock($docblock)
    {
        if (is_string($docblock)) {
            $docblock = array('shortDescription' => $docblock);
        }

        if (is_array($docblock)) {
            $docblock = new PhpDocblock($docblock);
        } elseif (!$docblock instanceof PhpDocblock) {
            throw new Exception\InvalidArgumentException('setDocblock() is expecting either a string, array or an instance of Zend_CodeGenerator_Php_Docblock');
        }

        $this->_docblock = $docblock;
        return $this;
    }

    /**
     * getNamespaceName()
     * 
     * @return string
     */
    public function getNamespaceName()
    {
        return $this->_namespaceName;
    }
    
    /**
     * setNamespaceName()
     * 
     * @param $namespaceName
     * @return Zend\CodeGenerator\Php\PhpClass
     */
    public function setNamespaceName($namespaceName)
    {
        $this->_namespaceName = $namespaceName;
        return $this;
    }
    
    /**
     * getDocblock()
     *
     * @return \Zend\CodeGenerator\PhpDocblock
     */
    public function getDocblock()
    {
        return $this->_docblock;
    }

    /**
     * setName()
     *
     * @param string $name
     * @return \Zend\CodeGenerator\Php\PhpClass
     */
    public function setName($name)
    {
        if (strstr($name, '\\')) {
            $namespace = substr($name, 0, strrpos($name, '\\'));
            $name      = substr($name, strrpos($name, '\\') + 1);
            $this->setNamespaceName($namespace);
        }

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
     * setAbstract()
     *
     * @param bool $isAbstract
     * @return \Zend\CodeGenerator\Php\PhpClass
     */
    public function setAbstract($isAbstract)
    {
        $this->_isAbstract = ($isAbstract) ? true : false;
        return $this;
    }

    /**
     * isAbstract()
     *
     * @return bool
     */
    public function isAbstract()
    {
        return $this->_isAbstract;
    }

    /**
     * setExtendedClass()
     *
     * @param string $extendedClass
     * @return \Zend\CodeGenerator\Php\PhpClass
     */
    public function setExtendedClass($extendedClass)
    {
        $this->_extendedClass = $extendedClass;
        return $this;
    }

    /**
     * getExtendedClass()
     *
     * @return string
     */
    public function getExtendedClass()
    {
        return $this->_extendedClass;
    }

    /**
     * setImplementedInterfaces()
     *
     * @param array $implementedInterfaces
     * @return \Zend\CodeGenerator\Php\PhpClass
     */
    public function setImplementedInterfaces(Array $implementedInterfaces)
    {
        $this->_implementedInterfaces = $implementedInterfaces;
        return $this;
    }

    /**
     * getImplementedInterfaces
     *
     * @return array
     */
    public function getImplementedInterfaces()
    {
        return $this->_implementedInterfaces;
    }

    /**
     * setProperties()
     *
     * @param array $properties
     * @return \Zend\CodeGenerator\Php\PhpClass
     */
    public function setProperties(Array $properties)
    {
        foreach ($properties as $property) {
            $this->setProperty($property);
        }

        return $this;
    }

    /**
     * setProperty()
     *
     * @param array|\Zend\CodeGenerator\Php\PhpProperty $property
     * @return \Zend\CodeGenerator\Php\PhpClass
     */
    public function setProperty($property)
    {
        if (is_array($property)) {
            $property = new PhpProperty($property);
            $propertyName = $property->getName();
        } elseif ($property instanceof PhpProperty) {
            $propertyName = $property->getName();
        } else {
            throw new Exception\InvalidArgumentException('setProperty() expects either an array of property options or an instance of Zend_CodeGenerator_Php_Property');
        }

        if (isset($this->_properties[$propertyName])) {
            throw new Exception\InvalidArgumentException('A property by name ' . $propertyName . ' already exists in this class.');
        }

        $this->_properties[$propertyName] = $property;
        return $this;
    }

    /**
     * getProperties()
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * getProperty()
     *
     * @param string $propertyName
     * @return \Zend\CodeGenerator\Php\PhpProperty
     */
    public function getProperty($propertyName)
    {
        foreach ($this->_properties as $property) {
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
        return isset($this->_properties[$propertyName]);
    }

    /**
     * setMethods()
     *
     * @param array $methods
     * @return \Zend\CodeGenerator\Php\PhpClass
     */
    public function setMethods(Array $methods)
    {
        foreach ($methods as $method) {
            $this->setMethod($method);
        }
        return $this;
    }

    /**
     * setMethod()
     *
     * @param array|\Zend\CodeGenerator\Php\PhpMethod $method
     * @return \Zend\CodeGenerator\Php\PhpClass
     */
    public function setMethod($method)
    {
        if (is_array($method)) {
            $method = new PhpMethod($method);
            $methodName = $method->getName();
        } elseif ($method instanceof PhpMethod) {
            $methodName = $method->getName();
        } else {
            throw new Exception\InvalidArgumentException('setMethod() expects either an array of method options or an instance of Zend\CodeGenerator\Php\Method');
        }

        if (isset($this->_methods[$methodName])) {
            throw new Exception\InvalidArgumentException('A method by name ' . $methodName . ' already exists in this class.');
        }

        $this->_methods[$methodName] = $method;
        return $this;
    }

    /**
     * getMethods()
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->_methods;
    }

    /**
     * getMethod()
     *
     * @param string $methodName
     * @return \Zend\CodeGenerator\Php\PhpMethod
     */
    public function getMethod($methodName)
    {
        foreach ($this->_methods as $method) {
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
        return isset($this->_methods[$methodName]);
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

        foreach ($this->_properties as $property) {
            if ($property->isSourceDirty()) {
                return true;
            }
        }

        foreach ($this->_methods as $method) {
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

        if ( !empty( $this->_extendedClass) ) {
            $output .= ' extends ' . $this->_extendedClass;
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

    /**
     * _init() - is called at construction time
     *
     */
    protected function _init()
    {
        $this->_properties = new PhpMember\MemberContainer(PhpMember\MemberContainer::TYPE_PROPERTY);
        $this->_methods = new PhpMember\MemberContainer(PhpMember\MemberContainer::TYPE_METHOD);
    }

}
