<?php
namespace ZendTest\Mvc\Service\TestAsset;

class ControllerWithDependencies
{
    /**
     * @var \stdClass
     */
    public $injectedValue;

    /**
     * @param \stdClass $injected
     */
    public function setInjectedValue(\stdClass $injected)
    {
        $this->injectedValue = $injected;
    }
}
