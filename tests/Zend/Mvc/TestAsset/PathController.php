<?php

namespace ZendTest\Mvc\TestAsset;

use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

class PathController implements DispatchableInterface
{
    public function dispatch(Request $request, Response $response = null)
    {
        if (!$response) {
            $response = new HttpResponse();
        }
        $response->setContent(__METHOD__);
        return $response;
    }
}
