<?php
namespace Zend\Stdlib;

interface Dispatchable
{
    public function dispatch(Request $request, Response $response = null);
}
