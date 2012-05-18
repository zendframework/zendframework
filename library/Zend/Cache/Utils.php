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
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Utils
{
    /**
     * Get disk capacity
     *
     * @param  string $path A directory of the filesystem or disk partition
     * @return array
     * @throws Exception\RuntimeException
     */
    public static function getDiskCapacity($path)
    {
        $total = @disk_total_space($path);
        if ($total === false) {
            $err = error_get_last();
            throw new Exception\RuntimeException($err['message']);
        }

        $free = @disk_free_space($path);
        if ($free === false) {
            $err = error_get_last();
            throw new Exception\RuntimeException($err['message']);
        }

        return array(
            'total' => $total,
            'free'  => $free,
        );
    }

    /**
     * Get php memory capacity
     *
     * @return array
     * @throws Exception\RuntimeException
     */
    public static function getPhpMemoryCapacity()
    {
        $memSize = (float) self::bytesFromString(ini_get('memory_limit'));
        if ($memSize <= 0) {
            return self::getSystemMemoryCapacity();
        }

        $memUsed = (float) memory_get_usage(true);
        $memFree = $memSize - $memUsed;

        return array(
            'total' => $memSize,
            'free'  => $memFree,
        );
    }

    /**
     * Get system memory capacity
     *
     * @return array
     * @throws Exception\RuntimeException
     */
    public static function getSystemMemoryCapacity()
    {
        // Windows
        if (substr(\PHP_OS, 0, 3) == 'WIN') {
            return self::getSystemMemoryCapacityWin();
        }

        // Darwin
        if (substr(\PHP_OS, 0, 6) == 'Darwin') {
            return self::getSystemMemoryCapacityOSX();
        }

        // *nix
        if (false === ($meminfo = @file_get_contents('/proc/meminfo'))) {
            $lastErr = error_get_last();
            throw new Exception\RuntimeException("Can't read '/proc/meminfo': {$lastErr['message']}");
        } elseif (!preg_match_all('/(\w+):\s*(\d+\s*\w*)[\r|\n]/i', $meminfo, $matches, PREG_PATTERN_ORDER)) {
            throw new Exception\RuntimeException("Can't parse '/proc/meminfo'");
        }

        $meminfoIndex  = array_flip($matches[1]);
        $meminfoValues = $matches[2];

        $memTotal = 0;
        $memFree  = 0;

        if (isset($meminfoIndex['MemTotal'])) {
            $memTotal+= self::bytesFromString( $meminfoValues[ $meminfoIndex['MemTotal'] ] );
        }
        if (isset($meminfoIndex['MemFree'])) {
            $memFree+= self::bytesFromString( $meminfoValues[ $meminfoIndex['MemFree'] ] );
        }
        if (isset($meminfoIndex['Buffers'])) {
            $memFree+= self::bytesFromString( $meminfoValues[ $meminfoIndex['Buffers'] ] );
        }
        if (isset($meminfoIndex['Cached'])) {
            $memFree+= self::bytesFromString( $meminfoValues[ $meminfoIndex['Cached'] ] );
        }

        return array(
            'total' => $memTotal,
            'free'  => $memFree,
        );
    }

    /**
     * Get system memory capacity on windows systems
     *
     * @return array
     * @throws Exception\RuntimeException
     */
    static protected function getSystemMemoryCapacityWin()
    {
        if (function_exists('win32_ps_stat_mem')) {
            $memstat = win32_ps_stat_mem();
        } elseif (!function_exists('exec')) {
            throw new Exception\RuntimeException(
                "Missing php extension 'win32ps' and the build-in function 'exec' is disabled"
            );
        } else {
            // call [DIR]\_win\GlobalMemoryStatus.exe
            $cmd  = escapeshellarg( // escapeshellarg instead of escapeshellcmd
                __DIR__
                . DIRECTORY_SEPARATOR . '_win'
                . DIRECTORY_SEPARATOR . 'GlobalMemoryStatus.exe'
            );
            $out  = $ret = null;
            $line = exec($cmd, $out, $ret);
            if ($ret) {
                $out = implode("\n", $out);
                throw new Exception\RuntimeException(
                    "Command '{$cmd}' failed"
                    . ", return: '{$ret}'"
                    . ", output: '{$out}'"
                );
            } elseif (!($memstat = @unserialize($line)) ) {
                $err = error_get_last();
                $out = implode("\n", $out);
                throw new Exception\RuntimeException(
                    "Can't parse output of command '{$cmd}'"
                    . ": {$err['message']}"
                    . ", output: '{$out}'"
                );
            }
        }

        if (!isset($memstat['total_phys'], $memstat['avail_phys'])) {
            throw new Exception\RuntimeException("Can't detect memory status");
        }

        return array(
            'total' => $memstat['total_phys'],
            'free'  => $memstat['avail_phys'],
        );
    }

    /**
     * Get system memory capacity on windows systems
     *
     * @return array
     * @throws Exception\RuntimeException
     */
    static protected function getSystemMemoryCapacityOSX()
    {
        $total = 0;
        $free = 0;

        if (!function_exists('exec')) {
            throw new Exception\RuntimeException(
                "Built-in function 'exec' is disabled"
            );
        } else {
            // sysctl will tell us the total amount of memory
            $cmd  = 'sysctl -n hw.memsize';
            $out  = $ret = null;
            $line = exec($cmd, $out, $ret);
           
            if ($ret) {
                $out = implode("\n", $out);
                throw new Exception\RuntimeException(
                    "Command '{$cmd}' failed"
                    . ", return: '{$ret}'"
                    . ", output: '{$out}'"
                );
            }
            $total = $line;

            // now work out amount used using vm_stat
            $cmd  = 'vm_stat | grep free';
            $out  = $ret = null;
            $line = exec($cmd, $out, $ret);
           
            if ($ret) {
                $out = implode("\n", $out);
                throw new Exception\RuntimeException(
                    "Command '{$cmd}' failed"
                    . ", return: '{$ret}'"
                    . ", output: '{$out}'"
                );
            }
            preg_match('/([\d]+)/', $line, $matches);
            if (isset($matches[1])) {
                $free = $matches[1] * 4096;
            }
        }

        return array(
            'total' => $total,
            'free'  => $free,
        );
    }

    /**
     * Generate a hash value.
     *
     * This helper adds the virtual hash algo "strlen".
     *
     * @param  string $algo  Name of selected hashing algorithm
     * @param  string $data  Message to be hashed.
     * @param  bool   $raw   When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
     * @return string        Hash value
     * @throws Exception\RuntimeException
     */
    static public function generateHash($algo, $data, $raw = false)
    {
        // special case on strlen as virtual hash algo
        if ($algo === 'strlen') {
            if ($raw) {
                $hash = pack('l', strlen($data));
            } else {
                $hash = dechex(strlen($data));
            }
        } else {
            $hash = hash($algo, $data, $raw);
            if ($hash === false) {
                throw new Exception\RuntimeException("Hash generation failed for algo '{$algo}'");
            }
        }

        return $hash;
    }

    /**
     * Return a list of registered hashing algorithms
     * incl. the virtual hash algo "strlen".
     *
     * @return string[]
     */
    static public function getHashAlgos()
    {
        $algos   = hash_algos();
        $algos[] = 'strlen';
        return $algos;
    }

    /**
     * Returns the number of bytes from a memory string (like 1 kB -> 1024)
     *
     * @param string $memStr
     * @return float
     * @throws Exception\RuntimeException
     */
    static public function bytesFromString($memStr)
    {
        if (!preg_match('/\s*([\-\+]?\d+)\s*(\w*)\s*/', $memStr, $matches)) {
            throw new Exception\RuntimeException("Can't detect bytes of string '{$memStr}'");
        }

        $value = (float)$matches[1];
        $unit  = strtolower($matches[2]);

        switch ($unit) {
            case 'g':
            case 'gb':
                $value*= 1024;
                // Break intentionally omitted

            case 'm':
            case 'mb':
                $value*= 1024;
                // Break intentionally omitted

            case 'k':
            case 'kb':
                $value*= 1024;
                // Break intentionally omitted

            case '':
            case 'b':
                break;

            default:
                throw new Exception\RuntimeException("Unknown unit '{$unit}'");
        }

        return $value;
    }
}
