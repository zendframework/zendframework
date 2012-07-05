<?php

namespace Zend\XmlRpc\Server\Exception;

use Zend\XmlRpc\Exception;

class BadMethodCallException extends Exception\BadMethodCallException implements ExceptionInterface
{
}
