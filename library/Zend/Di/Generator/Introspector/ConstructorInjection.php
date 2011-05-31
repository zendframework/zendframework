<?php

namespace Zend\Di\Generator\Introspector;

class ConstructorInjection implements \Zend\Di\Generator\Introspector
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
                //echo 'Reflecting ' . $type . PHP_EOL;
                if ($refClass->hasMethod('__construct')) {
                    
                    $refConstructor = $refClass->getMethod('__construct');
                    if ($refParameters = $refConstructor->getParameters()) {
                        
                        //echo '    Found injectable __construct ' . $type . PHP_EOL;
                        
                        if ($this->managedDefinitions->hasDefinition($type)) {
                            $definition = $this->managedDefinitions->getDefinition($type);
                        } else {
                            $definition = new \Zend\Di\Generator\DefinitionProxy(new \Zend\Di\Definition($type));
                            $this->managedDefinitions->addDefinition($definition);
                        }
                        
                        $paramMaps = array();
                        $params = array();
                        foreach ($refParameters as $refParam) {
                            $paramMaps[$refParam->getName()] = $refParam->getPosition();
                            
                            if ($refTypeClass = $refParam->getClass()) {
                                                            
                                //echo '      Param type: ' . $refTypeClass->getName() . PHP_EOL;
                                $params[$refParam->getName()] = new \Zend\Di\Reference($refTypeClass->getName());
                            }
                        }
                        $definition->setParamMap($paramMaps);
                        if ($params) {
                            $definition->setParams($params);
                        }
                    }
                }
            } catch (\ReflectionException $e) {
                throw new \Exception('An unmanaged type was found as a dependency');
            }
                
        }
    }

}
