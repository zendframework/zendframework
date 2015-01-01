<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Service\TestAsset;

use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

class ControllerWithDependencies implements DispatchableInterface
{
    /**
     * @var \stdClass
     */
    public $injectedValue;

    /**
     * @param \stdClass $injected
     */
    public function setInjectedValue(\stdClass $injected)
    {
        $this->injectedValue = $injected;
    }

    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
    }
}
