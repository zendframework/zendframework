<?php
namespace Zend\Loader;

require_once __DIR__ . '/Exception.php';

class BadMethodCallException
    extends \BadMethodCallException
    implements Exception
{
}
