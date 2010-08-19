<?php
namespace Zend\Loader;

/** Zend\Loader\Exception */
require_once __DIR__ . '/Exception.php';

class InvalidArgumentException
    extends \InvalidArgumentException
    implements Exception
{
}
