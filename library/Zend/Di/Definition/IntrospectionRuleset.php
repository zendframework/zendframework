<?php

namespace Zend\Di\Definition;

class IntrospectionRuleset
{
    const TYPE_GENERAL = 'general';
    const TYPE_CONSTRUCTOR = 'constructor';
    const TYPE_SETTER = 'setter';
    const TYPE_INTERFACE = 'interface';
    
    protected $generalRules = array(
        'excludedClassPatterns' => array('/[\w\\\\]*Exception\w*/')
        );
    
    protected $constructorRules = array(
        'enabled'		  => true,
        'includedClasses' => array(),
        'excludedClasses' => array(),
        );
    
    protected $setterRules = array(
        'enabled'		      => true,
        'pattern'             => '^set[A-Z]{1}\w*',
        'includedClasses'     => array(),
        'excludedClasses'     => array('ArrayObject'),
        'methodMaximumParams' => 1,
        'paramCanBeOptional'  => true,
        );
    
    protected $interfaceRules = array(
        'enabled'            => true,
        'pattern'            => '\w*Aware\w*',
        'includedInterfaces' => array(),
        'excludedInterfaces' => array()
        );
    
    public function __construct($config = null)
    {
        
    }
    
    public function addRule($strategy, $name, $value)
    {
        switch ($strategy) {
            case self::TYPE_GENERAL:
                $rule = &$this->generalRules;
                break;
            case self::TYPE_CONSTRUCTOR:
                $rule = &$this->constructorRules;
                break;
            case self::TYPE_SETTER:
                $rule = &$this->setterRules;
                break;
            case self::TYPE_INTERFACE:
                $rule = &$this->interfaceRules;
                break;
        }
        
        if (!isset($rule[$name])) {
            throw new \InvalidArgumentException('The rule name provided is not a valid rule name.');
        }

        switch (gettype($rule[$name])) {
            case 'array':
                array_push($rule[$name], $value);
                break;
            case 'boolean':
                $rule[$name] = (bool) $value;
                break;
            case 'string':
                $rule[$name] = (string) $value;
                break;
        }

        return $this;
    }
    
    public function getRules($ruleType)
    {
        if (!$ruleType) {
            return array(
                self::TYPE_GENERAL => $this->generalRules,
                self::TYPE_CONSTRUCTOR => $this->constructorRules,
                self::TYPE_SETTER => $this->setterRules,
                self::TYPE_INTERFACE => $this->interfaceRules
            );
        } else {
            switch ($ruleType) {
                case self::TYPE_GENERAL: return $this->generalRules;
                case self::TYPE_CONSTRUCTOR: return $this->constructorRules;
                case self::TYPE_SETTER: return $this->setterRules;
                case self::TYPE_INTERFACE: return $this->interfaceRules;
            }
        }
    }
    
    public function addGeneralRule($name, $value)
    {
        $this->addRule(self::TYPE_GENERAL, $name, $value);
    }
    
    public function getGeneralRules()
    {
        return $this->generalRules;
    }
    
    public function addConstructorRule($name, $value)
    {
        $this->addRule(self::TYPE_CONSTRUCTOR, $name, $value);
    }
    
    public function getConstructorRules()
    {
        return $this->constructorRules;
    }
    
    public function addSetterRule($name, $value)
    {
        $this->addRule(self::TYPE_SETTER, $name, $value);
    }
    
    public function getSetterRules()
    {
        return $this->setterRules;
    }
    
    public function addInterfaceRule($name, $value)
    {
        $this->addRule(self::TYPE_INTERFACE, $name, $value);
    }

    public function getInterfaceRules()
    {
        return $this->interfaceRules;
    }
    
}
