<?php
class ZfModule_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function run()
    {
    }

    public function setFoo($value)
    {
        $this->foo = $value;
        return $this;
    }
}
