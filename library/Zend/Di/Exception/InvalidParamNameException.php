<?php
namespace Zend\Di\Exception;

use InvalidArgumentException,
    Zend\Di\Exception;

class InvalidParamNameException 
    extends InvalidArgumentException 
    implements Exception
{
}
