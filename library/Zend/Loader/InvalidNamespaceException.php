<?php
namespace Zend\Loader;

require_once __DIR__ . '/Exception.php';

class InvalidNamespaceException
    extends \Exception
    implements Exception
{
}
