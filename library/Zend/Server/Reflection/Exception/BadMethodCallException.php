<?php

namespace Zend\Server\Reflection\Exception;

use Zend\Server\Exception;

class BadMethodCallException
    extends Exception\BadMethodCallException
    implements ExceptionInterface
{}
