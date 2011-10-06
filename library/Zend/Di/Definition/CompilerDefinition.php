<?php

namespace Zend\Di\Definition;

use Zend\Code\Scanner\DerivedClassScanner,
    Zend\Code\Scanner\AggregateDirectoryScanner,
    Zend\Code\Scanner\DirectoryScanner,
    Zend\Code\Scanner\FileScanner,
    Zend\Code\Scanner\MethodScanner;

class CompilerDefinition implements Definition
{
    protected $isCompiled = false;

    protected $introspectionStrategy = null;
    
    /**
     * @var AggregateDirectoryScanner
     */
    protected $directoryScanner = null;

    protected $classes = array();

    public function __construct(IntrospectionStrategy $introspectionStrategy = null)
    {
        $this->introspectionStrategy = ($introspectionStrategy) ?: new IntrospectionStrategy();
        $this->directoryScanner = new AggregateDirectoryScanner();
    }

    public function setIntrospectionStrategy(IntrospectionStrategy $introspectionStrategy)
    {
        $this->introspectionStrategy = $introspectionStrategy;
    }
    
    /**
     * 
     * @return IntrospectionStrategy
     */
    public function getIntrospectionStrategy()
    {
        return $this->introspectionStrategy;
    }

    public function addDirectory($directory)
    {
        $this->addDirectoryScanner(new DirectoryScanner($directory));
    }

    public function addDirectoryScanner(DirectoryScanner $directoryScanner)
    {
        $this->directoryScanner->addScanner($directoryScanner);
    }
    
    public function addCodeScannerFile(FileScanner $fileScanner)
    {
        if ($this->directoryScanner == null) {
            $this->directoryScanner = new DirectoryScanner();
        }
        
        $this->directoryScanner->addFileScanner($fileScanner);
    }
    
    public function compile()
    {
        $data = array();
        
        $introspectionStrategy = $this->getIntrospectionStrategy();

        /* @var $classScanner \Zend\Code\Scanner\DerivedClassScanner */
        foreach ($this->directoryScanner->getClasses() as $class) {
            $this->processClass($class);
        }

        //return new ArrayDefinition($data);
    }

    public function processClass($className)
    {
        $strategy = $this->introspectionStrategy;
        $sClass = $this->directoryScanner->getClass($className, true, true);

        if (!$sClass->isInstantiable()) {
            return;
        }

        // determine supertypes
        $superTypes = array();
        if (($parentClasses = $sClass->getParentClasses()) !== null) {
            $superTypes = array_merge($superTypes, $parentClasses);
        }
        if (($interfaces = $sClass->getInterfaces())) {
            $superTypes = array_merge($superTypes, $interfaces);
        }

        $className = $sClass->getName();
        $this->classes[$className] = array(
            'superTypes'       => $superTypes,
            'instantiator'     => null,
            'methods'          => array(),
            'parameters'       => array()
        );

        $def = &$this->classes[$className];

        if ($def['instantiator'] == null) {
            if ($sClass->isInstantiable()) {
                $def['instantiator'] = '__construct';
            }
        }

        if ($sClass->hasMethod('__construct')) {
            $mScanner = $sClass->getMethod('__construct');
            if ($mScanner->isPublic() && $mScanner->getNumberOfParameters() > 0) {
                $def['methods']['__construct'] = true;
                $this->processParams($def, $sClass, $mScanner);
            }
        }

        foreach ($sClass->getMethods(true) as $mScanner) {
            if (!$mScanner->isPublic()) {
                continue;
            }

            $methodName = $mScanner->getName();

            if ($mScanner->getName() === '__construct') {
                continue;
            }

            /*
            if ($strategy->getUseAnnotations() == true) {
                $annotations = $mScanner->getAnnotations($strategy->getAnnotationManager());

                if (($annotations instanceof AnnotationCollection)
                    && $annotations->hasAnnotation('Zend\Di\Definition\Annotation\Inject')) {

                    $def['methods'][$methodName] = true;
                    $this->processParams($def, $sClass, $mScanner);
                    continue;
                }
            }
            */

            $methodPatterns = $this->introspectionStrategy->getMethodNameInclusionPatterns();

            // matches a method injection pattern?
            foreach ($methodPatterns as $methodInjectorPattern) {
                preg_match($methodInjectorPattern, $methodName, $matches);
                if ($matches) {
                    $def['methods'][$methodName] = false; // check ot see if this is required?
                    $this->processParams($def, $sClass, $mScanner);
                    continue 2;
                }
            }

        }

        $interfaceInjectorPatterns = $this->introspectionStrategy->getInterfaceInjectionInclusionPatterns();

        // matches the interface injection pattern
        /** @var $sInterface \Zend\Code\Scanner\ClassScanner */
        foreach ($sClass->getInterfaces(true) as $sInterface) {
            foreach ($interfaceInjectorPatterns as $interfaceInjectorPattern) {
                preg_match($interfaceInjectorPattern, $sInterface->getName(), $matches);
                if ($matches) {
                    foreach ($sInterface->getMethods(true) as $sMethod) {
                        if ($sMethod->getName() === '__construct') { // ctor not allowed in ifaces
                            continue;
                        }
                        $def['methods'][$sMethod->getName()] = true;
                        $this->processParams($def, $sClass, $sMethod);
                    }
                    continue 2;
                }
            }
        }

    }

