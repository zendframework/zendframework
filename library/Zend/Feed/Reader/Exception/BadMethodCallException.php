<?php

namespace Zend\Feed\Reader\Exception;

use Zend\Feed\Exception;

class BadMethodCallException
    extends Exception\BadMethodCallException
    implements ExceptionInterface
{}