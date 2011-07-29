<?php
namespace Zend\Di\Exception;

use Zend\Di\Exception,
    DomainException;

class UndefinedReferenceException extends DomainException implements Exception
{
}
