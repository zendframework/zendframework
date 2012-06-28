<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace Zend\Cloud\QueueService\Adapter;

use Zend\Cloud\QueueService\Adapter;
use Zend\Cloud\QueueService\Message;
use Zend\Cloud\QueueService\MessageSet;

/**
 * Abstract queue adapter
 *
 * Provides functionality around setting message and message set classes.
 *
 * @category   Zend
 * @package    Zend_Cloud_QueueService
 * @subpackage Adapter
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**@+ option keys */
    const MESSAGE_CLASS    = 'message_class';
    const MESSAGESET_CLASS = 'messageset_class';
    /**@-*/

    /** @var string Class to use for queue messages */
    protected $_messageClass    = 'Zend\Cloud\QueueService\Message';

    /** @var string Class to use for collections of queue messages */
    protected $_messageSetClass = 'Zend\Cloud\QueueService\MessageSet';

    /**
     * Set class to use for message objects
     *
     * @param  string $class
     * @return \Zend\Cloud\QueueService\Adapter\AbstractAdapter
     */
    public function setMessageClass($class)
    {
        $this->_messageClass = (string) $class;
        return $this;
    }

    /**
     * Get class to use for message objects
     *
     * @return string
     */
    public function getMessageClass()
    {
        return $this->_messageClass;
    }

    /**
     * Set class to use for message collection objects
     *
     * @param  string $class
     * @return \Zend\Cloud\QueueService\Adapter\AbstractAdapter
     */
    public function setMessageSetClass($class)
    {
        $this->_messageSetClass = (string) $class;
        return $this;
    }

    /**
     * Get class to use for message collection objects
     *
     * @return string
     */
    public function getMessageSetClass()
    {
        return $this->_messageSetClass;
    }
}
