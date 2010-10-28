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
 * @package    Zend_Loader
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend;

/**
 * Static methods for loading classes and files.
 *
 * @category   Zend
 * @package    Zend_Loader
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Loader
{
    /**
     * Loads a PHP file.  This is a wrapper for PHP's include() function.
     *
     * $filename must be the complete filename, including any
     * extension such as ".php".  Note that a security check is performed that
     * does not permit extended characters in the filename.  This method is
     * intended for loading Zend Framework files.
     *
     * If $dirs is a string or an array, it will search the directories
     * in the order supplied, and attempt to load the first matching file.
     *
     * If the file was not found in the $dirs, or if no $dirs were specified,
     * it will attempt to load it from PHP's include_path.
     *
     * If $once is TRUE, it will use include_once() instead of include().
     *
     * @param  string        $filename
     * @param  string|array  $dirs - OPTIONAL either a path or array of paths
     *                       to search.
     * @param  boolean       $once
     * @return boolean
     * @throws Zend\Loader\Exception\SecurityException
     */
    public static function loadFile($filename, $dirs = null, $once = false)
    {
        self::_securityCheck($filename);

        /**
         * Search in provided directories, as well as include_path
         */
        $incPath = false;
        if (!empty($dirs) && (is_array($dirs) || is_string($dirs))) {
            if (is_array($dirs)) {
                $dirs = implode(PATH_SEPARATOR, $dirs);
            }
            $incPath = get_include_path();
            set_include_path($dirs . PATH_SEPARATOR . $incPath);
        }

        /**
         * Try finding for the plain filename in the include_path.
         */
        if ($once) {
            include_once $filename;
        } else {
            include $filename;
        }

        /**
         * If searching in directories, reset include_path
         */
        if ($incPath) {
            set_include_path($incPath);
        }

        return true;
    }

    /**
     * Returns TRUE if the $filename is readable, or FALSE otherwise.
     * This function uses the PHP include_path, where PHP's is_readable()
     * does not.
     *
     * Note from ZF-2900:
     * If you use custom error handler, please check whether return value
     *  from error_reporting() is zero or not.
     * At mark of fopen() can not suppress warning if the handler is used.
     *
     * @param string   $filename
     * @return boolean
     */
    public static function isReadable($filename)
    {
        if (is_readable($filename)) {
            // Return early if the filename is readable without needing the 
            // include_path
            return true;
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'
            && preg_match('/^[a-z]:/i', $filename)
        ) {
            // If on windows, and path provided is clearly an absolute path, 
            // return false immediately
            return false;
        }

        foreach (self::explodeIncludePath() as $path) {
            if ($path == '.') {
                if (is_readable($filename)) {
                    return true;
                }
                continue;
            }
            $file = $path . '/' . $filename;
            if (is_readable($file)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Explode an include path into an array
     *
     * If no path provided, uses current include_path. Works around issues that
     * occur when the path includes stream schemas.
     * 
     * @param  string|null $path 
     * @return array
     */
    public static function explodeIncludePath($path = null)
    {
        if (null === $path) {
            $path = get_include_path();
        }

        if (PATH_SEPARATOR == ':') {
            // On *nix systems, include_paths which include paths with a stream 
            // schema cannot be safely explode'd, so we have to be a bit more
            // intelligent in the approach.
            $paths = preg_split('#:(?!//)#', $path);
        } else {
            $paths = explode(PATH_SEPARATOR, $path);
        }
        return $paths;
    }

    /**
     * Ensure that filename does not contain exploits
     *
     * @param  string $filename
     * @return void
     * @throws Zend\Loader\Exception\SecurityException
     */
    protected static function _securityCheck($filename)
    {
        /**
         * Security check
         */
        if (preg_match('/[^a-z0-9\\/\\\\_.:-]/i', $filename)) {
            require_once __DIR__ . '/Loader/Exception/SecurityException.php';
            throw new Loader\Exception\SecurityException('Illegal character in filename');
        }
    }

    /**
     * Attempt to include() the file.
     *
     * include() is not prefixed with the @ operator because if
     * the file is loaded and contains a parse error, execution
     * will halt silently and this is difficult to debug.
     *
     * Always set display_errors = Off on production servers!
     *
     * @param  string  $filespec
     * @param  boolean $once
     * @return boolean
     * @deprecated Since 1.5.0; use loadFile() instead
     */
    protected static function _includeFile($filespec, $once = false)
    {
        if ($once) {
            return include_once $filespec;
        } else {
            return include $filespec ;
        }
    }
}
