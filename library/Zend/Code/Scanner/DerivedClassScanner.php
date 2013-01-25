<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace Zend\Code\Scanner;

use Zend\Code\Exception;

class DerivedClassScanner extends ClassScanner
{

    /**
     * @var DirectoryScanner
     */
    protected $directoryScanner = null;

    /**
     * @var ClassScanner
     */
    protected $classScanner = null;

    /**
     * @var array
     */
    protected $parentClassScanners = array();

    /**
     * @var array
     */
    protected $interfaceClassScanners = array();

    /**
     * Constructor
     *
     * @param ClassScanner $classScanner
     * @param DirectoryScanner $directoryScanner
     */
    public function __construct(ClassScanner $classScanner, DirectoryScanner $directoryScanner)
    {
        $this->classScanner     = $classScanner;
        $this->directoryScanner = $directoryScanner;

        $currentScannerClass = $classScanner;

        while ($currentScannerClass && $currentScannerClass->hasParentClass()) {
            $currentParentClassName = $currentScannerClass->getParentClass();
            if ($directoryScanner->hasClass($currentParentClassName)) {
                $currentParentClass                                 = $directoryScanner->getClass($currentParentClassName);
                $this->parentClassScanners[$currentParentClassName] = $currentParentClass;
                $currentScannerClass                                = $currentParentClass;
            } else {
                $currentScannerClass = false;
            }
        }

        foreach ($interfaces = $this->classScanner->getInterfaces() as $iName) {
            if ($directoryScanner->hasClass($iName)) {
                $this->interfaceClassScanners[$iName] = $directoryScanner->getClass($iName);
            }
        }
    }

    /**
     * Get name
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->classScanner->getName();
    }

    /**
     * Get short name
     *
     * @return null|string
     */
    public function getShortName()
    {
        return $this->classScanner->getShortName();
    }

    /**
     * Check if instantiable
     *
     * @return bool
     */
    public function isInstantiable()
    {
        return $this->classScanner->isInstantiable();
    }

    /**
     * Check if final
     *
     * @return bool
     */
    public function isFinal()
    {
        return $this->classScanner->isFinal();
    }

    /**
     * Check if is abstract
     *
     * @return bool
     */
    public function isAbstract()
    {
        return $this->classScanner->isAbstract();
    }

    /**
     * Check if is interface
     *
     * @return bool
     */
    public function isInterface()
    {
        return $this->classScanner->isInterface();
    }

    /**
     * Get parent classes
     *
     * @return array
     */
    public function getParentClasses()
    {
        return array_keys($this->parentClassScanners);
    }

    /**
     * Check for parent class
     *
     * @return bool
     */
    public function hasParentClass()
    {
        return ($this->classScanner->getParentClass() != null);
    }

    /**
     * Get parent class
     *
     * @return null|string
     */
    public function getParentClass()
    {
        return $this->classScanner->getParentClass();
    }

    /**
     * Get interfaces
     *
     * @param bool $returnClassScanners
     * @return array
     */
    public function getInterfaces($returnClassScanners = false)
    {
        if ($returnClassScanners) {
            return $this->interfaceClassScanners;
        }

        $interfaces = $this->classScanner->getInterfaces();
        foreach ($this->parentClassScanners as $pClassScanner) {
            $interfaces = array_merge($interfaces, $pClassScanner->getInterfaces());
        }
        return $interfaces;
    }

    /**
     * Get constants
     *
     * @return array
     */
    public function getConstants()
    {
        $constants = $this->classScanner->getConstants();
        foreach ($this->parentClassScanners as $pClassScanner) {
            $constants = array_merge($constants, $pClassScanner->getConstants());
        }
        return $constants;
    }

    /**
     * Get properties
     *
     * @param bool $returnScannerProperty
     * @return array
     */
    public function getProperties($returnScannerProperty = false)
    {
        $properties = $this->classScanner->getProperties($returnScannerProperty);
        foreach ($this->parentClassScanners as $pClassScanner) {
            $properties = array_merge($properties, $pClassScanner->getProperties($returnScannerProperty));
        }
        return $properties;
    }

    /**
     * Get method names
     *
     * @return array
     */
    public function getMethodNames()
    {
        $methods = $this->classScanner->getMethodNames();
        foreach ($this->parentClassScanners as $pClassScanner) {
            $methods = array_merge($methods, $pClassScanner->getMethodNames());
        }
        return $methods;
    }

    /**
     * Get methods
     *
     * @return MethodScanner[]
     */
    public function getMethods()
    {
        $methods = $this->classScanner->getMethods();
        foreach ($this->parentClassScanners as $pClassScanner) {
            $methods = array_merge($methods, $pClassScanner->getMethods());
        }
        return $methods;
    }

    /**
     * Get method
     *
     * @param int|string $methodNameOrInfoIndex
     * @return MethodScanner
     * @throws Exception\InvalidArgumentException
     */
    public function getMethod($methodNameOrInfoIndex)
    {
        if ($this->classScanner->hasMethod($methodNameOrInfoIndex)) {
            return $this->classScanner->getMethod($methodNameOrInfoIndex);
        }
        foreach ($this->parentClassScanners as $pClassScanner) {
            if ($pClassScanner->hasMethod($methodNameOrInfoIndex)) {
                return $pClassScanner->getMethod($methodNameOrInfoIndex);
            }
        }
        throw new Exception\InvalidArgumentException(sprintf(
                                                         'Method %s not found in %s',
                                                         $methodNameOrInfoIndex,
                                                         $this->classScanner->getName()
                                                     ));
    }

    /**
     * Check for method
     *
     * @param string $name
     * @return bool
     */
    public function hasMethod($name)
    {
        if ($this->classScanner->hasMethod($name)) {
            return true;
        }
        foreach ($this->parentClassScanners as $pClassScanner) {
            if ($pClassScanner->hasMethod($name)) {
                return true;
            }
        }
        return false;
    }
}
