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
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Log\Writer;
use Zend\Log;
use Zend\Wildfire\Plugin\FirePhp;

/**
 * Writes log messages to the Firebug Console via FirePHP.
 *
 * @uses       \Zend\Log\Logger
 * @uses       \Zend\Log\Formatter\Firebug
 * @uses       \Zend\Log\Writer\AbstractWriter
 * @uses       \Zend\Wildfire\Plugin\FirePhp
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Firebug extends AbstractWriter
{
    /**
     * Maps logging priorities to logging display styles
     *
     * @var array
     */
    protected $_priorityStyles = array(Log\Logger::EMERG  => FirePhp::ERROR,
                                       Log\Logger::ALERT  => FirePhp::ERROR,
                                       Log\Logger::CRIT   => FirePhp::ERROR,
                                       Log\Logger::ERR    => FirePhp::ERROR,
                                       Log\Logger::WARN   => FirePhp::WARN,
                                       Log\Logger::NOTICE => FirePhp::INFO,
                                       Log\Logger::INFO   => FirePhp::INFO,
                                       Log\Logger::DEBUG  => FirePhp::LOG);

    /**
     * The default logging style for un-mapped priorities
     *
     * @var string
     */
    protected $_defaultPriorityStyle = FirePhp::LOG;

    /**
     * Flag indicating whether the log writer is enabled
     *
     * @var boolean
     */
    protected $_enabled = true;

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct()
    {
        if (php_sapi_name() == 'cli') {
            $this->setEnabled(false);
        }

        $this->_formatter = new Log\Formatter\Firebug();
    }

    /**
     * Create a new instance of Zend_Log_Writer_Firebug
     *
     * @param  array|\Zend\Config\Config $config
     * @return \Zend\Log\Writer\Firebug
     */
    static public function factory($config = array())
    {
        return new self();
    }

    /**
     * Enable or disable the log writer.
     *
     * @param boolean $enabled Set to TRUE to enable the log writer
     * @return boolean The previous value.
     */
    public function setEnabled($enabled)
    {
        $previous = $this->_enabled;
        $this->_enabled = $enabled;
        return $previous;
    }

    /**
     * Determine if the log writer is enabled.
     *
     * @return boolean Returns TRUE if the log writer is enabled.
     */
    public function getEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Set the default display style for user-defined priorities
     *
     * @param string $style The default log display style
     * @return string Returns previous default log display style
     */
    public function setDefaultPriorityStyle($style)
    {
        $previous = $this->_defaultPriorityStyle;
        $this->_defaultPriorityStyle = $style;
        return $previous;
    }

    /**
     * Get the default display style for user-defined priorities
     *
     * @return string Returns the default log display style
     */
    public function getDefaultPriorityStyle()
    {
        return $this->_defaultPriorityStyle;
    }

    /**
     * Set a display style for a logging priority
     *
     * @param int $priority The logging priority
     * @param string $style The logging display style
     * @return string|boolean The previous logging display style if defined or TRUE otherwise
     */
    public function setPriorityStyle($priority, $style)
    {
        $previous = true;
        if (array_key_exists($priority,$this->_priorityStyles)) {
            $previous = $this->_priorityStyles[$priority];
        }
        $this->_priorityStyles[$priority] = $style;
        return $previous;
    }

    /**
     * Get a display style for a logging priority
     *
     * @param int $priority The logging priority
     * @return string|boolean The logging display style if defined or FALSE otherwise
     */
    public function getPriorityStyle($priority)
    {
        if (array_key_exists($priority,$this->_priorityStyles)) {
            return $this->_priorityStyles[$priority];
        }
        return false;
    }

    /**
     * Log a message to the Firebug Console.
     *
     * @param array $event The event data
     * @return void
     */
    protected function _write($event)
    {
        if (!$this->getEnabled()) {
            return;
        }

        if (array_key_exists($event['priority'],$this->_priorityStyles)) {
            $type = $this->_priorityStyles[$event['priority']];
        } else {
            $type = $this->_defaultPriorityStyle;
        }

        $message = $this->_formatter->format($event);

        $label = isset($event['firebugLabel'])?$event['firebugLabel']:null;

        FirePhp::getInstance()->send($message,
                                     $label,
                                     $type,
                                     array('traceOffset'=>4,
                                           'fixZendLogOffsetIfApplicable'=>true));
    }
}
