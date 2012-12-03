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

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ResponseSender\ResponseSenderInterface;
use Zend\Stdlib\ResponseInterface as Response;

/**
 * @category   Zend
 * @package    Zend_Mvc
 */
class SendResponseListener implements ListenerAggregateInterface
{
    /**
     * @var ResponseSenderInterface
     */
    protected $responseSender;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Set response sender
     *
     * @param ResponseSenderInterface $responseSender
     */
    public function setResponseSender(ResponseSenderInterface $responseSender)
    {
        $this->responseSender = $responseSender;
    }

    /**
     * Get response sender
     *
     * @return ResponseSenderInterface
     */
    public function getResponseSender()
    {
        return $this->responseSender;
    }

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'injectResponseSender'), -5000);
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
     * Inject response sender
     *
     * @param MvcEvent $e
     * @return void
     */
    public function injectResponseSender(MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response instanceof Response) {
            return; // there is no response to send
        }
        $app = $e->getApplication();
        $serviceManager = $app->getServiceManager();
        $this->setResponseSender($serviceManager->get('ResponseSender'));
    }

    /**
     * Send the response
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function sendResponse(MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response instanceof Response) {
            return; // there is no response to send
        }
        $responseSender = $this->getResponseSender();
        $responseSender->sendResponse();
    }
}
