<?php

namespace ListenerTestModule;

class Module
{
    public $initCalled = false;

    public function init($moduleManager = null)
    {
        $this->initCalled = true;
    }
}
