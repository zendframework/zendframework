<?php

namespace Zend\Mvc\Controller\Plugin;

use Zend\Stdlib\Dispatchable;

abstract class AbstractPlugin
{
    protected $controller;

    public function setController(Dispatchable $controller)
    {
        $this->controller = $controller;
    }

    public function getController()
    {
        return $this->controller;
    }
}
