<?php

namespace Zend\XmlRpc\Server\Exception;

use Zend\XmlRpc\Exception;

class InvalidArgumentException
    extends Exception\InvalidArgumentException
    implements ExceptionInterface
{}