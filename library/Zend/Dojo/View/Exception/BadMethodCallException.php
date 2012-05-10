<?php

namespace Zend\Dojo\View\Exception;

use Zend\Dojo\Exception;

class BadMethodCallException
    extends Exception\BadMethodCallException
    implements ExceptionInterface
{}
