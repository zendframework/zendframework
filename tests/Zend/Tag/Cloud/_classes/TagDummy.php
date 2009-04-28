<?php
require_once 'Zend/Tag/Cloud/Decorator/HtmlTag.php';

class Zend_Tag_Cloud_Decorator_Dummy_TagDummy extends Zend_Tag_Cloud_Decorator_HtmlTag
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
