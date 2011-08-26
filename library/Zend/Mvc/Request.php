<?php

namespace Zend\Mvc;

use Zend\Stdlib\RequestDescription;

class Request implements RequestDescription
{
    
    protected $serverRequest = null;
    
    public function __construct(RequestDescription $serverRequest)
    {
        $this->serverRequest = $serverRequest;
    }
    
    public function getServerRequest()
    {
        return $this->serverRequest;
    }

    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
    }
    
    public function getControllerName()
    {
        return $this->controllerName;
    }
    
    // convienence
    public function isHttp()
    {
        return ($this->serverRequest instanceof \Zend\Http\HttpRequest);
    }
    
    public function isConsole()
    {
        return ($this->serverRequest instanceof \Zend\Console\ConsoleRequest);
    }
    
    public function isTest()
    {
        return ($this->serverRequest instanceof \Zend\Test\TestRequest);
    }
    
    public function http()
    {
        if ($this->isHttp()) {
            return $this->serverRequest;
        } else {
            return $this;
        }
    }
    
    public function console()
    {
        if ($this->isConsole()) {
            return $this->serverRequest;
        } else {
            return $this;
        }
    }
        
    public function test()
    {
        if ($this->isTest()) {
            return $this->serverRequest;
        } else {
            return $this;
        }
    }
    
    public function __call($name, $args)
    {
        if (method_exists($this->serverRequest, $name)) {
            return $this->serverRequest->{$name}($args);
        }
        return null;
    }
    
}