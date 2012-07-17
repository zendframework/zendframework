<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace Zend\Cache\Pattern;

use Zend\Cache\Exception;
use Zend\Stdlib\ErrorHandler;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Pattern
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
            ErrorHandler::start(E_WARNING);
            $content = file_get_contents($file);
            ErrorHandler::stop();
            if ($content === false) {
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
        $options   = $this->getOptions();
        $publicDir = $options->getPublicDir();
        $path      = $this->pageId2Path($this->pageId);
        $file      = $path . \DIRECTORY_SEPARATOR . $this->pageId2Filename($this->pageId);

        $this->createDirectoryStructure($publicDir . \DIRECTORY_SEPARATOR . $path);
        $this->putFileContent($publicDir . \DIRECTORY_SEPARATOR . $file, $output);

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
     * @param  string  $file File complete path
     * @param  string  $data Data to write
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function putFileContent($file, $data)
    {
        $options = $this->getOptions();
        $locking = $options->getFileLocking();
        $perm    = $options->getFilePermission();
        $umask   = $options->getUmask();
        if ($umask !== false && $perm !== false) {
            $perm = $perm & ~$umask;
        }

        ErrorHandler::start();

        $umask = ($umask !== false) ? umask($umask) : false;
        $rs    = file_put_contents($file, $data, $locking ? \LOCK_EX : 0);
        if ($umask) {
            umask($umask);
        }

        if ($rs === false) {
            $err = ErrorHandler::stop();
            throw new Exception\RuntimeException(
                "Error writing file '{$file}'", 0, $err
            );
        }

        if ($perm !== false && !chmod($file, $perm)) {
            $oct = decoct($perm);
            $err = ErrorHandler::stop();
            throw new Exception\RuntimeException("chmod('{$file}', 0{$oct}) failed", 0, $err);
        }

        ErrorHandler::stop();
    }

    /**
     * Creates directory if not already done.
     *
     * @param string $pathname
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function createDirectoryStructure($pathname)
    {
        // Directory structure already exists
        if (file_exists($pathname)) {
            return;
        }

        $options = $this->getOptions();
        $perm    = $options->getDirPermission();
        $umask   = $options->getUmask();
        if ($umask !== false && $perm !== false) {
            $perm = $perm & ~$umask;
        }

        ErrorHandler::start();

        if ($perm === false) {
            // build-in mkdir function is enough

            $umask = ($umask !== false) ? umask($umask) : false;
            $res   = mkdir($pathname, ($perm !== false) ? $perm : 0777, true);

            if ($umask !== false) {
                umask($umask);
            }

            if (!$res) {
                $oct = ($perm === false) ? '777' : decoct($perm);
                $err = ErrorHandler::stop();
                throw new Exception\RuntimeException(
                    "mkdir('{$pathname}', 0{$oct}, true) failed", 0, $err
                );
            }

            if ($perm !== false && !chmod($pathname, $perm)) {
                $oct = decoct($perm);
                $err = ErrorHandler::stop();
                throw new Exception\RuntimeException(
                    "chmod('{$pathname}', 0{$oct}) failed", 0, $err
                );
            }

        } else {
            // build-in mkdir function sets permission together with current umask
            // which doesn't work well on multo threaded webservers
            // -> create directories one by one and set permissions

            // find existing path and missing path parts
            $parts = array();
            $path  = $pathname;
            while (!file_exists($path)) {
                array_unshift($parts, basename($path));
                $nextPath = dirname($path);
                if ($nextPath === $path) {
                    break;
                }
                $path = $nextPath;
            }

            // make all missing path parts
            foreach ($parts as $part) {
                $path.= \DIRECTORY_SEPARATOR . $part;

                // create a single directory, set and reset umask immediatly
                $umask = ($umask !== false) ? umask($umask) : false;
                $res   = mkdir($path, ($perm === false) ? 0777 : $perm, false);
                if ($umask !== false) {
                    umask($umask);
                }

                if (!$res) {
                    $oct = ($perm === false) ? '777' : decoct($perm);
                    $err = ErrorHandler::stop();
                    throw new Exception\RuntimeException(
                        "mkdir('{$path}', 0{$oct}, false) failed"
                    );
                }

                if ($perm !== false && !chmod($path, $perm)) {
                    $oct = decoct($perm);
                    $err = ErrorHandler::stop();
                    throw new Exception\RuntimeException(
                        "chmod('{$path}', 0{$oct}) failed"
                    );
                }
            }
        }

        ErrorHandler::stop();
    }
}
