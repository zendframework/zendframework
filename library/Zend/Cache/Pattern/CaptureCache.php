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
 * @subpackage Pattern
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Pattern;

use Zend\Cache\Exception,
    Zend\Cache\Storage\Adapter as StorageAdapter;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Pattern
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class CaptureCache extends AbstractPattern
{
    /**
     * Public directory
     *
     * @var string
     */
    protected $publicDir = null;

    /**
     * Used umask on creating a cache directory
     *
     * @var int
     */
    protected $dirUmask = 0007;

    /**
     * Used umask on creating a cache file
     *
     * @var int
     */
    protected $fileUmask = 0117;

    /**
     * Lock files on writing
     *
     * @var boolean
     */
    protected $fileLocking = true;

    /**
     * The index filename
     *
     * @var string
     */
    protected $indexFilename = 'index.html';

    /**
     * Page identifier
     *
     * @var null|string
     */
    protected $pageId = null;

    /**
     * Storage for tagging
     *
     * @var null|StorageAdapter
     */
    protected $tagStorage = null;

    /**
     * Cache item key to store tags
     *
     * @var string
     */
    protected $tagKey = 'ZendCachePatternCaptureCache_Tags';

    /**
     * Tags
     *
     * @var array
     */
    protected $tags = array();

    /**
     * Constructor
     *
     * @param array|\Traversable $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
    }

    /**
     * Get all pattern options
     *
     * @return array
     */
    public function getOptions()
    {
        return array(
            'public_dir'     => $this->getPublicDir(),
            'dir_perm'       => $this->getDirPerm(),
            'dir_umask'      => $this->getDirUmask(),
            'file_perm'      => $this->getFilePerm(),
            'file_umask'     => $this->getFileUmask(),
            'file_locking'   => $this->getFileLocking(),
            'tag_storage'    => $this->getTagStorage(),
            'tag_key'        => $this->getTagKey(),
        );
    }

    /**
     * Set public directory
     *
     * @param  null|string $dir
     * @return CaptureCache
     */
    public function setPublicDir($dir)
    {
        $this->publicDir = $dir;
        return $this;
    }

    /**
     * Get public directory
     *
     * @return null|string
     */
    public function getPublicDir()
    {
        return $this->publicDir;
    }

    /**
     * Set directory permissions
     *
     * @param  int|string $perm Permissions as octal number
     * @return CaptureCache
     */
    public function setDirPerm($perm)
    {
        if (is_string($perm)) {
            $perm = octdec($perm);
        } else {
            $perm = (int) $perm;
        }

        // use umask
        return $this->setDirUmask(~$perm);
    }

    /**
     * Get directory permissions
     *
     * @return int
     */
    public function getDirPerm()
    {
        return ~$this->getDirUmask();
    }

    /**
     * Set directory umask
     *
     * @param  int|string $umask Umask as octal number
     * @return CaptureCache
     */
    public function setDirUmask($umask)
    {
        if (is_string($umask)) {
            $umask = octdec($umask);
        } else {
            $umask = (int)$umask;
        }

        if ((~$umask & 0700) != 0700 ) {
            throw new Exception\InvalidArgumentException(
                'Invalid directory umask or directory permissions: '
              . 'need permissions to execute, read and write directories by owner'
            );
        }

        $this->dirUmask = $umask;
        return $this;
    }

    /**
     * Get directory umask
     *
     * @return int
     */
    public function getDirUmask()
    {
        return $this->dirUmask;
    }

    /**
     * Set file permissions
     *
     * @param  int|string $perm Permissions as octal number
     * @return CaptureCache
     */
    public function setFilePerm($perm)
    {
        if (is_string($perm)) {
            $perm = octdec($perm);
        } else {
            $perm = (int) $perm;
        }

        // use umask
        return $this->setFileUmask(~$perm);
    }

    /**
     * Get file permissions
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
     * @param  int|string $umask Umask as octal number
     * @return CaptureCache
     */
    public function setFileUmask($umask)
    {
        if (is_string($umask)) {
            $umask = octdec($umask);
        } else {
            $umask = (int) $umask;
        }
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
     * Set file locking
     *
     * @param  bool $flag
     * @return CaptureCache
     */
    public function setFileLocking($flag)
    {
        $this->fileLocking = (bool) $flag;
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
     * Set index filename
     *
     * @param  string $filename
     * @return CaptureCache
     */
    public function setIndexFilename($filename)
    {
        $this->indexFilename = (string) $filename;
        return $this;
    }

    /**
     * Get index filename
     *
     * @return string
     */
    public function getIndexFilename()
    {
        return $this->indexFilename;
    }

    /**
     * Set a storage for tagging or remove the storage
     *
     * @param  null|StorageAdapter $storage
     * @return CaptureCache
     */
    public function setTagStorage(StorageAdapter $storage = null)
    {
        $this->tagStorage = $storage;
        return $this;
    }

    /**
     * Get the storage for tagging
     *
     * @return null|StorageAdapter
     */
    public function getTagStorage()
    {
        return $this->tagStorage;
    }

    /**
     * Set cache item key to store tags
     *
     * @param  $tagKey string
     * @return CaptureCache
     */
    public function setTagKey($tagKey)
    {
        if (($tagKey = (string)$tagKey) === '') {
            throw new Exception\InvalidArgumentException("Missing tag key '{$tagKey}'");
        }

        $this->tagKey = $tagKey;
        return $this;
    }

    /**
     * Get cache item key to store tags
     *
     * @return string
     */
    public function getTagKey()
    {
        return $this->tagKey;
    }

    /**
     * Set tags to store
     *
     * @param  array $tags
     * @return CaptureCache
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Get tags to store
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Start the cache
     *
     * @param  string $pageId  Page identifier
     * @param  array  $options Options
     * @return boolean false
     */
    public function start($pageId = null, array $options = array())
    {
        if ($this->pageId !== null) {
            throw new Exception\RuntimeException("Capturing already stated with page id '{$this->pageId}'");
        }

        if (isset($options['tags'])) {
            $this->setTags($options['tags']);
            unset($options['tags']);
        }

        if ($this->getTags() && !$this->getTagStorage()) {
            throw new Exception\RuntimeException('Tags are defined but missing a tag storage');
        }

        if (($pageId = (string)$pageId) === '') {
            $pageId = $this->detectPageId();
        }

        ob_start(array($this, 'flush'));
        ob_implicitflush(false);
        $this->pageId = $pageId;

        return false;
    }

    /**
     * Get from cache
     *
     * @param  null|string $pageId
     * @param  array $options
     * @return bool|string
     * @throws Exception\RuntimeException
     */
    public function get($pageId = null, array $options = array())
    {
        if (($pageId = (string)$pageId) === '') {
            $pageId = $this->detectPageId();
        }

        $file = $this->getPublicDir()
              . DIRECTORY_SEPARATOR . $this->pageId2Path($pageId)
              . DIRECTORY_SEPARATOR . $this->pageId2Filename($pageId);

        if (file_exists($file)) {
            if (($content = @file_get_contents($file)) === false) {
                $lastErr = error_get_last();
                throw new Exception\RuntimeException("Failed to read cached pageId '{$pageId}': {$lastErr['message']}");
            }
            return $content;
        }

        return false;
    }

    /**
     * Checks if a cache with given id exists
     *
     * @param  null|string $pageId
     * @param  array $options
     * @return bool
     */
    public function exists($pageId = null, array $options = array())
    {
        if (($pageId = (string)$pageId) === '') {
            $pageId = $this->detectPageId();
        }

        $file = $this->getPublicDir()
              . DIRECTORY_SEPARATOR . $this->pageId2Path($pageId)
              . DIRECTORY_SEPARATOR . $this->pageId2Filename($pageId);

        return file_exists($file);
    }

    /**
     * Remove from cache
     *
     * @param  null|string $pageId
     * @param  array $options
     * @throws Exception\RuntimeException
     * @return void
     */
    public function remove($pageId = null, array $options = array())
    {
        if (($pageId = (string)$pageId) === '') {
            $pageId = $this->detectPageId();
        }

        $file = $this->getPublicDir()
              . DIRECTORY_SEPARATOR . $this->pageId2Path($pageId)
              . DIRECTORY_SEPARATOR . $this->pageId2Filename($pageId);

        if (file_exists($file)) {
            if (!@unlink($file)) {
                $lastErr = error_get_last();
                throw new Exception\RuntimeException("Failed to remove cached pageId '{$pageId}': {$lastErr['message']}");
            }
        }
    }

    /**
     * Clear cache
     */
    public function clear(/*TODO*/)
    {
        // TODO
    }

    /**
     * Determine the page to save from the request
     *
     * @return string
     */
    protected function detectPageId()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Get filename for page id
     *
     * @param string $pageId
     * @return string
     */
    protected function pageId2Filename($pageId)
    {
        $filename = basename($pageId);

        if ($filename === '') {
            $filename = $this->getIndexFilename();
        }

        return $filename;
    }

    /**
     * Get path for page id
     *
     * @param string $pageId
     * @return string
     */
    protected function pageId2Path($pageId)
    {
        $path = rtrim(dirname($pageId), '/');

        // convert requested "/" to the valid local directory separator
        if ('/' != DIRECTORY_SEPARATOR) {
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        }

        return $path;
    }

    /**
     * callback for output buffering
     *
     * @param  string $output Buffered output
     * @return boolean FALSE means original input is sent to the browser.
     */
    protected function flush($output)
    {
        $this->save($output);

        // http://php.net/manual/function.ob-start.php
        // -> If output_callback  returns FALSE original input is sent to the browser.
        return false;
    }

    /**
     * Save the cache
     *
     * @param  $output
     * @throws Exception\RuntimeException
     */
    protected function save($output)
    {
        $path     = $this->pageId2Path($this->pageId);
        $fullPath = $this->getPublicDir() . DIRECTORY_SEPARATOR . $path;
        if (!file_exists($fullPath)) {
            $oldUmask = umask($this->getDirUmask());
            if (!@mkdir($fullPath, 0777, true)) {
                $lastErr = error_get_last();
                throw new Exception\RuntimeException(
                    "Can't create directory '{$fullPath}': {$lastErr['message']}"
                );
            }
        }

        if ($oldUmask !== null) { // $oldUmask could be set on create directory
            umask($this->getFileUmask());
        } else {
            $oldUmask = umask($this->getFileUmask());
        }
        $file = $path . DIRECTORY_SEPARATOR . $this->pageId2Filename($this->pageId);
        $fullFile = $this->getPublicDir() . DIRECTORY_SEPARATOR . $file;
        $this->putFileContent($fullFile, $output);

        $tagStorage = $this->getTagStorage();
        if ($tagStorage) {
            $tagKey     = $this->getTagKey();
            $tagIndex = $tagStorage->getTagStorage()->getItem($tagKey);
            if (!$tagIndex) {
                $tagIndex = null;
            }

            if ($this->tags) {
                $tagIndex[$file] = &$this->tags;
            } elseif ($tagIndex) {
                unset($tagIndex[$file]);
            }

            if ($tagIndex !== null) {
                $this->getTagStorage()->setItem($tagKey, $tagIndex);
            }
        }
    }

    /**
     * Write content to a file
     *
     * @param  string $file  File complete path
     * @param  string $data  Data to write
     * @throws Exception\RuntimeException
     */
    protected function putFileContent($file, $data)
    {
        $flags = FILE_BINARY; // since PHP 6 but defined as 0 in PHP 5.3
        if ($this->getFileLocking()) {
            $flags = $flags | LOCK_EX;
        }

        $put = @file_put_contents($file, $data, $flags);
        if ( $put < strlen((binary)$data) ) {
            $lastErr = error_get_last();
            @unlink($file); // remove old or incomplete written file
            throw new Exception\RuntimeException($lastErr['message']);
        }
    }
}
