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

use Zend\Log\Logger,
    Zend\Log\Formatter,
    Zend\Wildfire\Plugin\FirePhp;

/**
 * Writes log messages to the Firebug Console via FirePHP.
 *
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
    protected $priorityStyles = array(
        Logger::EMERG  => FirePhp::ERROR,
        Logger::ALERT  => FirePhp::ERROR,
        Logger::CRIT   => FirePhp::ERROR,
        Logger::ERR    => FirePhp::ERROR,
        Logger::WARN   => FirePhp::WARN,
        Logger::NOTICE => FirePhp::INFO,
        Logger::INFO   => FirePhp::INFO,
        Logger::DEBUG  => FirePhp::LOG
    );

    /**
     * The default logging style for un-mapped priorities
     *
     * @var string
     */
    protected $defaultPriorityStyle = FirePhp::LOG;

    /**
     * Flag indicating whether the log writer is enabled
     *
     * @var boolean
     */
    protected $enabled = true;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        if (php_sapi_name() == 'cli') {
            $this->setEnabled(false);
        }

        $this->formatter = new Formatter\Firebug();
    }

    /**
     * Enable or disable the log writer.
     *
     * @param boolean $enabled Set to TRUE to enable the log writer
     * @return boolean The previous value.
     */
    public function setEnabled($enabled)
    {
        $previous = $this->enabled;
        $this->enabled = $enabled;
        return $previous;
    }

    /**
     * Determine if the log writer is enabled.
     *
     * @return boolean Returns TRUE if the log writer is enabled.
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set the default display style for user-defined priorities
     *
     * @param string $style The default log display style
     * @return string Returns previous default log display style
     */
    public function setDefaultPriorityStyle($style)
    {
        $previous = $this->defaultPriorityStyle;
        $this->defaultPriorityStyle = $style;
        return $previous;
    }

    /**
     * Get the default display style for user-defined priorities
     *
     * @return string Returns the default log display style
     */
    public function getDefaultPriorityStyle()
    {
        return $this->defaultPriorityStyle;
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
        if (array_key_exists($priority,$this->priorityStyles)) {
            $previous = $this->priorityStyles[$priority];
        }
        $this->priorityStyles[$priority] = $style;
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
        if (array_key_exists($priority, $this->priorityStyles)) {
            return $this->priorityStyles[$priority];
        }
        return false;
    }

    /**
     * Log a message to the Firebug Console.
     *
     * @param array $event The event data
     * @return void
     */
    protected function doWrite(array $event)
    {
        if (!$this->getEnabled()) {
            return;
        }

        if (array_key_exists($event['priority'], $this->priorityStyles)) {
            $type = $this->priorityStyles[$event['priority']];
        } else {
            $type = $this->defaultPriorityStyle;
        }

        $message = $this->formatter->format($event);
        $label = isset($event['firebugLabel']) ? $event['firebugLabel'] : null;

        FirePhp::getInstance()->send($message,
                                     $label,
                                     $type,
                                     array(
                                         'traceOffset'=>4,
                                         'fixZendLogOffsetIfApplicable' => true
                                     ));
    }
}