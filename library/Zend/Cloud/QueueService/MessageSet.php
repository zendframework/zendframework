<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace Zend\Cloud\QueueService;

use Countable;
use IteratorAggregate;

/**
 * Collection of message objects
 *
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage QueueService
 */
class MessageSet implements
    Countable,
    IteratorAggregate
{
    /** @var int */
    protected $_messageCount;

    /** @var \ArrayAccess Messages */
    protected $_messages;

    /**
     * Constructor
     *
     * @param  array $messages
     * @return void
     */
    public function __construct(array $messages)
    {
        $this->_messageCount = count($messages);
        $this->_messages     = new \ArrayIterator($messages);
    }

    /**
     * Countable: number of messages in collection
     *
     * @return int
     */
    public function count()
    {
        return $this->_messageCount;
    }

    /**
     * IteratorAggregate: return iterable object
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return $this->_messages;
    }
}
