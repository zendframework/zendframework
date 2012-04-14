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
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use Zend\Cache\Exception,
    Zend\Cache\Utils;

/**
 * These are options specific to the Filesystem adapter
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FilesystemOptions extends AdapterOptions
{

    /**
     * Directory to store cache files
     *
     * @var null|string The cache directory
     *                  or NULL for the systems temporary directory
     */
    protected $cacheDir = null;

    /**
     * Call clearstatcache enabled?
     *
     * @var boolean
     */
    protected $clearStatCache = true;

    /**
     * How much sub-directaries should be created?
     *
     * @var int
     */
    protected $dirLevel = 1;

    /**
     * Used umask on creating a cache directory
     *
     * @var int
     */
    protected $dirUmask = 0007;

    /**
     * Lock files on writing
     *
     * @var boolean
     */
    protected $fileLocking = true;

    /**
     * Used umask on creating a cache file
     *
     * @var int
     */
    protected $fileUmask = 0117;

    /**
     * Overwrite default key pattern
     *
     * Defined in AdapterOptions
     *
     * @var string
     */
    protected $keyPattern = '/^[a-z0-9_\+\-]*$/Di';

    /**
     * Overwrite default namespace pattern
     *
     * Defined in AdapterOptions.
     *
     * @var string
     */
    protected $namespacePattern = '/^[a-z0-9_\+\-]*$/Di';

    /**
     * Namespace separator
     *
     * @var string
     */
    protected $namespaceSeparator = '-';

    /**
     * Don't get 'fileatime' as 'atime' on metadata
     *
     * @var boolean
     */
    protected $noAtime = true;

    /**
     * Don't get 'filectime' as 'ctime' on metadata
     *
     * @var boolean
     */
    protected $noCtime = true;

    /**
     * Read control enabled ?
     *
     * If enabled a hash (readControlAlgo) will be saved and check on read.
     *
     * @var boolean
     */
    protected $readControl = false;

    /**
     * The used hash algorithm if read control is enabled
     *
     * @var string
     */
    protected $readControlAlgo = 'crc32';

    /**
     * Set cache dir
     *
     * @param  string $dir
     * @return FilesystemOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setCacheDir($dir)
    {
        if ($dir !== null) {
            if (!is_dir($dir)) {
                throw new Exception\InvalidArgumentException(
                    "Cache directory '{$dir}' not found or not a directoy"
                );
            } elseif (!is_writable($dir)) {
                throw new Exception\InvalidArgumentException(
                    "Cache directory '{$dir}' not writable"
                );
            } elseif (!is_readable($dir)) {
                throw new Exception\InvalidArgumentException(
                    "Cache directory '{$dir}' not readable"
                );
            }

            $dir = rtrim(realpath($dir), \DIRECTORY_SEPARATOR);
        } else {
            $dir = sys_get_temp_dir();
        }

        $this->triggerOptionEvent('cache_dir', $dir);
        $this->cacheDir = $dir;
        return $this;
    }

    /**
     * Get cache dir
     *
     * @return null|string
     */
    public function getCacheDir()
    {
        if ($this->cacheDir === null) {
            $this->setCacheDir(null);
        }

        return $this->cacheDir;
    }

    /**
     * Set clear stat cache
     *
     * @param  bool $flag
     * @return FilesystemOptions
     */
    public function setClearStatCache($flag)
    {
        $flag = (bool) $flag;
        $this->triggerOptionEvent('clear_stat_cache', $flag);
        $this->clearStatCache = $flag;
        return $this;
    }

    /**
     * Get clear stat cache
     *
     * @return bool
     */
    public function getClearStatCache()
    {
        return $this->clearStatCache;
    }

    /**
     * Set dir level
     *
     * @param  int $level
     * @return FilesystemOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setDirLevel($level)
    {
        $level = (int) $level;
        if ($level < 0 || $level > 16) {
            throw new Exception\InvalidArgumentException(
                "Directory level '{$level}' must be between 0 and 16"
            );
        }
        $this->triggerOptionEvent('dir_level', $level);
        $this->dirLevel = $level;
        return $this;
    }

    /**
     * Get dir level
     *
     * @return int
     */
    public function getDirLevel()
    {
        return $this->dirLevel;
    }

    /**
     * Set dir perm
     *
     * @param  string|int $perm
     * @return FilesystemOptions
     */
    public function setDirPerm($perm)
    {
        $perm = $this->normalizeUmask($perm);

        // use umask
        return $this->setDirUmask(~$perm);
    }

    /**
     * Get dir perm
     *
     * @return int
     */
    public function getDirPerm()
    {
        return ~$this->getDirUmask();
    }

    /**
     * Set dir umask
     *
     * @param  string|int $umask
     * @return FilesystemOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setDirUmask($umask)
    {
        $umask = $this->normalizeUmask($umask, function($umask) {
            if ((~$umask & 0700) != 0700 ) {
                throw new Exception\InvalidArgumentException(
                    'Invalid directory umask or directory permissions: '
                    . 'need permissions to execute, read and write directories by owner'
                );
            }
        });

        $this->triggerOptionEvent('dir_umask', $umask);
        $this->dirUmask = $umask;
        return $this;
    }

    /**
     * Get dir umask
     *
     * @return int
     */
    public function getDirUmask()
    {
        return $this->dirUmask;
    }

    /**
     * Set file locking
     *
     * @param  bool $flag
     * @return FilesystemOptions
     */
    public function setFileLocking($flag)
    {
        $flag = (bool) $flag;
        $this->triggerOptionEvent('file_locking', $flag);
        $this->fileLocking = $flag;
        return $this;
    }

    /**
     * Get file locking
     *
     * @return bool
     */
    public function getFileLocking()
    {
        return $this->fileLocking;
    }

    /**
     * Set file perm
     *
     * @param  int $perm
     * @return FilesystemOptions
     */
    public function setFilePerm($perm)
    {
        $perm = $this->normalizeUmask($perm);

        // use umask
        return $this->setFileUmask(~$perm);
    }

    /**
     * Get file perm
     *
     * @return int
     */
    public function getFilePerm()
    {
        return ~$this->getFileUmask();
    }

    /**
     * Set file umask
     *
     * @param  int $umask
     * @return FilesystemOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setFileUmask($umask)
    {
        $umask = $this->normalizeUmask($umask, function($umask) {
            if ((~$umask & 0600) != 0600 ) {
                throw new Exception\InvalidArgumentException(
                    'Invalid file umask or file permission: '
                    . 'need permissions to read and write files by owner'
                );
            } elseif ((~$umask & 0111) > 0) {
                throw new Exception\InvalidArgumentException(
                    'Invalid file umask or file permission: '
                    . 'executable cache files are not allowed'
                );
            }
        });

        $this->triggerOptionEvent('file_umask', $umask);
        $this->fileUmask = $umask;
        return $this;
    }

    /**
     * Get file umask
     *
     * @return int
     */
    public function getFileUmask()
    {
        return $this->fileUmask;
    }

    /**
     * Set namespace separator
     *
     * @param  string $separator
     * @return FilesystemOptions
     */
    public function setNamespaceSeparator($separator)
    {
        $separator = (string) $separator;
        $this->triggerOptionEvent('namespace_separator', $separator);
        $this->namespaceSeparator = $separator;
        return $this;
    }

    /**
     * Get namespace separator
     *
     * @return string
     */
    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }

    /**
     * Set no atime
     *
     * @param  bool $flag
     * @return FilesystemOptions
     */
    public function setNoAtime($flag)
    {
        $flag = (bool) $flag;
        $this->triggerOptionEvent('no_atime', $flag);
        $this->noAtime = $flag;
        return $this;
    }

    /**
     * Get no atime
     *
     * @return bool
     */
    public function getNoAtime()
    {
        return $this->noAtime;
    }

    /**
     * Set no ctime
     *
     * @param  bool $flag
     * @return FilesystemOptions
     */
    public function setNoCtime($flag)
    {
        $flag = (bool) $flag;
        $this->triggerOptionEvent('no_ctime', $flag);
        $this->noCtime = $flag;
        return $this;
    }

    /**
     * Get no ctime
     *
     * @return bool
     */
    public function getNoCtime()
    {
        return $this->noCtime;
    }

    /**
     * Set read control
     *
     * @param  bool $flag
     * @return FilesystemOptions
     */
    public function setReadControl($flag)
    {
        $flag = (bool) $flag;
        $this->triggerOptionEvent('read_control', $flag);
        $this->readControl = $flag;
        return $this;
    }

    /**
     * Get read control
     *
     * @return bool
     */
    public function getReadControl()
    {
        return $this->readControl;
    }

    /**
     * Set real control algo
     *
     * @param  string $algo
     * @return FilesystemOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setReadControlAlgo($algo)
    {
        $algo = strtolower($algo);

        if (!in_array($algo, Utils::getHashAlgos())) {
            throw new Exception\InvalidArgumentException("Unsupported hash algorithm '{$algo}");
        }

        $this->triggerOptionEvent('read_control_algo', $algo);
        $this->readControlAlgo = $algo;
        return $this;
    }

    /**
     * Get read control algo
     *
     * @return string
     */
    public function getReadControlAlgo()
    {
        return $this->readControlAlgo;
    }

    /**
     * Normalize a umask and optionally apply a callback to it
     *
     * @param  int|string $umask
     * @param  callable $callback
     * @return int
     */
    protected function normalizeUmask($umask, $callback = null)
    {
        if (is_string($umask)) {
            $umask = octdec($umask);
        } else {
            $umask = (int) $umask;
        }

        if (is_callable($callback)) {
            $callback($umask);
        }

        return $umask;
    }
}
