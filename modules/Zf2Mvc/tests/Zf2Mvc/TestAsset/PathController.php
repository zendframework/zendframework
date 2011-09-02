<?php

namespace Zf2Mvc\TestAsset;

use Zend\Stdlib\Dispatchable,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response;

class PathController implements Dispatchable
{
    public function Dispatch(Request $request, Response $response = null)
    {
    }
}
