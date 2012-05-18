<?php

namespace Zend\Authentication\Adapter\Http\Exception;

use Zend\Authentication\Adapter\Exception;

class RuntimeException
    extends Exception\RuntimeException
    implements ExceptionInterface
{
}