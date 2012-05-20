<?php

namespace Zend\Authentication\Adapter\Http\Exception;

use Zend\Authentication\Adapter\Exception;

class InvalidArgumentException
    extends Exception\InvalidArgumentException
    implements ExceptionInterface
{
}