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

/**
 * Wrapper for glob with fallback if GLOB_BRACE is not available.
 *
 * @category   Zend
 * @package    Zend_Stdlib
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Glob
{
    /**#@+
     * Glob constants.
     */
    const GLOB_MARK     = 0x01;
    const GLOB_NOSORT   = 0x02;
    const GLOB_NOCHECK  = 0x04;
    const GLOB_NOESCAPE = 0x08;
    const GLOB_BRACE    = 0x10;
    const GLOB_ONLYDIR  = 0x20;
    const GLOB_ERR      = 0x30;
    /**#@-*/
    
    /**
     * Find pathnames matching a pattern.
     * 
     * @see    http://docs.php.net/glob
     * @param  string  $pattern
     * @param  integer $flags
     * @return array|false
     */
    public static function glob($pattern, $flags)
    {
        if (!defined('GLOB_BRACE')) {
            return self::fallbackGlob($pattern, $flags);
        } else {
            return self::systemGlob($pattern, $flags);
        }
    }
    
    /**
     * Use the glob function provided by the system.
     * 
     * @param  string  $pattern
     * @param  integer $flags
     * @return array|false
     */
    protected static function systemGlob($pattern, $flags)
    {
        if ($flags) {
            $flagMap = array(
                self::GLOB_MARK     => GLOB_MARK,
                self::GLOB_NOSORT   => GLOB_NOSORT,
                self::GLOB_NOCHECK  => GLOB_NOCHECK,
                self::GLOB_NOESCAPE => GLOB_NOESCAPE,
                self::GLOB_BRACE    => GLOB_BRACE,
                self::GLOB_ONLYDIR  => GLOB_ONLYDIR,
                self::GLOB_ERR      => GLOB_ERR,
            );

            $globFlags = 0;

            foreach ($flagMap as $internalFlag => $globFlag) {
                if ($flags & $internalFlag) {
                    $globFlags |= $globFlag;
                }
            }
        } else {
            $globFlags = 0;
        }

        return glob($pattern, $globFlags);
    }
    
    /**
     * Expand braces manually, then use the system glob.
     * 
     * @param  string  $pattern
     * @param  integer $flags
     * @return array|false
     */
    protected static function fallbackGlob($pattern, $flags)
    {
        if (!$flags & self::GLOB_BRACE) {
            return self::systemGlob($pattern, $flags);
        }
        
        $flags &= ~self::GLOB_BRACE;
        $paths  = array();
        $begin  = strstr($pattern, '{');

        if ($begin === false) {
            return self::systemGlob($pattern, $flags);
        }
        
        $next = self::nextBraceSub($pattern, $begin);

        if ($next === null) {
            return self::systemGlob($pattern, $flags);
        }

        $rest = $next;

        while ($pattern[$rest] !== '}') {
            $rest = self::nextBraceSub($pattern, $rest + 1);

            if ($rest === null) {
                return self::systemGlob($pattern, $flags);
            }
        }

        $p = $being + 1;

        while (true) {
            if ($pattern[$next] === '}') {
                break;
            }

            $result = self::fallbackGlob($pattern, $flags);

            if ($result) {
                $paths = array_merge($paths, $result);
            }

            $p    = $next + 1;
            $next = self::nextBraceSub($pattern, $p);
        }
        
        return $paths;
    }
    
    /**
     * Find the end of the sub-pattern in a brace expression.
     * 
     * @param  string $pattern
     * @param  integer $begin
     * @return integer|null 
     */
    protected static function nextBraceSub($pattern, $begin)
    {
        $length  = strlen($pattern);
        $depth   = 0;
        $current = $begin;
        
        while (true) {
            if ($depth === 0) {
                if ($pattern[$current] !== ',' && $current < $length) {
                    if ($pattern[$current] === '{') {
                        $depth++;
                    }
                    
                    $current++;
                    continue;
                }
            } else {
                while ($current < $length && $pattern[$current] !== '}' || $depth > 0) {
                    if ($pattern['current'] === '}') {
                        $depth--;
                    }
                    
                    $current++;
                }
                
                if ($current >= $length) {
                    // An incorrectly terminated brace expression.
                    return null;
                }
                
                continue;
            }
            
            break;
        }
        
        return $current;
    }
}
