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
 * @package    Zend_Service
 * @subpackage LiveDocx
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Date **/
require_once 'Zend/Date.php';

/**
 * @category   Demos
 * @package    Demos_Zend_Service
 * @subpackage LiveDocx
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Demos_Zend_Service_LiveDocx_Helper
{
    /**
     * Name of configuration file stored in /demos/Zend/Service/LiveDocx/
     */
    const CONFIGURATION_FILE = 'configuration.php';
        
    /**
     * Line length in characters (used to wrap long lines)
     */
    const LINE_LENGTH = 80;
    
    /**
     * Default locale
     */
    const LOCALE = 'en_US';
    
    /**
     * Return true, if configuration file exists and constants
     * DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME and
     * DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD have been set.
     *  
     * @return boolean
     */
    public static function credentialsAvailable()
    {
        $ret = false;
        
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . self::CONFIGURATION_FILE;
        if (is_file($filename) && is_readable($filename)) {
            include_once $filename;
            if (defined('DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME') &&
                defined('DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD') &&
                false !== DEMOS_ZEND_SERVICE_LIVEDOCX_USERNAME  &&
                false !== DEMOS_ZEND_SERVICE_LIVEDOCX_PASSWORD ) {
                    $ret = true;
                }
        }
        
        return $ret;
    } 
    
    /**
     * Return instructions on how to register to use LiveDocx service and enter
     * username and password into configuration file.
     * 
     * @return string
     */
    public static function credentialsHowTo()
    {
        $ret  = PHP_EOL;
        $ret .= sprintf('ERROR: LIVEDOCX USERNAME AND PASSWORD HAVE NOT BEEN SET.%s', PHP_EOL);
        $ret .= PHP_EOL;
        $ret .= sprintf('1. Using a web browser, register to use the LiveDocx service at:%s', PHP_EOL);
        $ret .= sprintf('   https://www.livedocx.com/user/account_registration.aspx%s', PHP_EOL);
        $ret .= sprintf('   (takes less than 1 minute).%s', PHP_EOL);
        $ret .= PHP_EOL;
        $ret .= sprintf('2. Change directory into:%s', PHP_EOL);
        $ret .= sprintf('   %s%s', dirname(__FILE__), PHP_EOL);
        $ret .= PHP_EOL;
        $ret .= sprintf('3. Copy %s.dist to %s.%s', self::CONFIGURATION_FILE, self::CONFIGURATION_FILE, PHP_EOL);
        $ret .= PHP_EOL;
        $ret .= sprintf('4. Open %s in a text editor and enter the username and password%s', self::CONFIGURATION_FILE, PHP_EOL);
        $ret .= sprintf('   you obtained in step 1 (lines 43 and 44).%s', PHP_EOL);
        $ret .= PHP_EOL;
        $ret .= sprintf('5. Save and close configuration.php.%s', PHP_EOL);
        $ret .= PHP_EOL;
        $ret .= sprintf('Congratulations!%s', PHP_EOL);
        $ret .= PHP_EOL;
        $ret .= sprintf('You have now set up the Zend_Service_LiveDocx demo applications.%s', PHP_EOL);
        $ret .= PHP_EOL;
        
        return $ret;
    }
    
    /**
     * Decorator to format return value of list methods
     *
     * @param array $result
     * @return string
     */
    public static function listDecorator($result)
    {
        $ret = '';
        
        $date = new Zend_Date();
        
        if (count($result) > 0) {
            foreach ($result as $record) {
                $date->set($record['createTime']);
                $createTimeFormatted = $date->get(Zend_Date::RFC_1123);
                $date->set($record['modifyTime']);
                $modifyTimeFormatted = $date->get(Zend_Date::RFC_1123);
                $ret .= sprintf('         Filename  : %s%s', $record['filename'], PHP_EOL);
                $ret .= sprintf('         File Size : %d b%s', $record['fileSize'], PHP_EOL);
                $ret .= sprintf('     Creation Time : %d (%s)%s', $record['createTime'], $createTimeFormatted, PHP_EOL);
                $ret .= sprintf('Last Modified Time : %d (%s)%s', $record['modifyTime'], $modifyTimeFormatted, PHP_EOL);
                $ret .= PHP_EOL;
            }
        }
        
        unset($date);
        
        return $ret;
    }
    
    /**
     * Decorator to format array
     *
     * @param array $result
     * @return string
     */
    public static function arrayDecorator($result)
    {
        $ret = '';
        $count = count($result);
        if ($count > 0) {
            for ($i = 0; $i < $count; $i ++) {
                $ret .= $result[$i];
                if ($count === ($i + 1)) {
                    $ret .= '.';
                } elseif ($count === ($i + 2)) {
                    $ret .= ' & ';
                } else {
                    $ret .= ', ';
                }
            }
        } else {
            $ret .= 'none';
        }
        return $ret;
    }
    
    /**
     * Wrap the length of long lines
     * 
     * @param string $str
     * @return string
     */
    public static function wrapLine($str)
    {
        return wordwrap($str, self::LINE_LENGTH);
    }
}