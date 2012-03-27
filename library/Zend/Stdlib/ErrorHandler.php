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
 * @package    Zend_Stdlib
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Stdlib;

use ErrorException;

/**
 * ErrorHandler that can be used to catch internal PHP errors
 * and convert to a ErrorException instance.
 *
 * @category   Zend
 * @package    Zend_Stdlib
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class ErrorHandler
{
    /**
     * Flag to mark started
     *
     * @var boolean
     */
    protected static $started = false;

    /**
     * All errors as one instance of ErrorException
     * using the previous exception support.
     *
     * @var null|ErrorException
     */
    protected static $errorException = null;

    /**
     * If the error handler has been started.
     *
     * @return boolean
     */
    public static function started()
    {
        return static::$started;
    }

    /**
     * Starting the error handler
     *
     * @param int $errorLevel
     * @throws Exception\LogicException If already started
     */
    public static function start($errorLevel = \E_WARNING)
    {
        if (static::started() === true) {
            throw new Exception\LogicException('ErrorHandler already started');
        }

        static::$started        = true;
        static::$errorException = null;

        set_error_handler(array(get_called_class(), 'addError'), $errorLevel);
    }

    /**
     * Stopping the error handler
     *
     * @param  boolean $throw Throw the ErrorException if any
     * @return null|ErrorException
     * @throws Exception\LogicException If not started before
     * @throws ErrorException If an error has been catched and $throw is true
     */
    public static function stop($throw = false)
    {
        if (static::started() === false) {
            throw new Exception\LogicException('ErrorHandler not started');
        }

        $errorException = static::$errorException;

        static::$started        = false;
        static::$errorException = null;
        restore_error_handler();

        if ($errorException && $throw) {
            throw $errorException;
        }

        return $errorException;
    }

    /**
     * Add an error to the stack.
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     * @return void
     */
    public static function addError($errno, $errstr = '', $errfile = '', $errline = 0)
    {
        static::$errorException = new ErrorException($errstr, 0, $errno, $errfile, $errline, static::$errorException);
    }
}