    protected function processParams(&$def, DerivedClassScanner $sClass, MethodScanner $sMethod)
    {
        if (count($sMethod->getParameters()) === 0) {
            return;
        }

        $methodName = $sMethod->getName();

        $def['parameters'][$methodName] = array();

        foreach ($sMethod->getParameters(true) as $position => $p) {

            /** @var $p \Zend\Code\Scanner\ParameterScanner  */
            $actualParamName = $p->getName();

            $paramName = $this->createDistinctParameterName($actualParamName, $sClass->getName());

            $fqName = $sClass->getName() . '::' . $sMethod->getName() . ':' . $position;

            $def['parameters'][$methodName][$fqName] = array();

            // set the class name, if it exists
            $def['parameters'][$methodName][$fqName][] = $actualParamName;
            $def['parameters'][$methodName][$fqName][] = ($p->getClass() !== null) ? $p->getClass() : null;
            $def['parameters'][$methodName][$fqName][] = !$p->isOptional();
        }
    }

    protected function createDistinctParameterName($paramName, $class)
    {
        $currentParams = array();
        if ($this->classes[$class]['parameters'] === array()) {
            return $paramName;
        }
        foreach ($this->classes as $cdata) {
            foreach ($cdata['parameters'] as $mdata) {
                $currentParams = array_merge($currentParams, array_keys($mdata));
            }
        }

        if (!in_array($paramName, $currentParams)) {
            return $paramName;
        }

        $alt = 2;
        while (in_array($paramName . (string) $alt, $currentParams)) {
            $alt++;
        }

        return $paramName . (string) $alt;
    }

//    public function compileScannerInstantiator(DerivedClassScanner $scannerClass)
//    {
//        if ($scannerClass->hasMethod('__construct')) {
//            $construct = $scannerClass->getMethod('__construct');
//            if ($construct->isPublic()) {
//                return '__construct';
//            }
//        }
//
//        return null;
//    }
//
//    public function compileScannerInjectionMethods(DerivedClassScanner $c)
//    {
//        // return value
//        $methods = array();
//
//        // name of top level class (only, not derived)
//        $className = $c->getName();
//
//
//
//
//        /* @var $m ReflectionMethod */
//        foreach ($c->getMethods(true) as $m) {
//            //$declaringClassName = $m->getDeclaringClass()->getName();
//
//            if (!$m->isPublic() || $m->getNumberOfParameters() == 0) {
//                continue;
//            }
//
//            $methods[$m->getName()] = $this->compileScannerInjectionMethodParmeters($m);
//        }
//
//
////        if ($iRules['enabled']) {
////            foreach ($c->getInterfaces(true) as $i) {
////
////                // explicitly in the include interfaces
////                if ($iRules['includedInterfaces'] && !in_array($i->getName(), $iRules['includedInterfaces'])) {
////                    continue;
////                }
////                // explicity NOT in excluded classes
////                if ($iRules['excludedInterfaces'] && in_array($i->getName(), $iRules['excludedInterfaces'])) {
////                    continue;
////                }
////                // if there is a pattern, and it does not match, continue
////                if ($iRules['pattern'] && !preg_match('#' . preg_quote($iRules['pattern'], '#') . '#', $i->getName())) {
////                    continue;
////                }
////                foreach ($i->getMethods() as $m) {
////                    $methods[$m->getName()] = $this->compileScannerInjectionMethodParmeters(
////                        $m,
////                        IntrospectionStrategy::TYPE_INTERFACE
////                    );
////                }
////            }
////        }
//
//
//        return $methods;
//
//    }
//
//    /**
//     * Return the parameters for a method
//     *
//     * 3 item array:
//     *     #1 - Class name, string if it exists, else null
//     *     #2 - Optional?, boolean
//     *     #3 - Instantiable, boolean if class exists, otherwise null
//     *
//     * @return array
//     */
//    public function compileScannerInjectionMethodParmeters(MethodScanner $methodScanner)
//    {
//        $params = array();
//        $parameterScanners = $methodScanner->getParameters(true);
//
//        /* @var $p Zend\Code\Scanner\ParameterScanner */
//        foreach ($parameterScanners as $p) {
//
//            $paramName = $p->getName();
//
//            // create array for this parameter
//            $params[$paramName] = array();
//
//            // get name, and class if it exists
//            $pcName = $p->getClass();
//            if ($this->directoryScanner->hasClass($pcName)) {
//                $pc = $this->directoryScanner->getClass($pcName);
//            }
//
//            if ($pcName) {
//                // @todo Should we throw an exception if its an unknown type?
//                $params[$paramName][] = $pcName;
//            } else {
//                $params[$paramName][] = null;
//            }
//
//            $params[$paramName][] = $p->isOptional();
//
//            if (isset($pc)) {
//                $params[$paramName][] = ($pc->isInstantiable()) ? true : false;
//            } else {
//                $params[$paramName][] = null;
//            }
//
//        }
//        return $params;
//    }

