<?php

namespace Zend\Di\Generator\Introspector;

class SetterInjection implements \Zend\Di\Generator\Introspector
{
    protected $configuration = null;
    
    /**
     * @var Zend\Di\Generator\ManagedDefinitions
     */
    protected $managedDefinitions = null;
    protected $typeRegistry = null;
    
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
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
        foreach ($this->typeRegistry as $type) {

            try {
                $refClass = new \ReflectionClass($type);
                echo 'Reflecting ' . $type . PHP_EOL;
                
                foreach ($refClass->getMethods() as $refMethod) {
                    if (preg_match('#^set.*#', $refMethod->getName())) {
                        echo 'Found injectable method: ' . $refMethod->getName() . PHP_EOL;
                        
                        if ($this->managedDefinitions->hasDefinition($type)) {
                            $definition = $this->managedDefinitions->getDefinition($type);
                        } else {
                            $definition = new \Zend\Di\Generator\DefinitionProxy(new \Zend\Di\Definition($type));
                            $this->managedDefinitions->addDefinition($definition);
                        }
                        
                        if ($refParameters = $refMethod->getParameters()) {
                            $params = array();
                            foreach ($refParameters as $refParam) {
                                if ($refTypeClass = $refParam->getClass()) {
                                    //echo '      Param type: ' . $refTypeClass->getName() . PHP_EOL;
                                    $params[] = new \Zend\Di\Reference($refTypeClass->getName());
                                }
                            }
                            $definition->addMethodCall($refMethod->getName(), $params);
                        }
                    }
                }
                
            } catch (\ReflectionException $e) {
                throw new \Exception('An unmanaged type was found as a dependency');
            }
                
        }
    }

}
