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
     * LiveDocx demonstration username
     * 
     * IMPORTANT: These login credentials may be used for demonstration purposes only.
     *            Getting your own username and password takes less than 1 minute.
     *            Goto http://is.gd/5dK5A to sign up.
     */
    const USERNAME = 'zfdemos';
    
    /**
     * LiveDocx demonstration password
     *
     * IMPORTANT: These login credentials may be used for demonstration purposes only.
     *            Getting your own username and password takes less than 1 minute.
     *            Goto http://is.gd/5dK5A to sign up.
     */    
    const PASSWORD = 'fkj3487o4zf35';
    
    /**
     * Line length in characters (used to wrap long lines)
     */
    const LINE_LENGTH = 80;
    
    /**
     * Default Locale
     */
    const LOCALE = 'en_US';
    
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