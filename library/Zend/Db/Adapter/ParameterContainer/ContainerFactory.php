<?php

namespace Zend\Db\Adapter\DriverStatement;

class ContainerFactory
{
    public function createContainer($parameters)
    {
        if (!is_array($parameters)) {
            $parameters = array($parameters);
        }
        
        if (is_int(key($parameters))) {
            return new PositionalParameterContainer(count($parameters), $parameters);
        }
        
        if (is_string(key($parameters))) {
            return new MappedParameterContainer(array_keys($parameters), $parameters);
        }

        throw new \InvalidArgumentException('Unknown state for factory.');
    }
}
