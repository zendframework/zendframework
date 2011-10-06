<?php

namespace Zend\Di;

use Traversable;

class Configuration
{
    protected $data = array();
    
    /**
     * @var Zend\Di\DependencyInjector
     */
    protected $di = null;
    
    public function __construct($data)
    {
        if ($data instanceof Traversable) {
            if (method_exists($data, 'toArray')) {
                $data = $data->toArray();
            } else {
                $data = iterator_to_array($data, true);
            }
        } elseif (!is_array($data)) {
            throw new Exception\InvalidArgumentException(
                'Configuration data must be of type Zend\Config\Config or an array'
            );
        }
        $this->data = $data;
    }
    
    public function configure(Di $di)
    {
        if (isset($this->data['definition'])) {
            $this->configureDefinition($di, $this->data['definition']);
        }

        if (isset($this->data['instance'])) {
            $this->configureInstance($di, $this->data['instance']);
        }
        
    }

    public function configureDefinition(Di $di, $definition)
    {
        foreach ($definition as $definitionType => $definitionData) {
            switch ($definitionType) {
                case 'class':
                    foreach ($definitionData as $className => $classDefinitionData) {
                        $classDefinitions = $di->definitions()->getDefinitionsByType('Zend\Di\Definition\ClassDefinition');
                        foreach ($classDefinitions as $classDefinition) {
                            if (!$classDefinition->hasClass($className)) {
                                unset($classDefinition);
                            }
                        }
                        if (!isset($classDefinition)) {
                            $classDefinition = new Definition\ClassDefinition($className);
                            $di->definitions()->addDefinition($classDefinition);
                        }
                        foreach ($classDefinitionData as $classDefKey => $classDefData) {
                            switch ($classDefKey) {
                                case 'instatiator':
                                    $classDefinition->setInstantiator($classDefData);
                                    break;
                                case 'supertypes':
                                    $classDefinition->setSupertypes($classDefData);
                                    break;
                                case 'methods':
                                case 'method':
                                    foreach ($classDefData as $methodName => $methodInfo) {
                                        if (isset($methodInfo['required'])) {
                                            $classDefinition->addMethod($methodName, $methodInfo['required']);
                                            unset($methodInfo['required']);
                                        }
                                        foreach ($methodInfo as $paramName => $paramInfo) {
                                            $classDefinition->addMethodParameter($methodName, $paramName, $paramInfo);
                                        }
                                    }
                                default:
                                    $methodName = $classDefKey;
                                    $methodInfo = $classDefData;
                                    if (isset($classDefData['required'])) {
                                        $classDefinition->addMethod($methodName, $methodInfo['required']);
                                        unset($methodInfo['required']);
                                    }
                                    foreach ($methodInfo as $paramName => $paramInfo) {
                                        $classDefinition->addMethodParameter($methodName, $paramName, $paramInfo);
                                    }
                            }
                        }
                    }
                    break;
                case 'compiler':
                    // @todo
                case 'runtime':
                    // @todo
            }

        }

    }
    
    public function configureInstance(Di $di, $instanceData)
    {
        $im = $di->instanceManager();
        
        foreach ($instanceData as $target => $data) {
            switch (strtolower($target)) {
                case 'aliases':
                case 'alias':
                    foreach ($data as $n => $v) {
                        $im->addAlias($n, $v);
                    }
                    break;
                case 'preferences':
                case 'preference':
                    foreach ($data as $n => $v) {
                        if (is_array($v)) {
                            foreach ($v as $v2) {
                                $im->addTypePreference($n, $v2);
                            }
                        } else {
                            $im->addTypePreference($n, $v);
                        }
                    }
                    break;
                default:
                    foreach ($data as $n => $v) {
                        switch ($n) {
                            case 'parameters':
                            case 'parameter':
                                $im->setParameters($target, $v);
                                break;
                            case 'injections':
                            case 'injection':
                                $im->setInjections($target, $v);
                                break;
                        }
                    }
            }
        }

    }
    
}
