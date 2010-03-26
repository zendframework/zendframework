<?php
namespace Zend\Loader;

require_once __DIR__ . '/Exception.php';

class InvalidArgumentException
    extends \InvalidArgumentException
    implements Exception
{
}
