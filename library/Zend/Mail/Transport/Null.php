<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mail\Transport;

use Zend\Mail\Message;

/**
 * File transport
 *
 * The null transport will just store the message in memory.  It is helpful
 * when unit testing.
 */
class Null implements TransportInterface
{
    /**
     * @var Message
     */
    protected $lastMessage;

    /**
     * Takes the last message and Saves it for testing
     *
     * @param Message $message
     */
    public function send(Message $message)
    {
        $this->lastMessage = $message;
    }

    /**
     * Get the last message sent
     *
     * @return Message
     */
    public function getLastMessage()
    {
        return $this->lastMessage;
    }
}
