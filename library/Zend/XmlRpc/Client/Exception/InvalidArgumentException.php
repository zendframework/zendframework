<?php

namespace Zend\XmlRpc\Client\Exception;

use Zend\XmlRpc\Exception;

class InvalidArgumentException
    extends Exception\InvalidArgumentException
    implements ExceptionInterface
{}