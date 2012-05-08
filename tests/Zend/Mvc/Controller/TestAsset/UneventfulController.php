<?php

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\Stdlib\DispatchableInterface,
    Zend\Stdlib\RequestInterface as Request,
    Zend\Stdlib\ResponseInterface as Response;

class UneventfulController implements DispatchableInterface
{
    public function dispatch(Request $request, Response $response = null)
    {
    }
}
