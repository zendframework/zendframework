<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Value\Messaging;

/**
 * This is the type of message returned by the MessageBroker
 * to endpoints after the broker has routed an endpoint's message
 * to a service.
 *
 * flex.messaging.messages.AcknowledgeMessage
 *
 * @package    Zend_Amf
 * @subpackage Value
 */
class AcknowledgeMessage extends AsyncMessage
{
    /**
     * Create a new Acknowledge Message
     *
     * @param unknown_type $message
     */
    public function __construct($message)
    {
        $this->clientId    = $this->generateId();
        $this->destination = null;
        $this->messageId   = $this->generateId();
        $this->timestamp   = time().'00';
        $this->timeToLive  = 0;
        $this->headers     = new \STDClass();
        $this->body        = null;

        // correleate the two messages
        if ($message && isset($message->messageId)) {
            $this->correlationId = $message->messageId;
        }
    }
}
