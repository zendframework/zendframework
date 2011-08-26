<?php

namespace Zend\Mvc;

use Zend\Stdlib\ResponseDescription;

class Request implements ResponseDescription
{
    public function __construct(RequestDescription $serverResponse) {}
    
    public function canAcceptHeaders()
    {
        // @todo
    }
    
    public function startStreaming()
    {
        // @todo
    }
    public function startOutputBuffer()
    {
        // @todo
    }
    public function endOutputBuffer()
    {
        // @todo
    }
    
    // convienence
    public function isHttp()
    {
        return ($this->serverRequest instanceof \Zend\Http\HttpResponse);
    }
    
    public function isConsole()
    {
        return ($this->serverRequest instanceof \Zend\Console\ConsoleResponse);
    }
    
    public function isTest()
    {
        return ($this->serverRequest instanceof \Zend\Test\TestResponse);
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