<?php
namespace Zend\Loader;

require_once __DIR__ . '/Exception.php';

class MissingResourceNamespaceException
    extends \Exception
    implements Exception
{
}
