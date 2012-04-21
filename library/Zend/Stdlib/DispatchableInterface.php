<?php
namespace Zend\Stdlib;

interface DispatchableInterface
{
    public function dispatch(RequestInterface $request, ResponseInterface $response = null);
}
