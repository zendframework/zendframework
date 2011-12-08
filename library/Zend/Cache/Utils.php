<?php

namespace Zend\Cache;
use Zend\Cache\Exception\RuntimeException;

class Utils
{

    /**
     * Get disk capacity
     *
     * @param string $path A directory of the filesystem or disk partition
     * @return array
     * @throws Zend\Cache\Exception\RuntimeException
     */
    static public function getDiskCapacity($path)
    {
        $total = @disk_total_space($path);
        if ($total === false) {
            $err = error_get_last();
            throw new RuntimeException($err['message']);
        }

        $free = @disk_free_space($path);
        if ($free === false) {
            $err = error_get_last();
            throw new RuntimeException($err['message']);
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
     * @throws Zend\Cache\Exception\RuntimeException
     */
    static public function getPhpMemoryCapacity()
    {
        $memSize = (float)self::bytesFromString(ini_get('memory_limit'));
        if ($memSize <= 0) {
            return self::getSystemMemoryCapacity();
        }

        $memUsed = (float)memory_get_usage(true);
        $memFree = $memSize - $memUsed;

        return array(
            'total' => $memSize,
            'free'  => $memFree
        );
    }

    /**
     * Get system memory capacity
     *
     * @return array
     * @throws Zend\Cache\Exception\RuntimeException
     */
    static public function getSystemMemoryCapacity()
    {
        // Windows
        if (substr(\PHP_OS, 0, 3) == 'WIN') {
            return self::_getSystemMemoryCapacityWin();
        }

        // *nix
        if ( !($meminfo = @file_get_contents('/proc/meminfo')) ) {
            $lastErr = error_get_last();
            throw new RuntimeException("Can't read '/proc/meminfo': {$lastErr['messagae']}");
        } elseif (!preg_match_all('/(\w+):\s*(\d+\s*\w*)[\r|\n]/i', $meminfo, $matches, PREG_PATTERN_ORDER)) {
            throw new RuntimeException("Can't parse '/proc/meminfo'");
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
            'free'  => $memFree
        );
    }

    /**
     * Get system memory capacity on windows systems
     *
     * @return array
     * @throws Zend\Cache\Exception\RuntimeException
     */
    static protected function _getSystemMemoryCapacityWin()
    {
        if (function_exists('win32_ps_stat_mem')) {
            $memstat = win32_ps_stat_mem();
        } elseif (!function_exists('exec')) {
            throw new RuntimeException(
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
                throw new RuntimeException(
                    "Command '{$cmd}' failed"
                  . ", return: '{$ret}'"
                  . ", output: '{$out}'"
                );
            } elseif (!($memstat = @unserialize($line)) ) {
                $err = error_get_last();
                $out = implode("\n", $out);
                throw new RuntimeException(
                    "Can't parse output of command '{$cmd}'"
                  . ": {$err['message']}"
                  . ", output: '{$out}'"
                );
            }
        }

        if (!isset($memstat['total_phys'], $memstat['avail_phys'])) {
            throw new RuntimeException("Can't detect memory status");
        }

        return array(
            'total' => $memstat['total_phys'],
            'free'  => $memstat['avail_phys']
        );
    }

    /**
     * Generate a hash value.
     * This helper adds the virtual hash algo "strlen".
     *
     * @param  string $data  Name of selected hashing algorithm
     * @param  string $data  Message to be hashed.
     * @param  bool   $raw   When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
     * @return string        Hash value
     * @throws Zend\Cache\RuntimeException
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
                throw new RuntimeException("Hash generation failed for algo '{$algo}'");
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
        $algos = hash_algos();
        $algos[] = 'strlen';
        return $algos;
    }

    /**
     * Returns the number of bytes from a memory string (like 1 kB -> 1024)
     *
     * @param string $memStr
     * @return float
     * @throws Zend\Cache\Exception\RuntimeException
     */
    static protected function bytesFromString($memStr)
    {
        if (preg_match('/\s*([\-\+]?\d+)\s*(\w*)\s*/', $memStr, $matches)) {
            $value = (float)$matches[1];
            $unit  = strtolower($matches[2]);

            switch ($unit) {
                case '':
                case 'b':
                    break;

                case 'k':
                case 'kb':
                    $value*= 1024;
                    break;

                case 'm':
                case 'mb':
                    $value*= 1048576; // 1024 * 1024
                    break;

                case 'g':
                case 'gb':
                    $value*= 1073741824; // 1024 * 1024 * 1024
                    break;

                default:
                    throw new RuntimeException("Unknown unit '{$unit}'");
            }
        } else {
            throw new RuntimeException("Can't detect bytes of string '{$memStr}'");
        }

        return $value;
    }

}
