<?php

namespace Zend\Stdlib;

abstract class Options implements ParameterObject
{

    public function __construct($config = null)
    {
        if (!is_null($config)) {
            if (is_array($config) || $config instanceof \Traversable) {
                $this->processArray($config);
            } else {
                throw new \InvalidArgumentException(
                    'Parameter to \\Zend\\Stdlib\\Options\'s '
                    . 'constructor must be an array or implement the '
                    . '\\Traversable interface'
                );
            }
        }
    }

    protected function processArray(array $config)
    {
        foreach ($config as $key => $value) {
            $setter = $this->assembleSetterNameFromConfigKey($key);
            $this->{$setter}($value);
        }
    }

    protected function assembleSetterNameFromConfigKey($key)
    {
        $parts = explode('_', $key);
        $parts = array_map('ucfirst', $parts);
        $setter = 'set' . implode('', $parts);
        if (!method_exists($this, $setter)) {
            throw new \BadMethodCallException(
                'The configuration key "' . $key . '" does not '
                . 'have a matching ' . $setter . ' setter method '
                . 'which must be defined'
            );
        }
        return $setter;
    }
   
    protected function assembleGetterNameFromConfigKey($key)
    {
        $parts = explode('_', $key);
        $parts = array_map('ucfirst', $parts);
        $getter = 'get' . implode('', $parts);
        if (!method_exists($this, $getter)) {
            throw new \BadMethodCallException(
                'The configuration key "' . $key . '" does not '
                . 'have a matching ' . $getter . ' getter method '
                . 'which must be defined'
            );
        }
        return $getter;
    }
   
    public function __set($key, $value)
    {
        $setter = $this->assembleSetterNameFromConfigKey($key);
        $this->{$setter}($value);
    }
   
    public function __get($key)
    {
        $getter = $this->assembleGetterNameFromConfigKey($key);
        return $this->{$getter}();
    }
   
    public function __isset($key)
    {
        $getter = $this->assembleGetterNameFromConfigKey($key);
        return !is_null($this->{$getter}());
    }
   
    public function __unset($key)
    {
        $setter = $this->assembleSetterNameFromConfigKey($key);
        try {
            $this->{$setter}(null);
        } catch(\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(
                'The class property $' . $key . ' cannot be unset as'
                . ' NULL is an invalid value for it: ' . $e->getMessage()
            );
        }
    }

}