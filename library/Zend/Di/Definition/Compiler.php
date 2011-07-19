<?php

namespace Zend\Di\Definition;

use Zend\Code\Scanner\AggregateScanner,
    Zend\Code\Scanner\ClassScanner,
    Zend\Code\Scanner\DirectoryScanner,
    Zend\Code\Scanner\FileScanner;

class Compiler
{
    protected $introspectionRuleset = null;
    protected $codeScanners = null;
    protected $codeReflectors = array();
        
    public function setIntrospectionRuleset(IntrospectionRuleset $introspectionRuleset)
    {
        $this->introspectionRuleset = $introspectionRuleset;
    }
    
    /**
     * 
     * @return Zend\Di\Definition\IntrospectionRuleset
     */
    public function getIntrospectionRuleset()
    {
        if ($this->introspectionRuleset == null) {
            $this->introspectionRuleset = new IntrospectionRuleset();
        }
        return $this->introspectionRuleset;
    }
    
    public function addCodeScannerDirectory(DirectoryScanner $directoryScanner)
    {
        if ($this->codeScanners == null) {
            $this->codeScanners = new AggregateScanner();
        }
        
        $this->codeScanners->addScanner($directoryScanner);
    }
    
    public function addCodeScannerFile(FileScanner $fileScanner)
    {
        if ($this->codeScanners == null) {
            $this->codeScanners = new AggregateScanner();
        }
        
        $this->codeScanners->addScanner($fileScanner);
    }
    
    /*
    public function addCodeReflection($reflectionFileOrClass, $followTypes = true)
    {
        //$this->codeScanners[] = array($reflectionFileOrClass, $followTypes);
    }
    */
    
    public function compile()
    {
        $data = array();
        
        $introspectionRuleset = $this->getIntrospectionRuleset();
        
        foreach ($this->codeScanners as $codeScanner) {
            
            $classScanners = $codeScanner->getClasses(true);
            
            /* @var $class Zend\Code\Scanner\ClassScanner */
            foreach ($classScanners as $classScanner) {
                
                if ($classScanner->isAbstract() || $classScanner->isInterface()) {
                    continue;
                }
                
                // build the combined scanner (this + parents - interfaces)
                $combinedScanners = array($classScanner);
                $currentScanner = $classScanner;
                
                while ($currentScanner->hasParentClass()
                    && $this->codeScanners->hasClass($currentScanner->getParentClass())) {
                    $combinedScanners[]
                }
                
                
                // determine supertypes
                $superTypes = array();
                if (($parentClass = $classScanner->getParentClass()) !== null) {
                    $superTypes[] = $parentClass;
                }
                if (($interfaces = $classScanner->getInterfaces())) {
                    $superTypes = array_merge($superTypes, $interfaces);
                }
                
                $className = $classScanner->getName();
                $data[$className] = array(
                    'superTypes'       => $superTypes,
                    'instantiator'     => $this->compileScannerInstantiator($classScanner),
                    'injectionMethods' => $this->compileScannerInjectionMethods($classScanner),
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
