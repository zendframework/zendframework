<?php

namespace Zend\Db\Adapter\Driver\Sqlsrv;

class ErrorException extends \Exception
{
    protected $errors = array();
    
    public function __construct($errors = false)
    {
        $this->errors = ($errors === false) ? sqlsrv_errors() : $errors;
    }
    
}