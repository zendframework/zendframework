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
 * This type of message contains information needed to perform
 * a Remoting invocation.
 *
 * Corresponds to flex.messaging.messages.RemotingMessage
 *
 * @package    Zend_Amf
 * @subpackage Value
 */
class RemotingMessage extends AbstractMessage
{

    /**
     * The name of the service to be called including package name
     * @var String
     */
    public $source;

    /**
     * The name of the method to be called
     * @var string
     */
    public $operation;

    /**
     * The arguments to call the mathod with
     * @var array
     */
    public $parameters;

    /**
     * Create a new Remoting Message
     *
     * @return void
     */
    public function __construct()
    {
        $this->clientId    = $this->generateId();
        $this->destination = null;
        $this->messageId   = $this->generateId();
        $this->timestamp   = time().'00';
        $this->timeToLive  = 0;
        $this->headers     = new \stdClass();
        $this->body        = null;
    }
}
