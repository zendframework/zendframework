<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace Zend\Session\Exception;

/**
 * Zend_Session_Exception
 *
 * @category   Zend
 * @package    Zend_Session
 */
interface ExceptionInterface
{
    /**
     * sessionStartError
     *
     * @see http://framework.zend.com/issues/browse/ZF-1325
     * @var string PHP Error Message
     */
    //static public $sessionStartError = null;

    /**
     * handleSessionStartError() - interface for set_error_handler()
     *
     * @see    http://framework.zend.com/issues/browse/ZF-1325
     * @param  int    $errno
     * @param  string $errstr
     * @return void
     */
//    static public function handleSessionStartError($errno, $errstr, $errfile, $errline, $errcontext)
//    {
//        self::$sessionStartError = $errfile . '(Line:' . $errline . '): Error #' . $errno . ' ' . $errstr . ' ' . $errcontext;
//    }

    /**
     * handleSilentWriteClose() - interface for set_error_handler()
     *
     * @see    http://framework.zend.com/issues/browse/ZF-1325
     * @param  int    $errno
     * @param  string $errstr
     * @return void
     */
//    static public function handleSilentWriteClose($errno, $errstr, $errfile, $errline, $errcontext)
//    {
//        self::$sessionStartError .= PHP_EOL . $errfile . '(Line:' . $errline . '): Error #' . $errno . ' ' . $errstr . ' ' . $errcontext;
//    }
}

