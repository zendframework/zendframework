<?php

namespace Zend\Mvc\Controller\Plugin;

use Zend\Stdlib\DispatchableInterface;

abstract class AbstractPlugin
{
    protected $controller;

    public function setController(DispatchableInterface $controller)
    {
        $this->controller = $controller;
    }

    public function getController()
    {
        return $this->controller;
    }
}
