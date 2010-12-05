<?php

namespace TestNamespace;
use Zend\Validator\ValidatorBroker as BaseValidatorBroker;
require_once __DIR__ . '/ValidatorLoader.php';

class ValidatorBroker extends BaseValidatorBroker
{
    protected $defaultClassLoader = 'TestNamespace\ValidatorLoader';
}
