<?php

namespace Zend\Di\Definition;

use Zend\Code\Scanner\ClassScanner,
    Zend\Code\Scanner\DirectoryScanner,
    Zend\Code\Scanner\FileScanner;

class Compiler
{
    protected $codeScanners = array();
    protected $codeReflectors = array();
    
    public function addCodeScannerDirectory(DirectoryScanner $scannerDirectory)
    {
        $this->codeScanners[] = $scannerDirectory;
    }
    
    public function addCodeScannerFile(FileScanner $scannerFile)
    {
        $this->codeScanners[] = $scannerFile;
    }
    
    public function addCodeReflection($reflectionFileOrClass, $followTypes = true)
    {
        //$this->codeScanners[] = array($reflectionFileOrClass, $followTypes);
    }
    
    public function compile()
    {
        $data = array();
        
        foreach ($this->codeScanners as $codeScanner) {
            
            $scannerClasses = $codeScanner->getClasses(true);
            
            /* @var $class Zend\Code\Scanner\ClassScanner */
            foreach ($scannerClasses as $scannerClass) {
                
                if ($scannerClass->isAbstract() || $scannerClass->isInterface()) {
                    continue;
                }
                
                // determine supertypes
                $superTypes = array();
                if (($parentClass = $scannerClass->getParentClass()) !== null) {
                    $superTypes[] = $parentClass;
                }
                if (($interfaces = $scannerClass->getInterfaces())) {
                    $superTypes = array_merge($superTypes, $interfaces);
                }
                
                $className = $scannerClass->getName();
                $data[$className] = array(
                    'superTypes'       => $superTypes,
                    'instantiator'     => $this->compileScannerInstantiator($scannerClass),
                    'injectionMethods' => $this->compileScannerInjectionMethods($scannerClass),
                );
            }
        }
        
        return new ArrayDefinition($data);
    }
    
    public function compileScannerInstantiator(ClassScanner $scannerClass)
    {
        if ($scannerClass->hasMethod('__construct')) {
            $construct = $scannerClass->getMethod('__construct');
            if ($construct->isPublic()) {
                return '__construct';
            }
        }
        
        return null;
        
        // @todo scan parent classes for instantiator
    }
    
    public function compileScannerInjectionMethods(ClassScanner $scannerClass)
    {
        $data      = array();
        $className = $scannerClass->getName();
        foreach ($scannerClass->getMethods(true) as $scannerMethod) {
            $methodName = $scannerMethod->getName();
            
            // determine initiator & constructor dependencies
            if ($methodName === '__construct' && $scannerMethod->isPublic()) {
                $params = $scannerMethod->getParameters(true);
                if ($params) {
                    $data[$methodName] = array();
                    foreach ($params as $param) {
                        $data[$methodName][$param->getName()] = $param->getClass();
                    }
                }
            }
            
            // scan for setter injection
            if (preg_match('#^set[A-Z]#', $methodName)) {
                $data[$methodName] = $scannerMethod->getParameters();
                $params = $scannerMethod->getParameters(true);
                $data[$methodName] = array();
                foreach ($params as $param) {
                    $data[$methodName][$param->getName()] = $param->getClass();
                }
            }
        }
        return $data;
    }
    
    
    /*
    public function hasClass($class);
    public function getClassSupertypes($class);
    public function getInstantiator($class);
    public function hasInjectionMethods($class);
    public function hasInjectionMethod($class, $method);
    public function getInjectionMethods($class);
    public function getInjectionMethodParameters($class, $method);    
     */
}
