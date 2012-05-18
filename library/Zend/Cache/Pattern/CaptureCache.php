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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Pattern;

use Zend\Cache\Exception,
    Zend\Cache\Storage\Adapter\AdapterInterface as StorageAdapter;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Pattern
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class CaptureCache extends AbstractPattern
{
    /**
     * Page identifier
     *
     * @var null|string
     */
    protected $pageId = null;

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

        $classOptions = $this->getOptions();

        if (isset($options['tags'])) {
            $classOptions->setTags($options['tags']);
            unset($options['tags']);
        }

        if ($classOptions->getTags() && !$classOptions->getTagStorage()) {
            throw new Exception\RuntimeException('Tags are defined but missing a tag storage');
        }

        if (($pageId = (string) $pageId) === '') {
            $pageId = $this->detectPageId();
        }

        ob_start(array($this, 'flush'));
        ob_implicit_flush(false);
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
        if (($pageId = (string) $pageId) === '') {
            $pageId = $this->detectPageId();
        }

        $file = $this->getOptions()->getPublicDir()
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
        if (($pageId = (string) $pageId) === '') {
            $pageId = $this->detectPageId();
        }

        $file = $this->getOptions()->getPublicDir()
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

        $file = $this->getOptions()->getPublicDir()
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
            $filename = $this->getOptions()->getIndexFilename();
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
        $options  = $this->getOptions();
        $path     = $this->pageId2Path($this->pageId);
        $fullPath = $options->getPublicDir() . DIRECTORY_SEPARATOR . $path;
        if (!file_exists($fullPath)) {
            $oldUmask = umask($options->getDirUmask());
            if (!@mkdir($fullPath, 0777, true)) {
                $lastErr = error_get_last();
                throw new Exception\RuntimeException(
                    "Can't create directory '{$fullPath}': {$lastErr['message']}"
                );
            }
        }

        if ($oldUmask !== null) { // $oldUmask could be set on create directory
            umask($options->getFileUmask());
        } else {
            $oldUmask = umask($options->getFileUmask());
        }
        $file     = $path . DIRECTORY_SEPARATOR . $this->pageId2Filename($this->pageId);
        $fullFile = $options->getPublicDir() . DIRECTORY_SEPARATOR . $file;
        $this->putFileContent($fullFile, $output);

        $tagStorage = $options->getTagStorage();
        if ($tagStorage) {
            $tagKey     = $options->getTagKey();
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
                $tagStorage->setItem($tagKey, $tagIndex);
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
        if ($this->getOptions()->getFileLocking()) {
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
