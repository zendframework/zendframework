<?php
class BootstrapBaseCircularDependency extends Zend_Application_Bootstrap_BootstrapAbstract
{
    public $complete = false;

    public function run()
    {
    }

    public function _initFirst()
    {
        $this->bootstrap('Second');
        $this->complete = true;
    }

    public function _initSecond()
    {
        $this->bootstrap('First');
        $this->complete = true;
    }
}
