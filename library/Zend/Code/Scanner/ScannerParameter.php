<?php

namespace Zend\Code\Scanner;

class ScannerParameter
{
    protected $tokens = null;
    protected $class = null;
    protected $uses = array();
    
    public function __construct(array $parameterTokens, $class = null, array $uses = array())
    {
        $this->tokens = $parameterTokens;
        $this->class = $class;
        $this->uses = $uses;
    }
}