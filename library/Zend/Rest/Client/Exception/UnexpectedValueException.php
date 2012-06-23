<?php

namespace Zend\Rest\Client\Exception;

use Zend\Rest\Exception;

class UnexpectedValueException
    extends Exception\UnexpectedValueException
    implements ExceptionInterface
{}