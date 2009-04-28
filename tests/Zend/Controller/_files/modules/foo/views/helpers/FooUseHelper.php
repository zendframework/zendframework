<?php
require_once 'Zend/View/Helper/Abstract.php';

class Foo_View_Helper_FooUseHelper extends Zend_View_Helper_Abstract
{
    public function fooUseHelper()
    {
        return __FUNCTION__ . ' invoked';
    }
}
