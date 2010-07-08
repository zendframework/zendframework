<?php
namespace Zend\Loader;
require_once __DIR__ . '/../Exception.php';
require_once __DIR__ . '/Exception.php';

class ClassNotFoundException
    extends \Zend\Exception
    implements Exception
{
}
