<?php

namespace Zend\Cache\Pattern;

class CaptureCache extends AbstractPattern
{

    /**
     * Public directory
     *
     * @var string
     */
    protected $_publicDir = null;

    /**
     * Lock files on writing
     *
     * @var boolean
     */
    protected $_fileLocking = true;

    /**
     * The index filename
     *
     * @var string
     */
    protected $_indexFilename = 'index.html';

    /**
     * Page identifier
     *
     * @var null|string
     */
    protected $_pageId = null;

    /**
     * Storage for tagging
     *
     * @var null|\Zend\Cache\Storage\Adapter
     */
    protected $_tagStorage = null;

    /**
     * Cache item key to store tags
     *
     * @var string
     */
    protected $_tagKey = 'ZendCachePatternCaptureCache_Tags';

    /**
     * Tags
     *
     * @var array
     */
    protected $_tags = array();

    /**
     * Constructor
     *
     * @param array|Traversable $options
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
            'publicDir'     => $this->getPublicDir(),
            'fileExtension' => $this->getFileException(),
            'fileLocking'   => $this->getFileLocking(),
            'tagStorage'    => $this->getTagStorage(),
        );
    }

    /**
     * Set public directory
     *
     * @param null|string $dir
     * @return CaptureCache
     */
    public function setPublicDir($dir)
    {
        $this->_publicDir = $dir;
        return $this;
    }

    /**
     * Get public directory
     *
     * @return null|string
     */
    public function getPublicDir()
    {
        return $this->_publicDir;
    }

    /**
     * Set index filename
     *
     * @param string $filename
     * @return CaptureCache
     */
    public function setIndexFilename($filename)
    {
        $this->_indexFilename = (string)$filename;
        return $this;
    }

    /**
     * Get index filename
     *
     * @return string
     */
    public function getIndexFilename()
    {
        return $this->_indexFilename;
    }

    /**
     * Set file locking
     *
     * @param bool $flag
     * @return CaptureCache
     */
    public function setFileLocking($flag)
    {
        $this->_fileLocking = (boolean)$flag;
        return $this;
    }

    /**
     * Get file locking
     *
     * @return bool
     */
    public function getFileLocking()
    {
        return $this->_fileLocking;
    }

    /**
     * Set a storage for tagging
     *
     * @param \Zend\Cache\Storage\Adapter $storage
     * @return CaptureCache
     */
    public function setTagStorage(Adapter $storage)
    {
        $this->_tagStorage = $storage;
        return $this;
    }

    /**
     * Get the storage for tagging
     *
     * @return null|\Zend\Cache\Storage\Adapter
     */
    public function getTagStorage()
    {
        return $this->_tagStorage;
    }

    /**
     * Set cache item key to store tags
     *
     * @param $tagKey string
     * @return CaptureCache
     */
    public function setTagKey($tagKey)
    {
        if (($tagKey = (string)$tagKey) === '') {
            throw new InvalidArgumentException("Missing tag key '{$tagKey}'");
        }

        $this->_tagKey = $tagKey;
        return $this;
    }

    /**
     * Get cache item key to store tags
     *
     * @return string
     */
    public function getTagKey()
    {
        return $this->_tagKey;
    }

    /**
     * Set tags to store
     *
     * @param array $tags
     * @return CaptureCache
     */
    public function setTags(array $tags)
    {
        $this->_tags = $tags;
        return $this;
    }

    /**
     * Get tags to store
     *
     * @return array
     */
    public function getTags()
    {
        return $this->_tags;
    }

    /**
     * Start the cache
     *
     * @param string $pageId  Page identifier
     * @param array  $options Options
     * @return boolean false
     */
    public function start($pageId = null, array $options = array())
    {
        if ($this->_pageId !== null) {
            throw new RuntimeException("Capturing already stated with page id '{$this->_pageId}'");
        }

        if (isset($options['tags'])) {
            $this->setTags($options['tags']);
            unset($options['tags']);
        }

        if ($this->getTags() && !$this->getTagStorage()) {
            throw new RuntimeException('Tags are defined but missing a tag storage');
        }

        if (($pageId = (string)$pageId) === '') {
            $pageId = $this->_detectPageId();
        }

        ob_start(array($this, '_flush'));
        ob_implicit_flush(false);
        $this->_pageId = $pageId;

        return false;
    }

