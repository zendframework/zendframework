<?php

namespace ListenerTestModule;

class Module
{
    public $initCalled = false;
    public $getConfigCalled = false;

    public function init($moduleManager = null)
    {
        $this->initCalled = true;
    }

    public function getConfig()
    {
        $this->getConfigCalled = true;
        return array(
            'listener' => 'test'
        );
    }
}
