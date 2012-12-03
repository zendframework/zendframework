<?php

namespace Zend\Mvc\ResponseSender;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\ResponseInterface;

abstract class AbstractResponseSender implements ResponseSenderInterface
{
    /**#@+
     * Response sender events triggered by eventmanager
     */
    const EVENT_SEND_HEADERS  = 'sendHeaders';
    const EVENT_SEND_CONTENT  = 'sendContent';
    const EVENT_SEND_RESPONSE = 'sendResponse';
    /**#@-*/

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Get response
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set response
     *
     * @param ResponseInterface $response
     * @return void
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Inject an EventManager instance
     *
     * @param  EventManagerInterface $eventManager
     * @return void
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(
            'Zend\Mvc\ResponseSender\ResponseSenderInterface',
            __CLASS__,
            get_called_class(),
            'response_sender',
        ));
        $this->events = $eventManager;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (!$this->events instanceof EventManagerInterface) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }

}
