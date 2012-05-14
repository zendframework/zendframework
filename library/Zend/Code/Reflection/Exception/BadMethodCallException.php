<?php

namespace Zend\Code\Reflection\Exception;

use Zend\Code\Exception;

class BadMethodCallException
    extends Exception\BadMethodCallException
    implements ExceptionInterface
{
}
