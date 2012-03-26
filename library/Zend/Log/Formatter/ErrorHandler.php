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
 * @package    Zend_Log
 * @subpackage Formatter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Log\Formatter;
use \Zend\Log\Formatter;

/**
 * @uses       \Zend\Log\Formatter\AbstractFormatter
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Formatter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ErrorHandler extends AbstractFormatter
{
    /**
     * Factory for Zend\Log\Formatter\ErrorHandler
     *
     * @param array|\Zend\Config\Config $options useless
     * @return \Zend\Log\Formatter\Firebug
     */
    public static function factory($options = array())
    {
        return new self;
    }

    /**
     * This method formats the event for the PHP Error Handler.
     *
     * @param  array $event
     * @return string
     */
    public function format($event)
    {
        $output = $event['timestamp'] . ' ' . $event['priorityName'] . ' (' .
                  $event['priority'] . ') ' . $event['message'] . ' (errno ' .
                  $event['extra']['errno'] . ') in ' . $event['extra']['file'] .
                  ' on line ' . $event['extra']['line'];
        return $output;
    }
}