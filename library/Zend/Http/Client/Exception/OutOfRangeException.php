<?php

namespace Zend\Http\Client\Exception;

use Zend\Http\Exception;

class OutOfRangeException
    extends Exception\OutOfRangeException
    implements ExceptionInterface
{}