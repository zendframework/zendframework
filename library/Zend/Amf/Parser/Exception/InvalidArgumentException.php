<?php

namespace Zend\Amf\Parser\Exception;

use Zend\Amf\Exception;

class InvalidArgumentException
    extends Exception\InvalidArgumentException
    implements ExceptionInterface
{
}