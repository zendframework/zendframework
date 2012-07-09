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

use Zend\Cache\Exception;

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
     * Set cache dir
     *
     * @param  string $cacheDir
     * @return FilesystemOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setCacheDir($cacheDir)
    {
        if ($cacheDir !== null) {
            if (!is_dir($cacheDir)) {
                throw new Exception\InvalidArgumentException(
                    "Cache directory '{$cacheDir}' not found or not a directoy"
                );
            } elseif (!is_writable($cacheDir)) {
                throw new Exception\InvalidArgumentException(
                    "Cache directory '{$cacheDir}' not writable"
                );
            } elseif (!is_readable($cacheDir)) {
                throw new Exception\InvalidArgumentException(
                    "Cache directory '{$cacheDir}' not readable"
                );
            }

            $cacheDir = rtrim(realpath($cacheDir), \DIRECTORY_SEPARATOR);
        } else {
            $cacheDir = sys_get_temp_dir();
        }

        $this->triggerOptionEvent('cache_dir', $cacheDir);
        $this->cacheDir = $cacheDir;
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
     * @param  bool $clearStatCache
     * @return FilesystemOptions
     */
    public function setClearStatCache($clearStatCache)
    {
        $clearStatCache = (bool) $clearStatCache;
        $this->triggerOptionEvent('clear_stat_cache', $clearStatCache);
        $this->clearStatCache = $clearStatCache;
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
     * @param  int $dirLevel
     * @return FilesystemOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setDirLevel($dirLevel)
    {
        $dirLevel = (int) $dirLevel;
        if ($dirLevel < 0 || $dirLevel > 16) {
            throw new Exception\InvalidArgumentException(
                "Directory level '{$dirLevel}' must be between 0 and 16"
            );
        }
        $this->triggerOptionEvent('dir_level', $dirLevel);
        $this->dirLevel = $dirLevel;
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
     * @param  string|int $dirPerm
     * @return FilesystemOptions
     */
    public function setDirPerm($dirPerm)
    {
        $dirPerm = $this->normalizeUmask($dirPerm);

        // use umask
        return $this->setDirUmask(~$dirPerm);
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
     * @param  string|int $dirUmask
     * @return FilesystemOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setDirUmask($dirUmask)
    {
        $dirUmask = $this->normalizeUmask($dirUmask, function($dirUmask) {
            if ((~$dirUmask & 0700) != 0700 ) {
                throw new Exception\InvalidArgumentException(
                    'Invalid directory umask or directory permissions: '
                    . 'need permissions to execute, read and write directories by owner'
                );
            }
        });

        $this->triggerOptionEvent('dir_umask', $dirUmask);
        $this->dirUmask = $dirUmask;
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
     * @param  bool $fileLocking
     * @return FilesystemOptions
     */
    public function setFileLocking($fileLocking)
    {
        $fileLocking = (bool) $fileLocking;
        $this->triggerOptionEvent('file_locking', $fileLocking);
        $this->fileLocking = $fileLocking;
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
     * @param  int $filePerm
     * @return FilesystemOptions
     */
    public function setFilePerm($filePerm)
    {
        $filePerm = $this->normalizeUmask($filePerm);

        // use umask
        return $this->setFileUmask(~$filePerm);
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
     * @param  int $fileUmask
     * @return FilesystemOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setFileUmask($fileUmask)
    {
        $fileUmask = $this->normalizeUmask($fileUmask, function($fileUmask) {
            if ((~$fileUmask & 0600) != 0600 ) {
                throw new Exception\InvalidArgumentException(
                    'Invalid file umask or file permission: '
                    . 'need permissions to read and write files by owner'
                );
            } elseif ((~$fileUmask & 0111) > 0) {
                throw new Exception\InvalidArgumentException(
                    'Invalid file umask or file permission: '
                    . 'executable cache files are not allowed'
                );
            }
        });

        $this->triggerOptionEvent('file_umask', $fileUmask);
        $this->fileUmask = $fileUmask;
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
     * @param  string $namespaceSeperator
     * @return FilesystemOptions
     */
    public function setNamespaceSeparator($namespaceSeperator)
    {
        $namespaceSeperator = (string) $namespaceSeperator;
        $this->triggerOptionEvent('namespace_separator', $namespaceSeperator);
        $this->namespaceSeparator = $namespaceSeperator;
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
     * @param  bool $noAtime
     * @return FilesystemOptions
     */
    public function setNoAtime($noAtime)
    {
        $noAtime = (bool) $noAtime;
        $this->triggerOptionEvent('no_atime', $noAtime);
        $this->noAtime = $noAtime;
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
     * @param  bool $noCtime
     * @return FilesystemOptions
     */
    public function setNoCtime($noCtime)
    {
        $noCtime = (bool) $noCtime;
        $this->triggerOptionEvent('no_ctime', $noCtime);
        $this->noCtime = $noCtime;
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
