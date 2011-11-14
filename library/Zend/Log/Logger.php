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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Log;

use DateTime,
    SplStack,
    Zend\Loader\Broker,
    Zend\Loader\Pluggable;

/**
 * Logging messages with a stack of backends
 *
 * @category   Zend
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Logger implements Loggable, Pluggable
{
    /**#@+
     * @const int defined from the BSD Syslog message severities
     * @link http://tools.ietf.org/html/rfc3164
     */
    const EMERG  = 0;
    const ALERT  = 1;
    const CRIT   = 2;
    const ERR    = 3;
    const WARN   = 4;
    const NOTICE = 5;
    const INFO   = 6;
    const DEBUG  = 7;
    /**#@-*/

    /**
     * The format of the date used for a log entry
     *
     * @var string
     */
    protected $dateTimeFormat = 'c';

    /**
     * List of priority code => priority (short) name
     *
     * @var array
     */
    protected $priorities = array(
        self::EMERG => 'EMERG',
        self::ALERT => 'ALERT',
        self::CRIT => 'CRIT',
        self::ERR => 'ERR',
        self::WARN => 'WARN',
        self::NOTICE => 'NOTICE',
        self::INFO => 'INFO',
        self::DEBUG => 'DEBUG',
    );

    /**
     * Writers
     *
     * @var SplStack
     */
    protected $writers;

    /**
     * Writer broker
     *
     * @var WriterBroker
     */
    protected $writerBroker;

    /**
     * Constructor
     *
     * @todo support configuration (writers, dateTimeFormat, and broker)
     * @return Logger
     */
    public function __construct()
    {
        $this->writers = new SplStack();
    }

    /**
     * Destructor
     *
     * Shutdown all writers
     *
     * @return void
     */
    public function __destruct()
    {
        foreach ($this->writers as $writer) {
            try {
                $writer->shutdown();
            } catch (\Exception $e) {}
        }
    }

    /**
     * Return the format of DateTime
     *
     * @return string
     */
    public function getDateTimeFormat()
    {
        return $this->dateTimeFormat;
    }

    /**
     * Set the format of DateTime
     *
     * @param string $format
     * @return Logger
     */
    public function setDateTimeFormat($format)
    {
        $this->dateTimeFormat = (string) $format;
        return $this;
    }

    /**
     * Get writer broker
     *
     * @see Pluggable::getBroker()
     * @return Broker
     */
    public function getBroker()
    {
        if (null === $this->writerBroker) {
            $this->setBroker(new WriterBroker());
        }
        return $this->writerBroker;
    }

    /**
     * Set writer broker
     *
     * @param string|Broker $broker
     * @return Logger
     * @throws Exception\InvalidArgumentException
     */
    public function setBroker($broker)
    {
        if (is_string($broker)) {
            $broker = new $broker;
        }
        if (!$broker instanceof Broker) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Writer broker must implement Zend\Loader\Broker; received %s',
                is_object($broker) ? get_class($broker) : gettype($broker)
            ));
        }

        $this->writerBroker = $broker;
        return $this;
    }

    /**
     * Get writer instance
     *
     * @param string $name
     * @param array|null $options
     * @return Writer
     */
    public function plugin($name, array $options = null)
    {
        return $this->getBroker()->load($name, $options);
    }

    /**
     * Add a writer to a logger
     *
     * @param string|Writer $writer
     * @return Logger
     * @throws Exception\InvalidArgumentException
     */
    public function addWriter($writer)
    {
        if (is_string($writer)) {
            $writer = $this->plugin($writer);
        } elseif (!$writer instanceof Writer) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Writer must implement Zend\Log\Writer; received "%s"',
                is_object($writer) ? get_class($writer) : gettype($writer)
            ));
        }
        $this->writers->push($writer);

        return $this;
    }

    /**
     * Add a message as a log entry
     *
     * @todo implement if stack of writers is empty (exception or null writer)
     * @param int $priority
     * @param string $message
     * @param array|null $extra
     * @return Logger
     * @throws Exception\InvalidArgumentException if message can't be cast in string
     */
    public function log($priority, $message, array $extra = null)
    {
        if (is_object($message) && !method_exists($message, '__toString')) {
            throw new Exception\InvalidArgumentException(
                '$message must implement magic __toString() method'
            );
        }

        $date = new DateTime();
        $timestamp = $date->format($this->getDateTimeFormat());

        foreach ($this->writers as $writer) {
            $writer->write(array(
                'timestamp' => $timestamp,
                'priority' => (int) $priority,
                'priorityName' => $this->priorities[$priority],
                'message' => (string) $message,
                'extra' => $extra
            ));
        }

        return $this;
    }

    /**
     * @param string $message
     * @param array|null $extra
     * @return Logger
     */
    public function emerg($message, array $extra = null)
    {
        return $this->log(self::EMERG, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|null $extra
     * @return Logger
     */
    public function alert($message, array $extra = null)
    {
        return $this->log(self::ALERT, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|null $extra
     * @return Logger
     */
    public function crit($message, array $extra = null)
    {
        return $this->log(self::CRIT, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|null $extra
     * @return Logger
     */
    public function err($message, array $extra = null)
    {
        return $this->log(self::ERR, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|null $extra
     * @return Logger
     */
    public function warn($message, array $extra = null)
    {
        return $this->log(self::WARN, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|null $extra
     * @return Logger
     */
    public function notice($message, array $extra = null)
    {
        return $this->log(self::NOTICE, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|null $extra
     * @return Logger
     */
    public function info($message, array $extra = null)
    {
        return $this->log(self::INFO, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|null $extra
     * @return Logger
     */
    public function debug($message, array $extra = null)
    {
        return $this->log(self::DEBUG, $message, $extra);
    }
}