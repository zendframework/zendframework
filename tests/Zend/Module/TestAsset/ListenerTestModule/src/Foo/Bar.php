<?php

namespace Foo;

use ListenerTestModule\Module,
    Zend\Module\Manager;

class Bar
{
    public $module;
    public $moduleManager;

    public function __construct(Module $module, Manager $moduleManager)
    {
        $this->module  = $module;
        $this->moduleManager = $moduleManager;
    }
}
