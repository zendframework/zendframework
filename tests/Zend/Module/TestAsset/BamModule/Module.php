<?php

namespace BamModule;

use Zend\Config\Config;

class Module
{
	protected $version = 1;
	
    public function init()
    {
        $this->initAutoloader();
    }

    protected function initAutoloader()
    {
        include __DIR__ . '/autoload_register.php';
    }

    public function getConfig()
    {
        return new Config(include __DIR__ . '/configs/config.php');
    }
    
	public function getProvides()
    {
    	return array(
    		__NAMESPACE__ => array(
    	 		'version' => $this->version,
    		),
    	);
    }
    
    public function getDependencies()
    {
    	return array(
			'php' => array(
    			'version' => '5.3.0',
    			'required' => true,
    		),
    		'ext/core' => array(
    			'version' => '0.1',
    			'required' => true,
    		),
    		'BooModule' => true,
    	);
    }
}
