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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Log;
use Zend\Config\Config;

/**
 * @uses       \Zend\Loader
 * @uses       \Zend\Log\Exception
 * @uses       \Zend\Log\Filter\Priority
 * @category   Zend
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Logger implements Factory
{
    const EMERG   = 0;  // Emergency: system is unusable
    const ALERT   = 1;  // Alert: action must be taken immediately
    const CRIT    = 2;  // Critical: critical conditions
    const ERR     = 3;  // Error: error conditions
    const WARN    = 4;  // Warning: warning conditions
    const NOTICE  = 5;  // Notice: normal but significant condition
    const INFO    = 6;  // Informational: informational messages
    const DEBUG   = 7;  // Debug: debug messages

    /**
     * @var array of priorities where the keys are the
     * priority numbers and the values are the priority names
     */
    protected $_priorities = array();

    /**
     * @var array of \Zend\Log\Writer\AbstractWriter
     */
    protected $_writers = array();

    /**
     * @var array of \Zend\Log\Filter\FilterInterface
     */
    protected $_filters = array();

    /**
     * @var array of extra log event
     */
    protected $_extras = array();

    /**
     *
     * @var string
     */
    protected $_defaultWriterNamespace = 'Zend\Log\Writer';

    /**
     *
     * @var string
     */
    protected $_defaultFilterNamespace = 'Zend\Log\Filter';

    /**
     *
     * @var callback
     */
    protected $_origErrorHandler       = null;

    /**
     *
     * @var boolean
     */
    protected $_registeredErrorHandler = false;

    /**
     *
     * @var array
     */
    protected $_errorHandlerMap        = false;

    /**
     *
     * @var string
     */
    protected $_timestampFormat        = 'c';

    /**
     * Class constructor.  Create a new logger
     *
     * @param \Zend\Log\Writer\AbstractWriter|null  $writer  default writer
     */
    public function __construct(Writer $writer = null)
    {
        $r = new \ReflectionClass($this);
        $this->_priorities = array_flip($r->getConstants());

        if ($writer !== null) {
            $this->addWriter($writer);
        }
    }

    /**
     * Factory to construct the logger and one or more writers
     * based on the configuration array
     *
     * @param  array|\Zend\Config\Config Array or instance of \Zend\Config\Config
     * @return \Zend\Log\Logger
     */
    static public function factory($config = array())
    {
        if ($config instanceof Config) {
            $config = $config->toArray();
        }

        if (!is_array($config) || empty($config)) {
            throw new Exception('Configuration must be an array or instance of Zend\\Config\\Config');
        }

        $log = new self;

        if (!is_array(current($config))) {
            $log->addWriter(current($config));
        } else {
            foreach($config as $writer) {
                $log->addWriter($writer);
            }
        }

        return $log;
    }


    /**
     * Construct a writer object based on a configuration array
     *
     * @param  array $spec config array with writer spec
     * @return \Zend\Log\Writer\AbstractWriter
     */
    protected function _constructWriterFromConfig($config)
    {
        $writer = $this->_constructFromConfig('writer', $config, $this->_defaultWriterNamespace);

        if (!$writer instanceof Writer) {
            $writerName = is_object($writer)
                        ? get_class($writer)
                        : 'The specified writer';
            throw new Exception("{$writerName} does not extend Zend\\Log\\Writer!");
        }

        if (isset($config['filterName'])) {
            $filter = $this->_constructFilterFromConfig($config);
            $writer->addFilter($filter);
        }

        return $writer;
    }

    /**
     * Construct filter object from configuration array or Zend_Config object
     *
     * @param  array|\Zend\Config\Config $config \Zend\Config\Config or Array
     * @return \Zend\Log\Filter
     */
    protected function _constructFilterFromConfig($config)
    {
        $filter = $this->_constructFromConfig('filter', $config, $this->_defaultFilterNamespace);

        if (!$filter instanceof Filter) {
             $filterName = is_object($filter)
                         ? get_class($filter)
                         : 'The specified filter';
            throw new Exception("{$filterName} does not implement Zend\\Log\\Filter");
        }

        return $filter;
    }

    /**
     * Construct a filter or writer from config
     *
     * @param string $type 'writer' of 'filter'
     * @param mixed $config \Zend\Config\Config or Array
     * @param string $namespace
     * @return object
     */
    protected function _constructFromConfig($type, $config, $namespace)
    {
        if ($config instanceof Config) {
            $config = $config->toArray();
        }

        if (!is_array($config) || empty($config)) {
            throw new Exception(
                'Configuration must be an array or instance of Zend\\Config\\Config'
            );
        }

        $params    = isset($config[ $type .'Params' ]) ? $config[ $type .'Params' ] : array();
        $className = $this->getClassName($config, $type, $namespace);
        if (!class_exists($className)) {
            \Zend\Loader::loadClass($className);
        }

        $reflection = new \ReflectionClass($className);
        if (!$reflection->implementsInterface('Zend\Log\Factory')) {
            throw new Exception(
                'Driver does not implement Zend\Log\Factory and can not be constructed from config.'
            );
        }

        return $className::factory($params);
    }

    /**
     * Get the writer or filter full classname
     *
     * @param array $config
     * @param string $type filter|writer
     * @param string $defaultNamespace
     * @return string full classname
     */
    protected function getClassName($config, $type, $defaultNamespace)
    {
        if (!isset($config[ $type . 'Name' ])) {
            throw new Exception("Specify {$type}Name in the configuration array");
        }
        $className = $config[ $type . 'Name' ];

        $namespace = $defaultNamespace;
        if (isset($config[ $type . 'Namespace' ])) {
            $namespace = $config[ $type . 'Namespace' ];
        }

        $fullClassName = $namespace . '\\' . $className;
        return $fullClassName;
    }

    /**
     * Packs message and priority into Event array
     *
     * @param  string   $message   Message to log
     * @param  integer  $priority  Priority of message
     * @return array Event array
     **/
    protected function _packEvent($message, $priority)
    {
        return array_merge(array(
            'timestamp'    => date($this->_timestampFormat),
            'message'      => $message,
            'priority'     => $priority,
            'priorityName' => $this->_priorities[$priority]
            ),
            $this->_extras
        );
    }

    /**
     * Class destructor.  Shutdown log writers
     *
     * @return void
     */
    public function __destruct()
    {
        foreach($this->_writers as $writer) {
            $writer->shutdown();
        }
    }

    /**
     * Undefined method handler allows a shortcut:
     *   $log->priorityName('message')
     *     instead of
     *   $log->log('message', \Zend\Log\Logger::PRIORITY_NAME)
     *
     * @param  string  $method  priority name
     * @param  string  $params  message to log
     * @return void
     * @throws \Zend\Log\Exception
     */
    public function __call($method, $params)
    {
        $priority = strtoupper($method);
        if (($priority = array_search($priority, $this->_priorities)) !== false) {
            switch (count($params)) {
                case 0:
                    throw new Exception('Missing log message');
                case 1:
                    $message = array_shift($params);
                    $extras = null;
                    break;
                default:
                    $message = array_shift($params);
                    $extras  = array_shift($params);
                    break;
            }
            $this->log($message, $priority, $extras);
        } else {
            throw new Exception('Bad log priority');
        }
    }

    /**
     * Log a message at a priority
     *
     * @param  string   $message   Message to log
     * @param  integer  $priority  Priority of message
     * @param  mixed    $extras    Extra information to log in event
     * @return void
     * @throws \Zend\Log\Exception
     */
    public function log($message, $priority, $extras = null)
    {
        // sanity checks
        if (empty($this->_writers)) {
            throw new Exception('No writers were added');
        }

        if (! isset($this->_priorities[$priority])) {
            throw new Exception('Bad log priority');
        }

        // pack into event required by filters and writers
        $event = $this->_packEvent($message, $priority);

        // Check to see if any extra information was passed
        if (!empty($extras)) {
            $info = array();
            if (is_array($extras)) {
                foreach ($extras as $key => $value) {
                    if (is_string($key)) {
                        $event[$key] = $value;
                    } else {
                        $info[] = $value;
                    }
                }
            } else {
                $info = $extras;
            }
            if (!empty($info)) {
                $event['info'] = $info;
            }
        }

        // abort if rejected by the global filters
        foreach ($this->_filters as $filter) {
            if (! $filter->accept($event)) {
                return;
            }
        }

        // send to each writer
        foreach ($this->_writers as $writer) {
            $writer->write($event);
        }
    }

    /**
     * Add a custom priority
     *
     * @param  string   $name      Name of priority
     * @param  integer  $priority  Numeric priority
     * @throws Zend_Log_InvalidArgumentException
     */
    public function addPriority($name, $priority)
    {
        // Priority names must be uppercase for predictability.
        $name = strtoupper($name);

        if (isset($this->_priorities[$priority])
            || false !== array_search($name, $this->_priorities)) {
            throw new Exception('Existing priorities cannot be overwritten');
        }

        $this->_priorities[$priority] = $name;
        return $this;
    }

    /**
     * Add a filter that will be applied before all log writers.
     * Before a message will be received by any of the writers, it
     * must be accepted by all filters added with this method.
     *
     * @param  int|\Zend\Log\Filter\FilterInterface $filter
     * @return void
     */
    public function addFilter($filter)
    {
        if (is_integer($filter)) {
            $filter = new Filter\Priority($filter);

        } elseif ($filter instanceof Config || is_array($filter)) {
            $filter = $this->_constructFilterFromConfig($filter);

        } elseif(! $filter instanceof Filter) {
            throw new Exception('Invalid filter provided');
        }

        $this->_filters[] = $filter;
        return $this;
    }

    /**
     * Add a writer.  A writer is responsible for taking a log
     * message and writing it out to storage.
     *
     * @param  mixed $writer \Zend\Log\Writer\AbstractWriter or Config array
     * @return void
     */
    public function addWriter($writer)
    {
        if (is_array($writer) || $writer instanceof Config) {
            $writer = $this->_constructWriterFromConfig($writer);
        }

        if (!$writer instanceof Writer) {
            throw new Exception(
                'Writer must be an instance of Zend\\Log\\Writer'
                . ' or you should pass a configuration array'
            );
        }

        $this->_writers[] = $writer;
        return $this;
    }

    /**
     * Set an extra item to pass to the log writers.
     *
     * @param  $name    Name of the field
     * @param  $value   Value of the field
     * @return void
     */
    public function setEventItem($name, $value)
    {
        $this->_extras = array_merge($this->_extras, array($name => $value));
        return $this;
    }

    /**
     * Register Logging system as an error handler to log php errors
     * Note: it still calls the original error handler if set_error_handler is able to return it.
     *
     * Errors will be mapped as:
     *   E_NOTICE, E_USER_NOTICE => NOTICE
     *   E_WARNING, E_CORE_WARNING, E_USER_WARNING => WARN
     *   E_ERROR, E_USER_ERROR, E_CORE_ERROR, E_RECOVERABLE_ERROR => ERR
     *   E_DEPRECATED, E_STRICT, E_USER_DEPRECATED => DEBUG
     *   (unknown/other) => INFO
     *
     * @link http://www.php.net/manual/en/function.set-error-handler.php Custom error handler
     *
     * @return \Zend\Log\Logger
     */
    public function registerErrorHandler()
    {
        // Only register once.  Avoids loop issues if it gets registered twice.
        if ($this->_registeredErrorHandler) {
        	return $this;
        }

        $this->_origErrorHandler = set_error_handler(array($this, 'errorHandler'));

        // Contruct a default map of phpErrors to Zend_Log priorities.
        // Some of the errors are uncatchable, but are included for completeness
        $this->_errorHandlerMap = array(
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
        );
        
        $this->_errorHandlerMap['E_DEPRECATED'] = self::DEBUG;
        $this->_errorHandlerMap['E_USER_DEPRECATED'] = self::DEBUG;
        $this->_registeredErrorHandler = true;
        return $this;
    }

    /**
     * Error Handler will convert error into log message, and then call the original error handler
     *
     * @link http://www.php.net/manual/en/function.set-error-handler.php Custom error handler
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     * @return boolean
     */
    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $errorLevel = error_reporting();

        if ($errorLevel && $errno) {
            if (isset($this->_errorHandlerMap[$errno])) {
                $priority = $this->_errorHandlerMap[$errno];
            } else {
                $priority = self::INFO;
            }
            $this->log($errstr, $priority, array('errno'=>$errno, 'file'=>$errfile, 'line'=>$errline, 'context'=>$errcontext));
        }

        if ($this->_origErrorHandler !== null) {
            return call_user_func($this->_origErrorHandler, $errno, $errstr, $errfile, $errline, $errcontext);
        }
        return false;
    }

    /**
     * Set timestamp format for log entries.
     *
     * @param string $format
     * @return Zend\Log\Logger
     */
    public function setTimestampFormat($format)
    {
        $this->_timestampFormat = $format;
        return $this;
    }

    /**
     * Get timestamp format used for log entries.
     *
     * @return string
     */
    public function getTimestampFormat()
    {
        return $this->_timestampFormat;
    }
}
