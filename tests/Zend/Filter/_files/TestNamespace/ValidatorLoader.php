<?php

namespace TestNamespace;
use Zend\Validator\ValidatorLoader as BaseValidatorLoader;
require_once __DIR__ . '/MyDigits.php';
require_once __DIR__ . '/StringEquals.php';

class ValidatorLoader extends BaseValidatorLoader
{
    public function __construct($map = null)
    {
        $this->plugins['mydigits']     = 'TestNamespace\MyDigits';
        $this->plugins['stringequals'] = 'TestNamespace\StringEquals';
        parent::__construct($map);
    }
}
