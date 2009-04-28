<?php
class Zend_Application_BootstrapTest_Resource_Foo extends Zend_Application_Resource_ResourceAbstract
{
    public $someArbitraryKey;

    public function init()
    {
        $this->getBootstrap()->executedFooResource = true;
    }

    public function setSomeArbitraryKey($value)
    {
        $this->someArbitraryKey = $value;
    }
}
