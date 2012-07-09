<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Queue
 */

namespace ZendTest\Queue\Custom;

/*
 * The adapter test class provides a universal test class for all of the
 * abstract methods.
 *
 * All methods marked not supported are explictly checked for for throwing
 * an exception.
 */

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 */
class Queue extends \Zend\Queue\Queue
{
    /**
     * Constructor
     *
     * Can be called as
     * $queue = new \ZendTest\Queue\Custom\Queue($config);
     * - or -
     * $queue = new \ZendTest\Queue\Custom\Queue('ArrayAdapter', $config);
     * - or -
     * $queue = new \ZendTest\Queue\Custom\Queue(null, $config); // Zend_Queue->createQueue();
     *
     * @param Zend_Queue_Adapter_Abstract|string $adapter adapter object or class name
     * @param Zend_Config|array  $config Zend_Config or an configuration array
     */
    public function __construct()
    {
        $args = func_get_args();
        call_user_func_array(array($this, 'parent::__construct'), $args);

        $this->setMessageClass('\ZendTest\Queue\Custom\Message');
        $this->setMessageSetClass('\ZendTest\Queue\Custom\Messages');
    }

    /**
     * Send a message to the queue
     *
     * @param  \ZendTest\Queue\Custom\Message|\ZendTest\Queue\Custom\Messages $message message
     * @return $this
     * @throws Zend_Queue_Exception
     */
    public function send($message)
    {
        if (! ($message instanceof Message || $message instanceof Messages) ) {
            throw new \Zend\Queue\Exception(
               '$message must be an instance of \ZendTest\Queue\Custom\Message or \ZendTest\Queue\Custom\Messages'
            );
        }
        if ($message instanceof Message) {
            $response = parent::send($message->__toString());
        } else {
            foreach($message as $i => $one) {
                $response = parent::send($one->__toString());
            }
        }

        return $this;
    }
}
