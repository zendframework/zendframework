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
namespace Zend\CodeGenerator\PHP;

/**
 * @uses       \Zend\CodeGenerator\PHP\AbstractPHP
 * @uses       \Zend\CodeGenerator\PHPDocblock
 * @uses       \Zend\CodeGenerator\PHP\Exception
 * @uses       \Zend\CodeGenerator\PHP\PHPMember\MemberContainer
 * @uses       \Zend\CodeGenerator\PHP\PHPMethod
 * @uses       \Zend\CodeGenerator\PHP\PHPProperty
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PHPClass extends AbstractPHP
{

    /**
     * @var \Zend\CodeGenerator\PHP\PHPFile
     */
    protected $_phpFile = null;

    /**
     * @var string
     */
    protected $_namespaceName = null;
    
    /**
     * @var \Zend\CodeGenerator\PHP\PHPDocblock
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
     * fromReflection() - build a Code Generation PHP Object from a Class Reflection
     *
     * @param \Zend\Reflection\ReflectionClass $reflectionClass
     * @return \Zend\CodeGenerator\PHP\PHPClass
     */
    public static function fromReflection(\Zend\Reflection\ReflectionClass $reflectionClass)
    {
        $class = new self();

        $class->setSourceContent($class->getSourceContent());
        $class->setSourceDirty(false);

        if ($reflectionClass->getDocComment() != '') {
            $class->setDocblock(PHPDocblock::fromReflection($reflectionClass->getDocblock()));
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
                $properties[] = PHPProperty::fromReflection($reflectionProperty);
            }
        }
        $class->setProperties($properties);

        $methods = array();
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->getDeclaringClass()->getName() == $class->getName()) {
                $methods[] = PHPMethod::fromReflection($reflectionMethod);
            }
        }
        $class->setMethods($methods);

        return $class;
    }

    /**
     * setPHPFile()
     * 
     * @param Zend\CodeGenerator\PHP\PHPFile $phpFile
     */
    public function setPHPFile(PHPFile $phpFile)
    {
        $this->_phpFile = $phpFile;
        return $this;
    }
    
    /**
     * getPHPFile()
     * 
     * @return Zend\CodeGenerator\PHP\PHPFile
     */
    public function getPHPFile()
    {
        return $this->_phpFile;
    }
    
    /**
     * setDocblock() Set the docblock
     *
     * @param \Zend\CodeGenerator\PHPDocblock|array|string $docblock
     * @return \Zend\CodeGenerator\PHP\PHPFile
     */
    public function setDocblock($docblock)
    {
        if (is_string($docblock)) {
            $docblock = array('shortDescription' => $docblock);
        }

        if (is_array($docblock)) {
            $docblock = new PHPDocblock($docblock);
        } elseif (!$docblock instanceof PHPDocblock) {
            throw new Exception('setDocblock() is expecting either a string, array or an instance of Zend_CodeGenerator_Php_Docblock');
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
     * @return Zend\CodeGenerator\PHP\PHPClass
     */
    public function setNamespaceName($namespaceName)
    {
        $this->_namespaceName = $namespaceName;
        return $this;
    }
    
    /**
     * getDocblock()
     *
     * @return \Zend\CodeGenerator\PHPDocblock
     */
    public function getDocblock()
    {
        return $this->_docblock;
    }

    /**
     * setName()
     *
     * @param string $name
     * @return \Zend\CodeGenerator\PHP\PHPClass
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
     * setAbstract()
     *
     * @param bool $isAbstract
     * @return \Zend\CodeGenerator\PHP\PHPClass
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
     * @return \Zend\CodeGenerator\PHP\PHPClass
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
     * @return \Zend\CodeGenerator\PHP\PHPClass
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
     * @return \Zend\CodeGenerator\PHP\PHPClass
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
     * @param array|\Zend\CodeGenerator\PHP\PHPProperty $property
     * @return \Zend\CodeGenerator\PHP\PHPClass
     */
    public function setProperty($property)
    {
        if (is_array($property)) {
            $property = new PHPProperty($property);
            $propertyName = $property->getName();
        } elseif ($property instanceof PHPProperty) {
            $propertyName = $property->getName();
        } else {
            throw new Exception('setProperty() expects either an array of property options or an instance of Zend_CodeGenerator_Php_Property');
        }

        if (isset($this->_properties[$propertyName])) {
            throw new Exception('A property by name ' . $propertyName . ' already exists in this class.');
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
     * @return \Zend\CodeGenerator\PHP\PHPProperty
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
     * @return \Zend\CodeGenerator\PHP\PHPClass
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
     * @param array|\Zend\CodeGenerator\PHP\PHPMethod $method
     * @return \Zend\CodeGenerator\PHP\PHPClass
     */
    public function setMethod($method)
    {
        if (is_array($method)) {
            $method = new PHPMethod($method);
            $methodName = $method->getName();
        } elseif ($method instanceof PHPMethod) {
            $methodName = $method->getName();
        } else {
            throw new Exception('setMethod() expects either an array of method options or an instance of Zend_CodeGenerator_Php_Method');
        }

        if (isset($this->_methods[$methodName])) {
            throw new Exception('A method by name ' . $methodName . ' already exists in this class.');
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
     * @return \Zend\CodeGenerator\PHP\PHPMethod
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
            return $this->getSourceContent();
        }

        $output = '';

        if (null !== ($docblock = $this->getDocblock())) {
            $docblock->setIndentation('');
            $output .= $docblock->generate();
        }

        if ($this->isAbstract()) {
            $output .= 'abstract ';
        }

        $name = $this->getName();
        
        if ($this->_namespaceName && strpos($name, $this->_namespaceName) === 0) {
            $output .= 'class ' . substr($name, strlen($this->_namespaceName)+1);
        } else {
            $output .= 'class ' . $name;
        }
        
        if (null !== $this->_extendedClass) {
            if ($this->_namespaceName && strpos($this->_extendedClass, $this->_namespaceName) === 0) {
                $output .= ' extends ' . substr($this->_extendedClass, strlen($this->_namespaceName)+1);
            } else {
                $output .= ' extends ' . $this->_extendedClass;
            }
        }

        $implementedInterfaces = $this->getImplementedInterfaces();
        if (!empty($implementedInterfaces)) {
            $output .= ' implements ';
            foreach ($implementedInterfaces as $iiIndex => $ii) {
                if (strpos($ii, $this->_namespaceName) === 0) {
                    $implementedInterfaces[$iiIndex] = substr($ii, strlen($this->_namespaceName)+1);
                }
            }
            $output .= implode(', ', $implementedInterfaces);
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
        $this->_properties = new PHPMember\MemberContainer(PHPMember\MemberContainer::TYPE_PROPERTY);
        $this->_methods = new PHPMember\MemberContainer(PHPMember\MemberContainer::TYPE_METHOD);
    }

}
