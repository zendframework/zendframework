<?php

namespace Zend\Amf\Parser\Exception;

use Zend\Amf\Exception;

class OutOfBoundsException
    extends Exception\OutOfBoundsException
    implements ExceptionInterface
{
}
