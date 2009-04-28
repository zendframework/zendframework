<?php
class ZfAppBootstrap extends Zend_Application_Bootstrap_BootstrapAbstract
{
    public $barExecuted = 0;
    public $fooExecuted = 0;
    public $executedFooResource = false;
    public $executedFooBarResource = false;

    protected $_arbitraryValue;
    
    public function run()
    {
    }

    protected function _initFoo()
    {
        $this->fooExecuted++;
    }

    protected function _initBar()
    {
        $this->barExecuted++;
    }

    protected function _initBarbaz()
    {
        $o = new stdClass();
        $o->baz = 'Baz';
        return $o;
    }

    public function setArbitrary($value)
    {
        $this->_arbitraryValue = $value;
        return $this;
    }

    public function getArbitrary()
    {
        return $this->_arbitraryValue;
    }
}
