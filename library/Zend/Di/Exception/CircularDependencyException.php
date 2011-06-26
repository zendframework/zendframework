<?php
namespace Zend\Di\Exception;

use Zend\Di\Exception,
    DomainException;

class CircularDependencyException extends DomainException implements Exception
{
}
