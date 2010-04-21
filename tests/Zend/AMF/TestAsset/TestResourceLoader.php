<?php

namespace ZendTest\AMF\TestAsset;

class TestResourceLoader implements \Zend\Loader\PluginLoader\PluginLoaderInterface 
{
    public $suffix;
    public $namespace = 'ZendTest\\AMF\\TestAsset\\';
    
    public function __construct($suffix) 
    {
        $this->suffix = $suffix;
    }

    public function addPrefixPath($prefix, $path) {}
    public function removePrefixPath($prefix, $path = null) {}
    public function isLoaded($name) {}
    public function getClassName($name) {}

    public function load($name) 
    {
        return $this->namespace . $name . $this->suffix;
    }
}
