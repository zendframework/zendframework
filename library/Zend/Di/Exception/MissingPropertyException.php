<?php
namespace Zend\Di\Exception;

use Zend\Di\Exception,
    DomainException;

class MissingPropertyException extends DomainException implements Exception
{
}
