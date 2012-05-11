<?php

namespace Zend\Di;

use Traversable;
use Zend\Stdlib\ArrayUtils;

class Configuration
{
    protected $data = array();
    
    /**
     * @var Zend\Di\DependencyInjector
     */
    protected $di = null;

    /**
     * @param  array|Traversable $options
     */
    public function __construct($options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException(
                'Configuration data must be of type Traversable or an array'
            );
        }
        $this->data = $options;
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
                case 'compiler':
                    // @todo
                    break;
                case 'runtime':
                    // @todo
                    break;
                case 'class':
                    foreach ($definitionData as $className => $classData) {
                        $classDefinitions = $di->definitions()->getDefinitionsByType('Zend\Di\Definition\ClassDefinition');
                        foreach ($classDefinitions as $classDefinition) {
                            if (!$classDefinition->hasClass($className)) {
                                unset($classDefinition);
                            }
                        }
                        if (!isset($classDefinition)) {
                            $classDefinition = new Definition\ClassDefinition($className);
                            $di->definitions()->addDefinition($classDefinition, false);
                        }
                        foreach ($classData as $classDefKey => $classDefData) {
                            switch ($classDefKey) {
                                case 'instantiator':
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
                                    break;
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
                            case 'shared':
                            case 'share':
                                $im->setShared($target, $v);
                                break;
                        }
                    }
            }
        }

    }

    
}
