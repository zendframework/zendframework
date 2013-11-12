<?php

/**
 * Description of AbstractConsoleController
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
namespace Zend\Mvc\Controller;

use Zend\Console\Request as ConsoleRequest;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

class AbstractConsoleController extends AbstractActionController
{
    protected $console;

    public function setConsole(Zend\Console\Adapter\AdapterInterface $console)
    {
        $this->console = $console;
    }

    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        if (! $request instanceof ConsoleRequest) {
            throw new Exception\InvalidArgumentException(
                    'Expected an Console request');
        }

        parent::dispatch($request, $response);
    }
}