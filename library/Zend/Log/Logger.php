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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Log;

use DateTime,
    Zend\Stdlib\SplPriorityQueue,
    Traversable,
    Zend\Loader\Broker,
    Zend\Loader\Pluggable,
    Zend\Stdlib\ArrayUtils;

/**
 * Logging messages with a stack of backends
 *
 * @category   Zend
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Logger implements LoggableInterface, Pluggable
{
    /**
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

    /**
     * The format of the date used for a log entry (ISO 8601 date)
     * 
     * @see http://nl3.php.net/manual/en/function.date.php
     * @var string
     */
    protected $dateTimeFormat = 'c';

    /**
     * List of priority code => priority (short) name
     *
     * @var array
     */
    protected $priorities = array(
        self::EMERG  => 'EMERG',
        self::ALERT  => 'ALERT',
        self::CRIT   => 'CRIT',
        self::ERR    => 'ERR',
        self::WARN   => 'WARN',
        self::NOTICE => 'NOTICE',
        self::INFO   => 'INFO',
        self::DEBUG  => 'DEBUG',
    );

    /**
     * Writers
     *
     * @var SplPriorityQueue
     */
    protected $writers;

    /**
     * Writer broker
     *
     * @var WriterBroker
     */
    protected $writerBroker;

    /**
     * Registered error handler
     * 
     * @var boolean
     */
    protected static $registeredErrorHandler = false;
       
    /**
     * Registered exception handler
     * 
     * @var boolean
     */
    protected static $registeredExceptionHandler = false;
    
    /**
     * Constructor
     *
     * @todo support configuration (writers, dateTimeFormat, and broker)
     * @return Logger
     */
    public function __construct()
    {
        $this->writers = new SplPriorityQueue();
    }

    /**
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
     * @see    http://nl3.php.net/manual/en/function.date.php
     * @param  string $format
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
     * @param  string|Writer $writer
     * @param  int $priority
     * @return Logger
     * @throws Exception\InvalidArgumentException
     */
    public function addWriter($writer, $priority=1)
    {
        if (is_string($writer)) {
            $writer = $this->plugin($writer);
        } elseif (!$writer instanceof Writer\WriterInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Writer must implement Zend\Log\Writer; received "%s"',
                is_object($writer) ? get_class($writer) : gettype($writer)
            ));
        }
        
        $this->writers->insert($writer, $priority);

        return $this;
    }

    /**
     * Get writers
     * 
     * @return SplPriorityQueue 
     */
    public function getWriters()
    {
        return $this->writers;
    }
    /**
     * Set the writers
     * 
     * @param  SplPriorityQueue $writers 
     * @throws Exception\InvalidArgumentException
     * @return Logger
     */
    public function setWriters($writers)
    {
        if (!$writers instanceof SplPriorityQueue) {
            throw new Exception\InvalidArgumentException('Writers must be a SplPriorityQueue of Zend\Log\Writer');
        }
        foreach ($writers->toArray() as $writer) {
            if (!$writer instanceof Writer\WriterInterface) {
                throw new Exception\InvalidArgumentException('Writers must be a SplPriorityQueue of Zend\Log\Writer');
            }
        }
        $this->writers = $writers;
        return $this;
    }
    /**
     * Add a message as a log entry
     *
     * @param  int $priority
     * @param  mixed $message
     * @param  array|Traversable $extra
     * @return Logger
     * @throws Exception\InvalidArgumentException if message can't be cast to string
     * @throws Exception\InvalidArgumentException if extra can't be iterated over
     */
    public function log($priority, $message, $extra = array())
    {
        if (!is_int($priority) || ($priority<0) || ($priority>=count($this->priorities))) {
            throw new Exception\InvalidArgumentException(sprintf(
                '$priority must be an integer > 0 and < %d; received %s',
                count($this->priorities),
                var_export($priority, 1)
            ));
        }
        if (is_object($message) && !method_exists($message, '__toString')) {
            throw new Exception\InvalidArgumentException(
                '$message must implement magic __toString() method'
            );
        }

        if (!is_array($extra) && !$extra instanceof Traversable) {
            throw new Exception\InvalidArgumentException(
                '$extra must be an array or implement Traversable'
            );
        } elseif ($extra instanceof Traversable) {
            $extra = ArrayUtils::iteratorToArray($extra);
        }

        if ($this->writers->count() === 0) {
            throw new Exception\RuntimeException('No log writer specified');
        }
        
        $date = new DateTime();
        $timestamp = $date->format($this->getDateTimeFormat());

        if (is_array($message)) {
            $message = var_export($message, true);
        }
               
        foreach ($this->writers->toArray() as $writer) {
            $writer->write(array(
                'timestamp'    => $timestamp,
                'priority'     => (int) $priority,
                'priorityName' => $this->priorities[$priority],
                'message'      => (string) $message,
                'extra'        => $extra
            ));
        }

        return $this;
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function emerg($message, $extra = array())
    {
        return $this->log(self::EMERG, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function alert($message, $extra = array())
    {
        return $this->log(self::ALERT, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function crit($message, $extra = array())
    {
        return $this->log(self::CRIT, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function err($message, $extra = array())
    {
        return $this->log(self::ERR, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function warn($message, $extra = array())
    {
        return $this->log(self::WARN, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function notice($message, $extra = array())
    {
        return $this->log(self::NOTICE, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function info($message, $extra = array())
    {
        return $this->log(self::INFO, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function debug($message, $extra = array())
    {
        return $this->log(self::DEBUG, $message, $extra);
    }
    
    /**
     * Register logging system as an error handler to log PHP errors
     *
     * @link http://www.php.net/manual/en/function.set-error-handler.php 
     *
     * @param  Logger $logger
     * @return boolean
     */
    public static function registerErrorHandler(Logger $logger)
    {
        // Only register once per instance
        if (self::$registeredErrorHandler) {
            return false;
        }

        if ($logger === null) {
            throw new Exception\InvalidArgumentException('Invalid Logger specified');
        }
        
        $errorHandlerMap = array(
            E_NOTICE            => self::NOTICE,
            E_USER_NOTICE       => self::NOTICE,
            E_WARNING           => self::WARN,
            E_CORE_WARNING      => self::WARN,
            E_USER_WARNING      => self::WARN,
            E_ERROR             => self::ERR,
            E_USER_ERROR        => self::ERR,
            E_CORE_ERROR        => self::ERR,
            E_RECOVERABLE_ERROR => self::ERR,
            E_STRICT            => self::DEBUG,
            E_DEPRECATED        => self::DEBUG,
            E_USER_DEPRECATED   => self::DEBUG
        );
        
        set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) use ($errorHandlerMap, $logger) {
            $errorLevel = error_reporting();

            if ($errorLevel && $errno) {
                if (isset($errorHandlerMap[$errno])) {
                    $priority = $errorHandlerMap[$errno];
                } else {
                    $priority = Logger::INFO;
                }
                $logger->log($priority, $errstr, array('errno'=>$errno, 'file'=>$errfile, 'line'=>$errline, 'context'=>$errcontext));
            }
        });
        self::$registeredErrorHandler = true;
        return true;
    }
    /**
     * Unregister error handler
     * 
     */
    public static function unregisterErrorHandler()
    {
        restore_error_handler();
        self::$registeredErrorHandler = false;
    }
    /**
     * Register logging system as an exception handler to log PHP exceptions
     * 
     * @link http://www.php.net/manual/en/function.set-exception-handler.php
     * 
     * @param Logger $logger
     * @return type 
     */
    public static function registerExceptionHandler(Logger $logger)
    {
        // Only register once per instance
        if (self::$registeredExceptionHandler) {
            return false;
        }
        
        if ($logger === null) {
            throw new Exception\InvalidArgumentException('Invalid Logger specified');
        }
        
        set_exception_handler(function ($exception) use ($logger){
            $extra = array ('file'  => $exception->getFile(), 
                            'line'  => $exception->getLine(),
                            'trace' => $exception->getTrace());  
            if (isset($exception->xdebug_message)) {
                $extra['xdebug'] = $exception->xdebug_message;
            }
            $logger->log(Logger::ERR, $exception->getMessage(), $extra);
        });
        self::$registeredExceptionHandler = true;
        return true;
    }
    
    /**
     * Unregister exception handler
     */
    public static function unregisterExceptionHandler()
    {
        restore_exception_handler();
        self::$registeredExceptionHandler = false;
    }
}