    /**
     * Get from cache
     *
     * @param null|string $pageId
     * @param array $options
     * @return bool|string
     * @throws RuntimeException
     */
    public function get($pageId = null, array $options = array())
    {
        if (($pageId = (string)$pageId) === '') {
            $pageId = $this->_detectPageId();
        }

        $file = $this->getPublicDir()
              . DIRECTORY_SEPARATOR . $this->_pageId2Path($pageId)
              . DIRECTORY_SEPARATOR . $this->_pageId2Filename($pageId);

        if (file_exists($file)) {
            $content = @file_get_contents($file);
            if ($content === false) {
                throw new RuntimeException("Failed to read cached pageId '{$pageId}': {$lastErr['message']}");
            }
            return $content;
        }

        return false;
    }

    /**
     * Checks if a cache with given id exists
     *
     * @param null|string $pageId
     * @param array $options
     * @return bool
     */
    public function exists($pageId = null, array $options = array())
    {
        if (($pageId = (string)$pageId) === '') {
            $pageId = $this->_detectPageId();
        }

        $file = $this->getPublicDir()
              . DIRECTORY_SEPARATOR . $this->_pageId2Path($pageId)
              . DIRECTORY_SEPARATOR . $this->_pageId2Filename($pageId);

        return file_exists($file);
    }

    /**
     * Remove from cache
     *
     * @param null|string $pageId
     * @param array $options
     * @throws RuntimeException
     * @return void
     */
    public function remove($pageId = null, array $options = array())
    {
        if (($pageId = (string)$pageId) === '') {
            $pageId = $this->_detectPageId();
        }

        $file = $this->getPublicDir()
              . DIRECTORY_SEPARATOR . $this->_pageId2Path($pageId)
              . DIRECTORY_SEPARATOR . $this->_pageId2Filename($pageId);

        if (file_exists($file)) {
            if (!@unlink($file)) {
                $lastErr = error_get_last();
                throw new RuntimeException("Failed to remove cached pageId '{$pageId}': {$lastErr['message']}");
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
    protected function _detectPageId()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Get filename for page id
     *
     * @param string $pageId
     * @return string
     */
    protected function _pageId2Filename($pageId)
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
    protected function _pageId2Path($pageId)
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
    protected function _flush($output)
    {
        $this->_save($output);

        // http://php.net/manual/function.ob-start.php
        // -> If output_callback  returns FALSE original input is sent to the browser.
        return false;
    }

    /**
     * Save the cache
     *
     * @param  $output
     * @throws RuntimeException
     */
    protected function _save($output)
    {
        $path     = $this->_pageId2Path($this->_pageId);
        $fullPath = $this->getPublicDir() . DIRECTORY_SEPARATOR . $path;
        if (!file_exists($fullPath)) {
            $oldUmask = umask($this->getDirectoryUmask());
            if (!@mkdir($fullPath, 0777, true)) {
                $lastErr = error_get_last();
                throw new RuntimeException(
                    "Can't create directory '{$fullPath}': {$lastErr['message']}"
                );
            }
        }

        if ($oldUmask !== null) { // $oldUmask could be set on create directory
            umask($this->getFileUmask());
        } else {
            $oldUmask = umask($this->getFileUmask());
        }
        $file = $path . DIRECTORY_SEPARATOR . $this->_pageId2Filename($this->_pageId);
        $fullFile = $this->getPublicDir() . DIRECTORY_SEPARATOR . $file;
        $this->_putFileContent($fullFile, $output);

        $tagStorage = $this->getTagStorage();
        if ($tagStorage) {
            $tagKey     = $this->getTagKey();
            $tagIndex = $tagStorage->getTagStorage()->getItem($tagKey);
            if (!$tagIndex) {
                $tagIndex = null;
            }

            if ($this->_tags) {
                $tagIndex[$file] = &$this->_tags;
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
     * @throws RuntimeException
     */
    protected function _putFileContent($file, $data)
    {
        $flags = FILE_BINARY; // since PHP 6 but defined as 0 in PHP 5.3
        if ($this->getFileLocking()) {
            $flags = $flags | LOCK_EX;
        }

        $put = @file_put_contents($file, $data, $flags);
        if ( $put < strlen((binary)$data) ) {
            $lastErr = error_get_last();
            @unlink($file); // remove old or incomplete written file
            throw new RuntimeException($lastErr['message']);
        }
    }

}
