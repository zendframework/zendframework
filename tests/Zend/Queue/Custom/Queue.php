<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
