<?php
namespace ZendTest\Mvc\Service\TestAsset;

use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

class ControllerWithDependencies implements DispatchableInterface
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

    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
    }
}
