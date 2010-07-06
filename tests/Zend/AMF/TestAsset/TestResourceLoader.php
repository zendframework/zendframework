<?php

namespace ZendTest\AMF\TestAsset;

use Zend\Loader\PrefixPathMapper;

class TestResourceLoader implements PrefixPathMapper
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
