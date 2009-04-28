<?php
require_once 'Zend/Tag/Cloud/Decorator/HtmlCloud.php';

class Zend_Tag_Cloud_Decorator_Dummy_CloudDummy1 extends Zend_Tag_Cloud_Decorator_HtmlCloud
{
    protected $_foo;

    public function setFoo($value)
    {
        $this->_foo = $value;
    }
    
    public function getFoo()
    {
        return $this->_foo;
    }
}
