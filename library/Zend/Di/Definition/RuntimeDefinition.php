<?php

namespace Zend\Di\Definition;

use Zend\Di\Definition;

class RuntimeDefinition implements Definition
{
    
    const LOOKUP_TYPE_IMPLICIT = 'implicit';
    const LOOKUP_TYPE_EXPLICIT = 'explicit';
    
    protected $introspectionRuleset = null;
    
    protected $lookupType = self::LOOKUP_TYPE_IMPLICIT;
    
    protected $classes = array();
    protected $injectionMethodCache = array();
    
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
    
    public function getClasses()
    {
        return array();
    }
    
    /**
     * Set the Lookup Type
     * 
     * @param string $lookupType
     */
    public function setLookupType($lookupType)
    {
        $this->lookupType = $lookupType;
    }
    
    /**
     * Track classes when using EXPLICIT lookups
     * @param unknown_type $class
     */
    public function addClass($class)
    {
        $this->classes[] = $class;
    }
    
    public function hasClass($class)
    {
        return class_exists($class, true);
    }
    
    public function getClassSupertypes($class)
    {
        return class_parents($class) + class_implements($class);
    }
    
    public function getInstantiator($class)
    {
        $class = new \ReflectionClass($class);
        if ($class->isInstantiable()) {
            return '__construct';
        }
        return false;
    }
    
    public function hasInjectionMethods($class)
    {
        
    }
    
    public function hasInjectionMethod($class, $method)
    {
        $c = new \ReflectionClass($class);
        return $c->hasMethod($method);
    }
    
    public function getInjectionMethods($class)
    {
        $introspectionRuleset = $this->getIntrospectionRuleset();
var_dump($introspectionRuleset);
        // setup
        $methods = array();
        $c = new \ReflectionClass($class);
        $className = $c->getName();
        
        if (array_key_exists($className, $this->injectionMethodCache)) {
            return $this->injectionMethodCache;
        }
        
        // constructor injection
        $cRules = $introspectionRuleset->getConstructorRules();

        if ($cRules['enabled']) {
            if ($c->hasMethod('__construct') && $c->getMethod('__construct')->getNumberOfParameters() > 0) {
                do {
                    // explicity in included classes
                    if ($cRules['includedClasses'] && !in_array($className, $cRules['includedClasses'])) {
                        break;
                    }
                    // explicity NOT in excluded classes
                    if ($cRules['excludedClasses'] && in_array($className, $cRules['excludedClasses'])) {
                        break;
                    }
                    $methods[] = '__construct';
                } while (false);
            }
        }

        // setter injection
        $sRules = $introspectionRuleset->getSetterRules();
        
        if ($sRules['enabled']) {
            /* @var $m ReflectionMethod */
            foreach ($c->getMethods() as $m) {
                if ($m->getNumberOfParameters() == 0) {
                    continue;
                }
                
                // explicitly in the include classes
                if ($sRules['includedClasses'] && !in_array($className, $sRules['includedClasses'])) {
                    continue;
                }

                // explicity NOT in excluded classes
                if ($sRules['excludedClasses'] && in_array($className, $sRules['excludedClasses'])) {
                    continue;
                }
                // if there is a pattern & it does not match
                if ($sRules['pattern'] && !preg_match('/' . $sRules['pattern'] . '/', $m->getName())) {
                    continue;
                }
                // if there are more than methodsMaxParameters, continue
                if ($sRules['methodMaximumParams'] && ($m->getNumberOfParameters() > $sRules['methodMaximumParams'])) {
                    continue;
                }
                // if param type hint must exist & it does not, continue
                foreach ($m->getParameters() as $p) {
                	/* @var $p ReflectionParameter */
                    if ($sRules['paramTypeMustExist'] && ($p->getClass() == null)) {
                        continue 2;
                    }
                    if (!$sRules['paramCanBeOptional'] && $p->isOptional()) {
                        continue 2;
                    }
                }

                $methods[] = $m->getName();
            }
        }
var_dump($methods);
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
                    $methods[] = $m->getName();
                }
            }
        }

        $this->injectionMethodCache[$className] = $methods;
        return $this->injectionMethodCache[$className];
    }
    
    public function getInjectionMethodParameters($class, $method)
    {
        $params = array();
        
        $injectionMethods = $this->getInjectionMethods($class);

        if (!in_array($method, $injectionMethods[$class])) {
            throw new \Exception('Injectible method was not found.');
        }
        
        $m = new \ReflectionMethod($class, $method);

        foreach ($m->getParameters() as $p) {
            $pc = $p->getClass();
            $params[$p->getName()] = ($pc !== null) ? $pc->getName() : null;
        }
        
        return $params;
    }
    
    
    
}