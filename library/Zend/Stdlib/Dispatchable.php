<?php
namespace Zend\Stdlib;

interface Dispatchable
{
    public function dispatch(RequestDescription $request, ResponseDescription $response = null);
}
