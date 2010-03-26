<?php
namespace Zend\Loader;

require_once __DIR__ . '/Exception.php';

class ClassNotFoundException
    extends \Exception
    implements Exception
{
}
