<?php

namespace Zend\Di;

class Configuration
{
    protected $data = array();
    
    public function __construct($data)
    {
        if ($data instanceof \Zend\Config\Config) {
            $data = $data->toArray();
        }
    }
    
    public function getDefinition()
    {
        
    }
    
    public function getInstanceManager()
    {
        
    }
    
}