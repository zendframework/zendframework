<?php

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\Stdlib\Dispatchable,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response;

class UneventfulController implements Dispatchable
{
    public function dispatch(Request $request, Response $response = null)
    {
    }
}
