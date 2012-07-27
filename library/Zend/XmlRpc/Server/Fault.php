<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_XmlRpc
 */

namespace Zend\XmlRpc\Server;

/**
 * XMLRPC Server Faults
 *
 * Encapsulates an exception for use as an XMLRPC fault response. Valid
 * exception classes that may be used for generating the fault code and fault
 * string can be attached using {@link attachFaultException()}; all others use a
 * generic '404 Unknown error' response.
 *
 * You may also attach fault observers, which would allow you to monitor
 * particular fault cases; this is done via {@link attachObserver()}. Observers
 * need only implement a static 'observe' method.
 *
 * To allow method chaining, you may use the {@link getInstance()} factory
 * to instantiate a Zend_XmlRpc_Server_Fault.
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Server
 */
class Fault extends \Zend\XmlRpc\Fault
{
    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @var array Array of exception classes that may define xmlrpc faults
     */
    protected static $faultExceptionClasses = array('Zend\\XmlRpc\\Server\\Exception\\ExceptionInterface' => true);

    /**
     * @var array Array of fault observers
     */
    protected static $observers = array();

    /**
     * Constructor
     *
     * @param  \Exception $e
     * @return Fault
     */
    public function __construct(\Exception $e)
    {
        $this->exception = $e;
        $code             = 404;
        $message          = 'Unknown error';
        $exceptionClass   = get_class($e);

        foreach (array_keys(self::$faultExceptionClasses) as $class) {
            if ($e instanceof $class) {
                $code    = $e->getCode();
                $message = $e->getMessage();
                break;
            }
        }

        parent::__construct($code, $message);

        // Notify exception observers, if present
        if (!empty(self::$observers)) {
            foreach (array_keys(self::$observers) as $observer) {
                $observer::observe($this);
            }
        }
    }

    /**
     * Return Zend\XmlRpc\Server\Fault instance
     *
     * @param \Exception $e
     * @return Fault
     */
    public static function getInstance(\Exception $e)
    {
        return new self($e);
    }

    /**
     * Attach valid exceptions that can be used to define xmlrpc faults
     *
     * @param string|array $classes Class name or array of class names
     * @return void
     */
    public static function attachFaultException($classes)
    {
        if (!is_array($classes)) {
            $classes = (array) $classes;
        }

        foreach ($classes as $class) {
            if (is_string($class) && class_exists($class)) {
                self::$faultExceptionClasses[$class] = true;
            }
        }
    }

    /**
     * Detach fault exception classes
     *
     * @param string|array $classes Class name or array of class names
     * @return void
     */
    public static function detachFaultException($classes)
    {
        if (!is_array($classes)) {
            $classes = (array) $classes;
        }

        foreach ($classes as $class) {
            if (is_string($class) && isset(self::$faultExceptionClasses[$class])) {
                unset(self::$faultExceptionClasses[$class]);
            }
        }
    }

    /**
     * Attach an observer class
     *
     * Allows observation of xmlrpc server faults, thus allowing logging or mail
     * notification of fault responses on the xmlrpc server.
     *
     * Expects a valid class name; that class must have a public static method
     * 'observe' that accepts an exception as its sole argument.
     *
     * @param string $class
     * @return boolean
     */
    public static function attachObserver($class)
    {
        if (!is_string($class)
            || !class_exists($class)
            || !is_callable(array($class, 'observe')))
        {
            return false;
        }

        if (!isset(self::$observers[$class])) {
            self::$observers[$class] = true;
        }

        return true;
    }

    /**
     * Detach an observer
     *
     * @param string $class
     * @return boolean
     */
    public static function detachObserver($class)
    {
        if (!isset(self::$observers[$class])) {
            return false;
        }

        unset(self::$observers[$class]);
        return true;
    }

    /**
     * Retrieve the exception
     *
     * @access public
     * @return Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
