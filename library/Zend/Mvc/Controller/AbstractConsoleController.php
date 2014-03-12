<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Controller;

use Zend\Console\Adapter\AdapterInterface as ConsoleAdaper;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Exception\InvalidArgumentException;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

class AbstractConsoleController extends AbstractActionController
{
    /**
     * @var ConsoleAdaper
     */
    protected $console;

    /**
     * @param ConsoleAdaper $console
     * @return self
     */
    public function setConsole(ConsoleAdaper $console)
    {
        $this->console = $console;

        return $this;
    }

    /**
     * @return ConsoleAdaper
     */
    public function getConsole()
    {
        return $this->console;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        if (! $request instanceof ConsoleRequest) {
            throw new InvalidArgumentException(sprintf(
                '%s can only dispatch requests in a console environment',
                get_called_class()
            ));
        }
        return parent::dispatch($request, $response);
    }
}
