<?php

namespace Zend\Di\Definition;

use Zend\Code\Scanner\DerivedClassScanner,
    Zend\Code\Scanner\DirectoryScanner,
    Zend\Code\Scanner\FileScanner,
    Zend\Code\Scanner\MethodScanner;

class Compiler
{
    protected $introspectionRuleset = null;
    
    /**
     * @var Zend\Code\Scanner\DirectoryScanner
     */
    protected $directoryScanner = null;
    
    //protected $codeReflectors = array();
        
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
        if ($this->directoryScanner == null) {
            $this->directoryScanner = new DirectoryScanner();
        }
        
        $this->directoryScanner->addDirectoryScanner($directoryScanner);
    }
    
    public function addCodeScannerFile(FileScanner $fileScanner)
    {
        if ($this->directoryScanner == null) {
            $this->directoryScanner = new DirectoryScanner();
        }
        
        $this->directoryScanner->addFileScanner($fileScanner);
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
        
        /* @var $classScanner Zend\Code\Scanner\DerivedClassScanner */
        foreach ($this->directoryScanner->getClasses(true, true) as $classScanner) {
            
            // determine supertypes
            $superTypes = array();
            if (($parentClasses = $classScanner->getParentClasses()) !== null) {
                $superTypes = array_merge($superTypes, $parentClasses);
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

        return new ArrayDefinition($data);
    }
    
    public function compileScannerInstantiator(DerivedClassScanner $scannerClass)
    {
        if ($scannerClass->hasMethod('__construct')) {
            $construct = $scannerClass->getMethod('__construct');
            if ($construct->isPublic()) {
                return '__construct';
            }
        }
        
        return null;
    }
    
    public function compileScannerInjectionMethods(DerivedClassScanner $c)
    {
        // return value
        $methods = array();

        // name of top level class (only, not derived)
        $className = $c->getName();
        
        // constructor injection      
        $cRules = $this->getIntrospectionRuleset()->getConstructorRules();
        
        if ($cRules['enabled']) {
            if ($c->hasMethod('__construct')) {
                $constructScanner = $c->getMethod('__construct');
                if ($constructScanner->isPublic() && $constructScanner->getNumberOfParameters() > 0) {
                    do {
                        // explicity in included classes
                        if ($cRules['includedClasses'] && !in_array($className, $cRules['includedClasses'])) {
                            break;
                        }
                        // explicity NOT in excluded classes
                        if ($cRules['excludedClasses'] && in_array($className, $cRules['excludedClasses'])) {
                            break;
                        }
                        gettype($constructScanner);
                        $methods['__construct'] = $this->compileScannerInjectionMethodParmeters(
                            $constructScanner,
                            IntrospectionRuleset::TYPE_CONSTRUCTOR
                        );
                    } while (false);
                }
            }
        }
        
            // setter injection
        $sRules = $this->getIntrospectionRuleset()->getSetterRules();
        
        if ($sRules['enabled']) {
            /* @var $m ReflectionMethod */
            foreach ($c->getMethods(true) as $m) {
                //$declaringClassName = $m->getDeclaringClass()->getName();
                
                if (!$m->isPublic() || $m->getNumberOfParameters() == 0) {
                    continue;
                }
                
                // explicitly in the include classes
                if ($sRules['includedClasses'] && !in_array($className, $sRules['includedClasses'])) {
                    continue;
                }

                // explicity NOT in excluded classes
                if ($sRules['excludedClasses']
                    && (in_array($className, $sRules['excludedClasses'])
                        //|| in_array($declaringClassName, $sRules['excludedClasses'])) 
                    )) {
                    continue;
                }
                
                // declaring class 
                
                // if there is a pattern & it does not match
                if ($sRules['pattern'] && !preg_match('/' . $sRules['pattern'] . '/', $m->getName())) {
                    continue;
                }
                // if there are more than methodsMaxParameters, continue
                if ($sRules['methodMaximumParams'] && ($m->getNumberOfParameters() > $sRules['methodMaximumParams'])) {
                    continue;
                }
                $methods[$m->getName()] = $this->compileScannerInjectionMethodParmeters(
                    $m,
                    IntrospectionRuleset::TYPE_SETTER
                );
            }
        }

        // interface injection
        $iRules = $this->getIntrospectionRuleset()->getInterfaceRules();
        
        if ($iRules['enabled']) {
            foreach ($c->getInterfaces(true) as $i) {

                // explicitly in the include interfaces
                if ($iRules['includedInterfaces'] && !in_array($i->getName(), $iRules['includedInterfaces'])) {
                    continue;
                }
                // explicity NOT in excluded classes
                if ($iRules['excludedInterfaces'] && in_array($i->getName(), $iRules['excludedInterfaces'])) {
                    continue;
                }
                // if there is a pattern, and it does not match, continue
                if ($iRules['pattern'] && !preg_match('#' . preg_quote($iRules['pattern'], '#') . '#', $i->getName())) {
                    continue;
                }
                foreach ($i->getMethods() as $m) {
                    $methods[$m->getName()] = $this->compileScannerInjectionMethodParmeters(
                        $m,
                        IntrospectionRuleset::TYPE_INTERFACE
                    );
                }
            }
        }
        
        
        return $methods;

    }
    
    /**
     * Return the parameters for a method
     * 
     * 3 item array:
     *     #1 - Class name, string if it exists, else null
     *     #2 - Optional?, boolean
     *     #3 - Instantiable, boolean if class exists, otherwise null
     * 
     * @return array 
     */
    public function compileScannerInjectionMethodParmeters(MethodScanner $methodScanner, $introspectionType)
    {
        $params = array();
        $parameterScanners = $methodScanner->getParameters(true);
        
        // rules according to type
        $rules = $this->getIntrospectionRuleset()->getRules($introspectionType);
        
        /* @var $p Zend\Code\Scanner\ParameterScanner */
        foreach ($parameterScanners as $p) {
            
            $paramName = $p->getName();
            
            // create array for this parameter
            $params[$paramName] = array();
            
            // get name, and class if it exists
            $pcName = $p->getClass();
            if ($this->directoryScanner->hasClass($pcName)) {
                $pc = $this->directoryScanner->getClass($pcName);
            }
            
            if ($pcName) {
                // @todo Should we throw an exception if its an unknown type?
                $params[$paramName][] = $pcName;
            } else {
                $params[$paramName][] = null;
            }
            
            if ($introspectionType == IntrospectionRuleset::TYPE_SETTER && $rules['paramCanBeOptional']) {
                $params[$paramName][] = true;
            } else {
                $params[$paramName][] = $p->isOptional(); 
            }
            
            if (isset($pc)) {
                $params[$paramName][] = ($pc->isInstantiable()) ? true : false;
            } else {
                $params[$paramName][] = null;
            }
            
        }
        return $params;
    }

}
