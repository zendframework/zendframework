<?php

namespace ZendTest\Mvc\TestAsset;

use Zend\Stdlib\Dispatchable,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response;

class PathController implements Dispatchable
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
