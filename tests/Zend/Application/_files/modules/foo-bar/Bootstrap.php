<?php
class FooBar_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public $bootstrapped = false;

    public function run()
    {
    }

    protected function _bootstrap($resource = null)
    {
        $this->bootstrapped = true;
    }
}
