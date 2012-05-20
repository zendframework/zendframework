<?php

namespace Zend\Authentication\Adapter\Exception;

use Zend\Authentication\Exception;

class RuntimeException 
    extends Exception\RuntimeException
    implements ExceptionInterface
{
}