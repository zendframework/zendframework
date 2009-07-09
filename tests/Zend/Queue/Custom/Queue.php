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
 * @subpackage Custom
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 13626 2009-01-14 18:24:57Z matthew $
 */

/*
 * The adapter test class provides a universal test class for all of the
 * abstract methods.
 *
 * All methods marked not supported are explictly checked for for throwing
 * an exception.
 */

/** Zend_Queue */
require_once 'Zend/Queue.php';

/** Zend_Queue */
require_once 'Zend/Queue/Message.php';

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage Custom
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 13626 2009-01-14 18:24:57Z matthew $
 */

class Custom_Queue extends Zend_Queue
{
    /**
     * Constructor
     *
     * Can be called as
     * $queue = new Zend_Queue($config);
     * - or -
     * $queue = new Zend_Queue('array', $config);
     * - or -
     * $queue = new Zend_Queue(null, $config); // Zend_Queue->createQueue();
     *
     * @param Zend_Queue_Adapter_Abstract|string $adapter adapter object or class name
     * @param Zend_Config|array  $config Zend_Config or an configuration array
     */
    public function __construct()
    {
        $args = func_get_args();
        call_user_func_array(array($this, 'parent::__construct'), $args);

        $this->setMessageClass('Custom_Message');
        $this->setMessageSetClass('Custom_Messages');

        $this->getLogger()->debug('Succcessfully created class: ' . get_class($this));
    }

    /**
     * Send a message to the queue
     *
     * @param  Custom_Message|Custom_Messages $message message
     * @return $this
     * @throws Zend_Queue_Exception
     */
    public function send($message)
    {
        if (! ($message instanceof Custom_Message || $message instanceof Custom_Messages) ) {
            /**
             * @see Zend_Queue_Exception
             */
            require_once 'Zend/Queue/Exception.php';
            throw new Zend_Queue_Exception('$message must be an instance of Custom_Message or Custom_Messages');
        }
        if ($message instanceof Custom_Message) {
            $response = parent::send($message->__toString());
        } else {
            foreach($message as $i => $one) {
                $response = parent::send($one->__toString());
            }
        }

        return $this;
    }
}