    /**
     * Return nothing
     *
     * @return array
     */
    public function getClasses()
    {
        return array_keys($this->classes);
    }

    /**
     * Return whether the class exists
     *
     * @param string $class
     * @return bool
     */
    public function hasClass($class)
    {
        return (array_key_exists($class, $this->classes));
    }

    /**
     * Return the supertypes for this class
     *
     * @param string $class
     * @return array of types
     */
    public function getClassSupertypes($class)
    {
        if (!array_key_exists($class, $this->classes[$class])) {
            $this->processClass($class);
        }
        return $this->classes[$class]['supertypes'];
    }

    /**
     * Get the instantiator
     *
     * @param string $class
     * @return string|callable
     */
    public function getInstantiator($class)
    {
        if (!array_key_exists($class, $this->classes)) {
            $this->processClass($class);
        }
        return $this->classes[$class]['instantiator'];
    }

    /**
     * Return if there are injection methods
     *
     * @param string $class
     * @return bool
     */
    public function hasMethods($class)
    {
        if (!array_key_exists($class, $this->classes)) {
            $this->processClass($class);
        }
        return (count($this->classes[$class]['methods']) > 0);
    }

    /**
     * Return injection methods
     *
     * @param string $class
     * @param string $method
     * @return bool
     */
    public function hasMethod($class, $method)
    {
        if (!array_key_exists($class, $this->classes)) {
            $this->processClass($class);
        }
        return isset($this->classes[$class]['methods'][$method]);
    }

    /**
     * Return an array of the injection methods
     *
     * @param string $class
     * @return array
     */
    public function getMethods($class)
    {
        if (!array_key_exists($class, $this->classes)) {
            $this->processClass($class);
        }
        return $this->classes[$class]['methods'];
    }

    public function hasMethodParameters($class, $method)
    {
        if (!isset($this->classes[$class])) {
            return false;
        }
        return (array_key_exists($method, $this->classes[$class]));
    }

    /**
     * Return the parameters for a method
     *
     * 3 item array:
     *     #1 - Class name, string if it exists, else null
     *     #2 - Optional?, boolean
     *     #3 - Instantiable, boolean if class exists, otherwise null
     *
     * @param string $class
     * @param string $method
     * @return array
     */
    public function getMethodParameters($class, $method)
    {
        if (!is_array($this->classes[$class])) {
            $this->processClass($class);
        }
        return $this->classes[$class]['parameters'][$method];
    }
}
