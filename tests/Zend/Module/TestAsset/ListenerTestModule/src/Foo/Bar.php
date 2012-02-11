<?php

namespace Foo;

use ListenerTestModule\Module;

class Bar
{
    public $module;

    public function __construct(Module $module)
    {
        $this->module = $module;
    }
}
