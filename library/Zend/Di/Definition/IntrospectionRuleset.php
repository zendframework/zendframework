<?php

namespace Zend\Di\Definition;

class IntrospectionRuleset
{
    const TYPE_CONSTRUCTOR = 'constructor';
    const TYPE_SETTER = 'setter';
    const TYPE_INTERFACE = 'interface';
    
    protected $construtorRules = array(
        'enabled'		  => true,
        'includedClasses' => array(),
        'excludedClasses' => array(),
        );
    
    protected $setterRules = array(
        'enabled'		      => true,
        'pattern'             => 'set[A-Z]{1}\w*',
        'includedClasses'     => array(),
        'excludedClasses'     => array(),
        /* 'includedMethods'     => array(),
        'excludedMethods'     => array(), */
        'methodMaximumParams' => 1,
        'paramTypeMustExist'  => true,
        'paramCanBeOptional'  => false,
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
            case self::TYPE_CONSTRUCTOR:
                $rule = &$this->construtorRules;
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
            case 'bool':
                $rule[$name] = (bool) $value;
                break;
            case 'string':
                $rule[$name] = (string) $value;
                break;
        }
        
        return $this;
    }
    
    public function getRules()
    {
        return array(
            self::TYPE_CONSTRUCTOR => $this->construtorRules,
            self::TYPE_SETTER => $this->setterRules,
            self::TYPE_INTERFACE => $this->interfaceRules
        );
    }
    
    public function addConstructorRule($name, $value)
    {
        $this->addRule(self::TYPE_CONSTRUCTOR, $name, $value);
    }
    
    public function getConstructorRules()
    {
        return $this->construtorRules;
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
