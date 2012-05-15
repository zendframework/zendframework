<?php

namespace Zend\Db\Adapter\Exception;

class InvalidConnectionParametersException extends \RuntimeException implements ExceptionInterface
{
    protected $parameters;
    public function __construct($message, $parameters)
    {
        parent::__construct($message);
        $this->parameters = $parameters;
    }
}