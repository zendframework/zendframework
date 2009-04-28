<?php
class Zend_Application_BootstrapTest_Resource_Baz extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $o = new stdClass();
        $o->baz = 'Baz';
        return $o;
    }
}
