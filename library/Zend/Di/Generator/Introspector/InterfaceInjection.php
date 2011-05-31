<?php

namespace Zend\Di\Generator\Introspector;

class InterfaceInjection implements \Zend\Di\Generator\Introspector
{
    protected $managedDefinitions = null;
    protected $typeRegistry = null;
    protected $interfaces = array();
    
    public function setConfiguration(array $configuration)
    {
        $this->interfaces = $configuration;
    }
    
    public function setManagedDefinitions(\Zend\Di\Generator\ManagedDefinitions $managedDefinitions)
    {
        $this->managedDefinitions = $managedDefinitions;
    }
    
    public function setTypeRegistry(\Zend\Di\Generator\TypeRegistry $classRegistry)
    {
        $this->typeRegistry = $classRegistry;
    }
    
    public function introspect()
    {
        $ifaceParams = array();
        
        foreach ($this->interfaces as $interface) {
            if (!interface_exists($interface, false)) {
                throw new \Exception('An unmanaged interface was provided');
            }
            
            $refInterface = new \ReflectionClass($interface);
            
            $ifaceParams[$refInterface->getName()] = array();
            
            foreach ($refInterface->getMethods() as $refMethod) {
                $refParameters = $refMethod->getParameters();

                //$paramMaps = array();
                $params = array();
                foreach ($refParameters as $refParam) {
                    //$paramMaps[$refParam->getName()] = $refParam->getPosition();

                    if ($refTypeClass = $refParam->getClass()) {
                        //echo '      Param type: ' . $refTypeClass->getName() . PHP_EOL;
                        $params[] = new \Zend\Di\Reference($refTypeClass->getName());
                    }
                }
            }
            
            $ifaceParams[$refInterface->getName()][$refMethod->getName()] = $params;

        }
        
        
        
        foreach ($this->typeRegistry as $type) {
            foreach (array_keys($ifaceParams) as $currentInterface) {
                if (in_array($currentInterface, class_implements($type, false))) {
                    //echo 'FOUND INTERFACE INJECTION TYPE ' . $type . ' FOR INTERFACE ' . $currentInterface . PHP_EOL;
                    
                    if ($this->managedDefinitions->hasDefinition($type)) {
                        $definition = $this->managedDefinitions->getDefinition($type);
                    } else {
                        $definition = new \Zend\Di\Generator\DefinitionProxy(new \Zend\Di\Definition($type));
                        $this->managedDefinitions->addDefinition($definition);
                    }
                    
                    foreach ($ifaceParams[$currentInterface] as $methodName => $methodParams) {
                        $definition->addMethodCall($methodName, $methodParams);
                    }

                }
            }
        }
        
    }

}
