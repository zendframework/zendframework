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

/**
 * Writes log messages to syslog
 *
 * @uses       \Zend\Log\Log
 * @uses       \Zend\Log\Exception\InvalidArgumentException
 * @uses       \Zend\Log\Formatter
 * @uses       \Zend\Log\Writer\AbstractWriter
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Syslog extends AbstractWriter
{
    /**
     * Maps Zend_Log priorities to PHP's syslog priorities
     *
     * @var array
     */
    protected $_priorities = array(
        Log\Logger::EMERG  => LOG_EMERG,
        Log\Logger::ALERT  => LOG_ALERT,
        Log\Logger::CRIT   => LOG_CRIT,
        Log\Logger::ERR    => LOG_ERR,
        Log\Logger::WARN   => LOG_WARNING,
        Log\Logger::NOTICE => LOG_NOTICE,
        Log\Logger::INFO   => LOG_INFO,
        Log\Logger::DEBUG  => LOG_DEBUG,
    );

    /**
     * The default log priority - for unmapped custom priorities
     *
     * @var string
     */
    protected $_defaultPriority = LOG_NOTICE;

    /**
     * Last application name set by a syslog-writer instance
     *
     * @var string
     */
    protected static $_lastApplication;

    /**
     * Last facility name set by a syslog-writer instance
     *
     * @var string
     */
    protected static $_lastFacility;

    /**
     * Application name used by this syslog-writer instance
     *
     * @var string
     */
    protected $_application = 'Zend_Log';

    /**
     * Facility used by this syslog-writer instance
     *
     * @var int
     */
    protected $_facility = LOG_USER;

    /**
     * Types of program available to logging of message
     *
     * @var array
     */
    protected $_validFacilities = array();

    /**
     * Class constructor
     *
     * @param  array $params Array of options; may include "application" and "facility" keys
     * @return void
     */
    public function __construct(array $params = array())
    {
        if (isset($params['application'])) {
            $this->_application = $params['application'];
        }

        $runInitializeSyslog = true;
        if (isset($params['facility'])) {
            $this->setFacility($params['facility']);
            $runInitializeSyslog = false;
        }

        if ($runInitializeSyslog) {
            $this->_initializeSyslog();
        }
    }

    /**
     * Create a new instance of Zend_Log_Writer_Syslog
     *
     * @param  array|\Zend\Config\Config $config
     * @return \Zend\Log\Writer\Syslog
     */
    static public function factory($config = array())
    {
        return new self(self::_parseConfig($config));
    }

    /**
     * Initialize values facilities
     *
     * @return void
     */
    protected function _initializeValidFacilities()
    {
        $constants = array(
            'LOG_AUTH',
            'LOG_AUTHPRIV',
            'LOG_CRON',
            'LOG_DAEMON',
            'LOG_KERN',
            'LOG_LOCAL0',
            'LOG_LOCAL1',
            'LOG_LOCAL2',
            'LOG_LOCAL3',
            'LOG_LOCAL4',
            'LOG_LOCAL5',
            'LOG_LOCAL6',
            'LOG_LOCAL7',
            'LOG_LPR',
            'LOG_MAIL',
            'LOG_NEWS',
            'LOG_SYSLOG',
            'LOG_USER',
            'LOG_UUCP'
        );

        foreach ($constants as $constant) {
            if (defined($constant)) {
                $this->_validFacilities[] = constant($constant);
            }
        }
    }

    /**
     * Initialize syslog / set application name and facility
     *
     * @return void
     */
    protected function _initializeSyslog()
    {
        self::$_lastApplication = $this->_application;
        self::$_lastFacility    = $this->_facility;
        openlog($this->_application, LOG_PID, $this->_facility);
    }

    /**
     * Set syslog facility
     *
     * @param  int $facility Syslog facility
     * @return \Zend\Log\Writer\Syslog
     * @throws \Zend\Log\Exception\InvalidArgumentException for invalid log facility
     */
    public function setFacility($facility)
    {
        if ($this->_facility === $facility) {
            return $this;
        }

        if (!count($this->_validFacilities)) {
            $this->_initializeValidFacilities();
        }

        if (!in_array($facility, $this->_validFacilities)) {
            throw new Log\Exception\InvalidArgumentException(
            	'Invalid log facility provided; please see http://php.net/openlog for a list of valid facility values'
            );
        }

        if ('WIN' == strtoupper(substr(PHP_OS, 0, 3))
            && ($facility !== LOG_USER)
        ) {
            throw new Log\Exception\InvalidArgumentException('Only LOG_USER is a valid log facility on Windows');
        }

        $this->_facility = $facility;
        $this->_initializeSyslog();
        return $this;
    }

    /**
     * Set application name
     *
     * @param  string $application Application name
     * @return \Zend\Log\Writer\Syslog
     */
    public function setApplicationName($application)
    {
        if ($this->_application === $application) {
            return $this;
        }
        $this->_application = $application;
        $this->_initializeSyslog();
        return $this;
    }

    /**
     * Close syslog.
     *
     * @return void
     */
    public function shutdown()
    {
        closelog();
    }

    /**
     * Write a message to syslog.
     *
     * @param  array $event event data
     * @return void
     */
    protected function _write($event)
    {
        if (array_key_exists($event['priority'], $this->_priorities)) {
            $priority = $this->_priorities[$event['priority']];
        } else {
            $priority = $this->_defaultPriority;
        }

        if ($this->_application !== self::$_lastApplication
            || $this->_facility !== self::$_lastFacility)
        {
            $this->_initializeSyslog();
        }

        $message = $event['message'];
        if ($this->_formatter instanceof Log\Formatter) {
            $message = $this->_formatter->format($event);
        }

        syslog($priority, $message);
    }
}
