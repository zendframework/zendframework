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

/**
 * Generic message class
 *
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage QueueService
 */
class Message
{
    protected $_body;
    protected $_clientMessage;

    /**
     * @param string $body Message text
     * @param string $message Original message
     */
    public function __construct($body, $message)
    {
        $this->_body = $body;
        $this->_clientMessage = $message;
    }

    /**
     * Get the message body
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * Get the original adapter-specific message
     */
    public function getMessage()
    {
        return $this->_clientMessage;
    }
}
