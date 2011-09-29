<?php

namespace Zend\Di\Definition;

use Zend\Di\Definition,
    Zend\Code\Annotation\AnnotationManager,
    Zend\Code\Reflection;

class RuntimeDefinition implements Definition
{
    /**
     * @var IntrospectionRuleset
     */
    protected $introspectionRuleset = null;

    /**
     * @var array
     */
    protected $classes = array();

    /**
     * @var array
     */
    protected $injectionMethodCache = array();
    protected $methodAnnotationCache = array();

    /**
     *
     */
    public function __construct(IntrospectionRuleset $introspectionRuleset = null)
    {
        if ($introspectionRuleset === null) {
            $this->introspectionRuleset = $introspectionRuleset;
        }
    }

    /**
     * @param IntrospectionRuleset $introspectionRuleset
     * @return void
     */
    public function setIntrospectionRuleset(IntrospectionRuleset $introspectionRuleset)
    {
        $this->introspectionRuleset = $introspectionRuleset;
    }
    
    /**
     * @return Zend\Di\Definition\IntrospectionRuleset
     */
    public function getIntrospectionRuleset()
    {
        if ($this->introspectionRuleset == null) {
            $this->introspectionRuleset = new IntrospectionRuleset();
        }
        return $this->introspectionRuleset;
    }
    
    /**
     * Return nothing
     * 
     * @return array
     */
    public function getClasses()
    {
        return array();
    }

    /**
     * Track classes when using EXPLICIT lookups
     * @param string $class
     */
    public function addClass($class)
    {
        $this->classes[] = $class;
    }

    /**
     * Return whether the class exists
     *
     * @param string $class
     * @return bool
     */
    public function hasClass($class)
    {
        return class_exists($class, true);
    }

    /**
     * Return the supertypes for this class
     *
     * @param string $class
     * @return array of types
     */
    public function getClassSupertypes($class)
    {
        return class_parents($class) + class_implements($class);
    }

    /**
     * Get the instiatiator
     *
     * @param string $class
     * @return string|callable
     */
    public function getInstantiator($class)
    {
        $class = new Reflection\ClassReflection($class);
        if ($class->isInstantiable()) {
            return '__construct';
        }
        return false;
    }

    /**
     * Return if there are injection methods
     *
     * @param string $class
     * @return bool
     */
    public function hasInjectionMethods($class)
    {
        $methods = $this->getInjectionMethods($class);
        return (count($methods) > 0);
    }

    /**
     * Return injection methods
     *
     * @param string $class
     * @param string $method
     * @return bool
     */
    public function hasInjectionMethod($class, $method)
    {
        $injectionMethods = $this->getInjectionMethods($class);
        return (in_array($method, $injectionMethods));
    }

    /**
     * Return an array of the injection methods
     *
     * @param string $class
     * @return array
     */
    public function getInjectionMethods($class)
    {
        $introspectionRuleset = $this->getIntrospectionRuleset();

        // setup
        $methods = array();
        $c = new Reflection\ClassReflection($class);
        if ($this->introspectionRuleset->useAnnotations()) {
            $c->setAnnotationManager($this->introspectionRuleset->getAnnotationManager());
        }
        $className = $c->getName();
        
        if (array_key_exists($className, $this->injectionMethodCache)) {
            return array_keys($this->injectionMethodCache[$className]);
        }

        /***
         * PROCESS ANNOTATIONS
         */
        if ($this->introspectionRuleset->useAnnotations()) {
            foreach ($c->getMethods() as $method) {
                if (($db = $method->getDocBlock()) !== false) {
                    if ($db->hasAnnotation('inject')) {
                        if (!isset($this->methodAnnotationCache[$c->getName()])) {
                            $this->methodAnnotationCache[$c->getName()] = array();
                        }
                        $this->methodAnnotationCache[$c->getName()][$method->getName()] = $db->getAnnotations();
                        $methods[$method->getName()] = IntrospectionRuleset::TYPE_ANNOTATION;
                    }
                }
            }
        }

        // constructor injection
        $cRules = $introspectionRuleset->getConstructorRules();

        if ($cRules['enabled']) {
            $m = ($c->hasMethod('__construct')) ? $c->getMethod('__construct') : null;
            if ($m && $m->isPublic() && $m->getNumberOfParameters() > 0) {
                do {
                    // explicity in included classes
                    if ($cRules['includedClasses'] && !in_array($className, $cRules['includedClasses'])) {
                        break;
                    }
                    // explicity NOT in excluded classes
                    if ($cRules['excludedClasses'] && in_array($className, $cRules['excludedClasses'])) {
                        break;
                    }
                    $methods['__construct'] = IntrospectionRuleset::TYPE_CONSTRUCTOR;
                } while (false);
            }
        }

        // setter injection
        $sRules = $introspectionRuleset->getSetterRules();
        
        if ($sRules['enabled']) {
            /* @var $m ReflectionMethod */
            foreach ($c->getMethods() as $m) {
                $declaringClassName = $m->getDeclaringClass()->getName();
                
                if (!$m->isPublic() || $m->getNumberOfParameters() == 0) {
                    continue;
                }
                
                // explicitly in the include classes
                if ($sRules['includedClasses'] && !in_array($className, $sRules['includedClasses'])) {
                    continue;
                }

                // explicitly NOT in excluded classes
                if ($sRules['excludedClasses']
                    && (in_array($className, $sRules['excludedClasses'])
                        || in_array($declaringClassName, $sRules['excludedClasses']))) {
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
                $methods[$m->getName()] = IntrospectionRuleset::TYPE_SETTER;
            }
        }

        // interface injection
        $iRules = $introspectionRuleset->getInterfaceRules();
        
        if ($iRules['enabled']) {
            foreach ($c->getInterfaces() as $i) {
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
                    $methods[$m->getName()] = IntrospectionRuleset::TYPE_INTERFACE;
                }
            }
        }

        $this->injectionMethodCache[$className] = $methods;

        return array_keys($this->injectionMethodCache[$className]);
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
    public function getInjectionMethodParameters($class, $method)
    {
        $params = array();

        if (!$this->hasClass($class)) {
            throw new \Exception('Class not found');
        }

        $c = new \ReflectionClass($class);
        $class = $c->getName(); // normalize provided name
        
        $injectionMethods = $this->getInjectionMethods($class);

        if (!in_array($method, $injectionMethods)) {
            throw new \Exception('Injectible method was not found.');
        }
        $m = $c->getMethod($method);
        
        $introspectionType = $this->injectionMethodCache[$class][$m->getName()];
        $rules = $this->getIntrospectionRuleset()->getRules($introspectionType);
        
        foreach ($m->getParameters() as $p) {
            /* @var $p ReflectionParameter */
            $pc = $p->getClass();
            $paramName = $p->getName();
            
            $params[$paramName] = array();
            
            // set the class name, if it exists
            $params[$paramName][] = ($pc !== null) ? $pc->getName() : null;

            // optional?
            if ($introspectionType == IntrospectionRuleset::TYPE_SETTER && $rules['paramCanBeOptional']) {
                $params[$paramName][] = true;
            } else {
                $params[$paramName][] = $p->isOptional(); 
            }
            
            // instantiable?
            if ($pc !== null) {
                $params[$paramName][] = ($pc->isInstantiable()) ? true : false;
            } else {
                $params[$paramName][] = null;
            }
        }
        return $params;
    }
    
}