<?php

namespace Zend\Di\Generator;

class ManagedDefinitions
{
    
    protected $definitions = array();
    
    public function addDefinitionFromArray(array $values)
    {
        if (!isset($values['class']) 
            || !is_string($values['class']) 
            || empty($values['class'])
        ) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot create definition; provided definition contains no class key (%s)',
                var_export($values, 1)
            ));
        }

        $definition = new \Zend\Di\Definition($values['class']);

        foreach ($values as $key => $value) {
            switch (strtolower($key)) {
                case 'class':
                    break;
                case 'constructor_callback':
                    $callback = $value;
                    if (is_array($value) 
                        && (isset($value['class']) && isset($value['method']))
                    ) {
                        $callback = array($value['class'], $value['method']);
                    }
                    $definition->setConstructorCallback($callback);
                    break;
                case 'params':
                    if (!is_array($value)) {
                        break;
                    }
                    $params = $this->resolveReferences($value);
                    $definition->setParams($params);
                    break;
                case 'param_map':
                    $definition->setParamMap($value);
                    break;
                case 'tags':
                    $definition->addTags($value);
                    break;
                case 'shared':
                    $definition->setShared((bool) $value);
                    break;
                case 'methods':
                    $this->buildMethods($definition, $value);
                    break;
                default:
                    // ignore all other options
                    break;
            }
        }

        $this->addDefinition($definition);
    }
    
    public function addDefinition(\Zend\Di\DependencyDefinition $definition)
    {
        $class = $definition->getClass();
        if ($this->hasDefinition($class)) {
            throw new \InvalidArgumentException('A definition for this class already exist.');
        }
        $this->definitions[$class] = $definition;
    }
    
    public function hasDefinition($class)
    {
        return array_key_exists($class, $this->definitions);
    }
    
    public function getDefinition($class)
    {
        return $this->definitions[$class];
    }
    
    public function mergeObjectConfiguration($objectConfiguration) 
    {
        foreach ($objectConfiguration as $class => $configValues) {
            if (!array_key_exists($class, $this->definitions)) {
                continue;
            }
            $def = $this->definitions[$class];
            $def->setParams($configValues);
        }
    }
    
    public function toArray()
    {
        $defs = array();
        /* @var $definition \Zend\Di\Definition */
        foreach ($this->definitions as $definition) {
            $defArray = $definition->toArray();
            $defs[] = $defArray;
        }
        return $defs;
    }
    
}
