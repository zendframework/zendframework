<?php

namespace Zend\XmlRpc\Server\Exception;

use Zend\XmlRpc\Exception;

class RuntimeException
    extends Exception\RuntimeException
    implements ExceptionInterface
{}