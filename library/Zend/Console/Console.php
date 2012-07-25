<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Console
 */

namespace Zend\Console;

use Zend\Console\Exception\InvalidArgumentException;

/**
 * An static, utility class for interacting with Console enviromen.
 * Declared abstract to prevent from instantiating.
 *
 * @category   Zend
 * @package    Zend_Console
 */
abstract class Console
{
    /**
     * @var \Zend\Console\Adapter
     */
    protected static $instance;

    /**
     * Instantiate (if needed) and retrieve Console\Adapter instance.
     *
     *
     * @static
     * @param null $forceAdapter       Console\Adapter class name (can be absolute namespace or relative to Adapter\)
     * @return \Zend\Console\Adapter
     */
    static public function getInstance($forceAdapter = null, $forceCharset = null)
    {
        /**
         * Create instance
         */
        if (static::$instance === null) {
            if ($forceAdapter !== null) {
                /**
                 * Use the supplied adapter class
                 */
                if (substr($forceAdapter,0,1) == '\\') {
                    $className = $forceAdapter;
                } elseif (stristr($forceAdapter,'\\')) {
                    $className = __NAMESPACE__.'\\'.ltrim($forceAdapter,'\\');
                } else {
                    $className = __NAMESPACE__.'\\Adapter\\'.$forceAdapter;
                }

                if (!class_exists($className)) {
                    throw new InvalidArgumentException('Cannot find Console adapter class '.$className);
                }
            } else {
                /**
                 * Try to detect best instance for console
                 */
                $className = static::detectBestAdapter();
            }

            /**
             * Create adapter instance
             */
            static::$instance = new $className();

            /**
             * Try to use the supplied charset class
             */
            if ($forceCharset !== null) {
                if (substr($forceCharset,0,1) == '\\') {
                    $className = $forceCharset;
                } elseif (stristr($forceAdapter,'\\')) {
                    $className = __NAMESPACE__.'\\'.ltrim($forceCharset,'\\');
                } else {
                    $className = __NAMESPACE__.'\\Charset\\'.$forceCharset;
                }

                if (!class_exists($className)) {
                    throw new InvalidArgumentException('Cannot find Charset class '.$className);
                }

                /**
                 * Set adapter charset
                 */
                static::$instance->setCharset(new $className());
            }
        }

        return static::$instance;
    }

    /**
     * Check if currently running under MS Windows
     *
     * @static
     * @return bool
     */
    static public function isWindows()
    {
        return class_exists('COM',false);
    }

    /**
     * Check if running under MS Windows Ansicon
     *
     * @static
     * @return bool
     */
    static public function isAnsicon()
    {
        return getenv('ANSICON') !== false;
    }

    /**
     * Check if running in a console environment (CLI)
     *
     * @static
     * @return bool
     */
    static public function isConsole()
    {
        return PHP_SAPI == 'cli';
    }

    /**
     * @static
     * @return \Zend\Console\Adapter|null
     */
    static public function detectBestAdapter()
    {
        // Check if we are in a console environment
        if (!static::isConsole()) {
            return null;
        }

        // Check if we're on windows
        if (static::isWindows()) {
            if (static::isAnsicon()) {
                $className = __NAMESPACE__.'\Adapter\WindowsAnsicon';
            } else {
                $className = __NAMESPACE__.'\Adapter\Windows';
            }

            return new $className();
        }

        // Default is a Posix console
        $className = __NAMESPACE__.'\Adapter\Posix';
        return new $className();
    }

    /**
     * Pass-thru static call to current Console\Adapter instance.
     *
     * @static
     * @param $funcName
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($funcName, $arguments)
    {
        $instance = static::getInstance();
        return call_user_func_array(array($instance,$funcName),$arguments);
    }
}
