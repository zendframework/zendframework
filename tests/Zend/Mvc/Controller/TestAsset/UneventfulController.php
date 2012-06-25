<?php

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

class UneventfulController implements DispatchableInterface
{
    public function dispatch(Request $request, Response $response = null)
    {
    }
}
