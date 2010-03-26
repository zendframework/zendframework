<?php
namespace Zend\Loader;

require_once __DIR__ . '/Exception.php';

class InvalidDirectoryArgumentException
    extends \InvalidArgumentException
    implements Exception
{
}
