<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc;

use Traversable;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ResponseInterface as Response;

/**
 * @category   Zend
 * @package    Zend_Mvc
 */
class SendResponseListener implements
    ListenerAggregateInterface,
    ServiceLocatorAwareInterface
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'sendResponse'), -10000);
    }

    /**
     * Detach aggregate listeners from the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Send the response
     *
     * @param  MvcEvent $e
     * @return mixed
     */
    public function sendResponse(MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response instanceof Response) {
            return false; // there is no response to send
        }

        $responseType = get_class($response);
        if (!isset($this->options[$responseType])) {
            return false; // there is no known response type to send
        }

        $responseSender = $this->getServiceLocator()->get($this->options[$responseType]);
        $responseSender->setResponse($response);
        $responseSender->sendResponse();
    }
}
