<?php

namespace Zend\Mvc\Application;

class Configuration /* implements Configurable? */
{
    protected $configData;
    
    public function __construct($configData)
    {
        $this->configData = $configData;
    }
    
    /*
    public function fromArray(array $config) {}
    public function fromConfig() {}
    */
    
    public function configure($application)
    {
        if (!$application instanceof Application) {
            throw new \RuntimeException('No application provided');
        }
    }
    
}



