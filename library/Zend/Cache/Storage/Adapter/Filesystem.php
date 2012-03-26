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

use ArrayObject,
    GlobIterator,
    stdClass,
    Exception as BaseException,
    Zend\Cache\Exception,
    Zend\Cache\Storage,
    Zend\Cache\Storage\Capabilities,
    Zend\Cache\Utils,
    Zend\Stdlib\ErrorHandler;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Filesystem extends AbstractAdapter
{
    /**
     * GlobIterator used as statement
     *
     * @var GlobIterator|null
     */
    protected $stmtGlob = null;

    /**
     * Matching mode of active statement
     *
     * @var integer|null
     */
    protected $stmtMatch = null;

    /**
     * Key (namespace + separator + key) of buffered information
     *
     * @var string|null
     */
    protected $bufferKey = null;

    /**
     * The buffered filespec
     *
     * @var string|null
     */
    protected $bufferedFilespec = null;

    /**
     * Set options.
     *
     * @param  array|Traversable|FilesystemOptions $options
     * @return Filesystem
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!$options instanceof FilesystemOptions) {
            $options = new FilesystemOptions($options);
        }

        return parent::setOptions($options);
    }

    /**
     * Get options.
     *
     * @return FilesystemOptions
     * @see setOptions()
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new FilesystemOptions());
        }
        return $this->options;
    }

    /* reading */

    /**
     * Get an item.
     *
     * Options:
     *  - ttl <int> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item or return false
     *
     * @param  string $key
     * @param  array  $options
     * @return mixed Data on success and false on failure
     * @throws Exception
     *
     * @triggers getItem.pre(PreEvent)
     * @triggers getItem.post(PostEvent)
     * @triggers getItem.exception(ExceptionEvent)
     */
    public function getItem($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getReadable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::getItem($key, $options);
    }

    /**
     * Get multiple items.
     *
     * Options:
     *  - ttl <int> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $keys
     * @param  array $options
     * @return array Associative array of existing keys and values
     * @throws Exception
     *
     * @triggers getItems.pre(PreEvent)
     * @triggers getItems.post(PostEvent)
     * @triggers getItems.exception(ExceptionEvent)
     */
    public function getItems(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getReadable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::getItems($keys, $options);
    }

    /**
     * Internal method to get an item.
     *
     * Options:
     *  - ttl <int>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *  - ignore_missing_items <boolean>
     *    - Throw exception on missing item or return false
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return mixed Data on success or false on failure
     * @throws Exception
     */
    protected function internalGetItem(& $normalizedKey, array & $normalizedOptions)
    {
        if (!$this->internalHasItem($normalizedKey, $normalizedOptions)) {
            if (!$normalizedOptions['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException(
                    "Key '{$normalizedKey}' not found within namespace '{$normalizedOptions['namespace']}'"
                );
            }

            return false;
        }

        try {
            $filespec    = $this->getFileSpec($normalizedKey, $normalizedOptions);
            $baseOptions = $this->getOptions();
            $data        = $this->getFileContent($filespec . '.dat');

            if ($baseOptions->getReadControl()) {
                if ( ($info = $this->readInfoFile($filespec . '.ifo'))
                    && isset($info['hash'], $info['algo'])
                    && Utils::generateHash($info['algo'], $data, true) != $info['hash']
                ) {
                    throw new Exception\UnexpectedValueException(
                        "ReadControl: Stored hash and computed hash don't match"
                    );
                }
            }

            if (array_key_exists('token', $normalizedOptions)) {
                // use filemtime + filesize as CAS token
                $normalizedOptions['token'] = filemtime($filespec . '.dat') . filesize($filespec . '.dat');
            }

            return $data;

        } catch (Exception $e) {
            try {
                // remove cache file on exception
                $this->internalRemoveItem($normalizedKey, $normalizedOptions);
            } catch (Exception $tmp) {
                // do not throw remove exception on this point
            }

            throw $e;
        }
    }

    /**
     * Internal method to get multiple items.
     *
     * Options:
     *  - ttl <int>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $normalizedKeys
     * @param  array $normalizedOptions
     * @return array Associative array of existing keys and values
     * @throws Exception
     */
    protected function internalGetItems(array & $normalizedKeys, array & $normalizedOptions)
    {
        $baseOptions = $this->getOptions();

        $result = array();
        while (true) {

            // LOCK_NB if more than one items have to read
            $wouldblock = count($normalizedKeys) > 1;

            // read items
            foreach ($normalizedKeys as $i => $normalizedKey) {
                if (!$this->internalHasItem($normalizedKey, $normalizedOptions)) {
                    unset($normalizedKeys[$i]);
                    continue;
                }

                $filespec = $this->getFileSpec($normalizedKey, $normalizedOptions);
                $data     = $this->getFileContent($filespec . '.dat', $wouldblock);
                if ($data === false) {
                    continue;
                } else {
                    unset($normalizedKeys[$i]);
                }

                if ($baseOptions->getReadControl()) {
                    $info = $this->readInfoFile($filespec . '.ifo');
                    if (isset($info['hash'], $info['algo'])
                        && Utils::generateHash($info['algo'], $data, true) != $info['hash']
                    ) {
                        throw new Exception\UnexpectedValueException(
                            "ReadControl: Stored hash and computed hash doesn't match"
                        );
                    }
                }

                $result[$normalizedKey] = $data;
            }

            // no more items to read
            if (!$normalizedKeys) {
                break;
            }
        }

        return $result;
    }

    /**
     * Test if an item exists.
     *
     * Options:
     *  - ttl <int> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string $key
     * @param  array  $options
     * @return boolean
     * @throws Exception
     *
     * @triggers hasItem.pre(PreEvent)
     * @triggers hasItem.post(PostEvent)
     * @triggers hasItem.exception(ExceptionEvent)
     */
    public function hasItem($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getReadable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::hasItem($key, $options);
    }

    /**
     * Test multiple items.
     *
     * Options:
     *  - ttl <int> optional
     *    - The time-to-life (Default: ttl of object)
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $keys
     * @param  array $options
     * @return array Array of existing keys
     * @throws Exception
     *
     * @triggers hasItems.pre(PreEvent)
     * @triggers hasItems.post(PostEvent)
     * @triggers hasItems.exception(ExceptionEvent)
     */
    public function hasItems(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getReadable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::hasItems($keys, $options);
    }

    /**
     * Internal method to test if an item exists.
     *
     * Options:
     *  - ttl <int>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception
     */
    protected function internalHasItem(& $normalizedKey, array & $normalizedOptions)
    {
        $ttl      = $normalizedOptions['ttl'];
        $filespec = $this->getFileSpec($normalizedKey, $normalizedOptions);

        if (!file_exists($filespec . '.dat')) {
            return false;
        }

        if ($ttl) {
            ErrorHandler::start();
            $mtime = filemtime($filespec . '.dat');
            $error = ErrorHandler::stop();
            if (!$mtime) {
                throw new Exception\RuntimeException(
                    "Error getting mtime of file '{$filespec}.dat'", 0, $error
                );
            }

            if (time() >= ($mtime + $ttl)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get metadata
     *
     * @param $key
     * @param array $options
     * @return array|bool|mixed|null
     */
    public function getMetadata($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getReadable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::getMetadata($key, $options);
    }

    /**
     * Get metadatas
     *
     * @param array $keys
     * @param array $options
     * @return array
     */
    public function getMetadatas(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getReadable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::getMetadatas($keys, $options);
    }

    /**
     * Get info by key
     *
     * @param string $normalizedKey
     * @param array  $normalizedOptions
     * @return array|bool
     * @throws ItemNotFoundException
     */
    protected function internalGetMetadata(& $normalizedKey, array & $normalizedOptions)
    {
        if (!$this->internalHasItem($normalizedKey, $normalizedOptions)) {
            if (!$normalizedOptions['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException(
                    "Key '{$normalizedKey}' not found on namespace '{$normalizedOptions['namespace']}'"
                );
            }

            return false;
        }

        $baseOptions = $this->getOptions();
        $filespec    = $this->getFileSpec($normalizedKey, $normalizedOptions);

        $metadata = $this->readInfoFile($filespec . '.ifo');
        if (!$metadata) {
            $metadata  = array();
        }

        $metadata['filespec'] = $filespec;
        $metadata['mtime']    = filemtime($filespec . '.dat');

        if (!$baseOptions->getNoCtime()) {
            $metadata['ctime'] = filectime($filespec . '.dat');
        }

        if (!$baseOptions->getNoAtime()) {
            $metadata['atime'] = fileatime($filespec . '.dat');
        }

        return $metadata;
    }

    /* writing */

    /**
     * Store an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception
     *
     * @triggers setItem.pre(PreEvent)
     * @triggers setItem.post(PostEvent)
     * @triggers setItem.exception(ExceptionEvent)
     */
    public function setItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getWritable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::setItem($key, $value, $options);
    }

    /**
     * Store multiple items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers setItems.pre(PreEvent)
     * @triggers setItems.post(PostEvent)
     * @triggers setItems.exception(ExceptionEvent)
     */
    public function setItems(array $keyValuePairs, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getWritable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::setItems($keyValuePairs, $options);
    }

    /**
     * Add an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception
     *
     * @triggers addItem.pre(PreEvent)
     * @triggers addItem.post(PostEvent)
     * @triggers addItem.exception(ExceptionEvent)
     */
    public function addItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getWritable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::addItem($key, $value, $options);
    }

    /**
     * Add multiple items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers addItems.pre(PreEvent)
     * @triggers addItems.post(PostEvent)
     * @triggers addItems.exception(ExceptionEvent)
     */
    public function addItems(array $keyValuePairs, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getWritable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::addItems($keyValuePairs, $options);
    }

    /**
     * Replace an existing item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception
     *
     * @triggers replaceItem.pre(PreEvent)
     * @triggers replaceItem.post(PostEvent)
     * @triggers replaceItem.exception(ExceptionEvent)
     */
    public function replaceItem($key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getWritable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::replaceItem($key, $value, $options);
    }

    /**
     * Replace multiple existing items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  array $keyValuePairs
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers replaceItems.pre(PreEvent)
     * @triggers replaceItems.post(PostEvent)
     * @triggers replaceItems.exception(ExceptionEvent)
     */
    public function replaceItems(array $keyValuePairs, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getWritable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::replaceItems($keyValuePairs, $options);
    }

    /**
     * Internal method to store an item.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *  - tags <array>
     *    - An array of tags
     *
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception
     */
    protected function internalSetItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
        $baseOptions = $this->getOptions();
        $filespec    = $this->getFileSpec($normalizedKey, $normalizedOptions);
        $oldUmask    = null;

        if ($baseOptions->getDirLevel() > 0) {
            $path = dirname($filespec);
            if (!file_exists($path)) {
                $oldUmask = umask($baseOptions->getDirUmask());
                ErrorHandler::start();
                $mkdir = mkdir($path, 0777, true);
                $error = ErrorHandler::stop();
                if (!$mkdir) {
                    umask($oldUmask);
                    throw new Exception\RuntimeException(
                        "Error creating directory '{$path}'", 0, $error
                    );
                }
            }
        }

        $info = null;
        if ($baseOptions->getReadControl()) {
            $info['hash'] = Utils::generateHash($baseOptions->getReadControlAlgo(), $value, true);
            $info['algo'] = $baseOptions->getReadControlAlgo();
        }

        if (isset($options['tags']) && $normalizedOptions['tags']) {
            $tags = $normalizedOptions['tags'];
            if (!is_array($tags)) {
                $tags = array($tags);
            }
            $info['tags'] = array_values(array_unique($tags));
        }

        try {
            if ($oldUmask !== null) { // $oldUmask could be defined on create directory
                umask($baseOptions->getFileUmask());
            } else {
                $oldUmask = umask($baseOptions->getFileUmask());
            }

            $ret = $this->putFileContent($filespec . '.dat', $value);
            if ($ret && $info) {
                // Don't throw exception if writing of info file failed
                // -> only return false
                try {
                    $ret = $this->putFileContent($filespec . '.ifo', serialize($info));
                } catch (Exception\RuntimeException $e) {
                    $ret = false;
                }
            }

            $this->bufferKey = null;

            // reset file_umask
            umask($oldUmask);

            return $ret;

        } catch (Exception $e) {
            // reset umask on exception
            umask($oldUmask);
            throw $e;
        }
    }

    /**
     * Set an item only if token matches
     *
     * It uses the token received from getItem() to check if the item has
     * changed before overwriting it.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - tags <array> optional
     *    - An array of tags
     *
     * @param  mixed  $token
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $options
     * @return boolean
     * @throws Exception
     * @see    getItem()
     * @see    setItem()
     */
    public function checkAndSetItem($token, $key, $value, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getWritable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::checkAndSetItem($token, $key, $value, $options);
    }

    /**
     * Internal method to set an item only if token matches
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *  - tags <array>
     *    - An array of tags
     *
     * @param  mixed  $token
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception
     * @see    getItem()
     * @see    setItem()
     */
    protected function internalCheckAndSetItem(& $token, & $normalizedKey, & $value, array & $normalizedOptions)
    {
        if (!$this->internalHasItem($normalizedKey, $normalizedOptions)) {
            if (!$normalizedOptions['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException(
                    "Key '{$normalizedKey}' not found within namespace '{$normalizedOptions['namespace']}'"
                );
            }

            return false;
        }

        // use filemtime + filesize as CAS token
        $filespec = $this->getFileSpec($normalizedKey, $normalizedOptions);
        $check    = filemtime($filespec . '.dat') . filesize($filespec . '.dat');
        if ($token !== $check) {
            return false;
        }

        return $this->internalSetItem($normalizedKey, $value, $normalizedOptions);
    }

    /**
     * Reset lifetime of an item
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  string $key
     * @param  array  $options
     * @return boolean
     * @throws Exception
     *
     * @triggers touchItem.pre(PreEvent)
     * @triggers touchItem.post(PostEvent)
     * @triggers touchItem.exception(ExceptionEvent)
     */
    public function touchItem($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getWritable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::touchItem($key, $options);
    }

    /**
     * Reset lifetime of multiple items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *
     * @param  array $keys
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers touchItems.pre(PreEvent)
     * @triggers touchItems.post(PostEvent)
     * @triggers touchItems.exception(ExceptionEvent)
     */
    public function touchItems(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getWritable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::touchItems($keys, $options);
    }

    /**
     * Internal method to reset lifetime of an item
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  string $key
     * @param  array  $options
     * @return boolean
     * @throws Exception
     */
    protected function internalTouchItem(& $normalizedKey, array & $normalizedOptions)
    {
        if (!$this->internalHasItem($normalizedKey, $normalizedOptions)) {
            if (!$normalizedOptions['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException(
                    "Key '{$normalizedKey}' not found within namespace '{$normalizedOptions['namespace']}'"
                );
            }

            return false;
        }

        $filespec = $this->getFileSpec($normalizedKey, $normalizedOptions);

        ErrorHandler::start();
        $touch = touch($filespec . '.dat');
        $error = ErrorHandler::stop();
        if (!$touch) {
            throw new Exception\RuntimeException(
                "Error touching file '{$filespec}.dat'", 0, $error
            );
        }

        // remove the buffered info
        $this->bufferKey = null;

        return true;
    }

    /**
     * Remove an item.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item
     *
     * @param  string $key
     * @param  array  $options
     * @return boolean
     * @throws Exception
     *
     * @triggers removeItem.pre(PreEvent)
     * @triggers removeItem.post(PostEvent)
     * @triggers removeItem.exception(ExceptionEvent)
     */
    public function removeItem($key, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getWritable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::removeItem($key, $options);
    }

    /**
     * Remove multiple items.
     *
     * Options:
     *  - namespace <string> optional
     *    - The namespace to use (Default: namespace of object)
     *  - ignore_missing_items <boolean> optional
     *    - Throw exception on missing item
     *
     * @param  array $keys
     * @param  array $options
     * @return boolean
     * @throws Exception
     *
     * @triggers removeItems.pre(PreEvent)
     * @triggers removeItems.post(PostEvent)
     * @triggers removeItems.exception(ExceptionEvent)
     */
    public function removeItems(array $keys, array $options = array())
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getWritable() && $baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        return parent::removeItems($keys, $options);
    }

    /**
     * Internal method to remove an item.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *  - ignore_missing_items <boolean>
     *    - Throw exception on missing item
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return boolean
     * @throws Exception
     */
    protected function internalRemoveItem(& $normalizedKey, array & $normalizedOptions)
    {
        $filespec = $this->getFileSpec($normalizedKey, $normalizedOptions);
        if (!file_exists($filespec . '.dat')) {
            if (!$normalizedOptions['ignore_missing_items']) {
                throw new Exception\ItemNotFoundException("Key '{$normalizedKey}' with file '{$filespec}.dat' not found");
            }
        } else {
            $this->unlink($filespec . '.dat');
            $this->unlink($filespec . '.ifo');
            $this->bufferKey = null;
        }
        return true;
    }

    /* non-blocking */

    /**
     * internal method to find items.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-live
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  int   $normalizedMode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception
     * @see    fetch()
     * @see    fetchAll()
     */
    protected function internalFind(& $normalizedMode, array & $normalizedOptions)
    {
        if ($this->stmtActive) {
            throw new Exception\RuntimeException('Statement already in use');
        }

        try {
            $baseOptions = $this->getOptions();

            $prefix = $normalizedOptions['namespace'] . $baseOptions->getNamespaceSeparator();
            $find   = $baseOptions->getCacheDir()
                    . str_repeat(\DIRECTORY_SEPARATOR . $prefix . '*', $baseOptions->getDirLevel())
                    . \DIRECTORY_SEPARATOR . $prefix . '*.dat';
            $glob   = new GlobIterator($find);

            $this->stmtActive  = true;
            $this->stmtGlob    = $glob;
            $this->stmtMatch   = $normalizedMode;
            $this->stmtOptions = $normalizedOptions;
        } catch (BaseException $e) {
            throw new Exception\RuntimeException("new GlobIterator({$find}) failed", 0, $e);
        }

        return true;
    }

    /**
     * Internal method to fetch the next item from result set
     *
     * @return array|boolean The next item or false
     * @throws Exception
     */
    protected function internalFetch()
    {
        if (!$this->stmtActive) {
            return false;
        }

        if ($this->stmtGlob !== null) {
            $result = $this->fetchByGlob();

            if ($result === false) {
                // clear statement
                $this->stmtActive  = false;
                $this->stmtGlob    = null;
                $this->stmtMatch   = null;
                $this->stmtOptions = null;
            }
        } else {
            $result = parent::internalFetch();
        }

        return $result;
    }

    /* cleaning */

    /**
     * Internal method to clear items off all namespaces.
     *
     * @param  int   $normalizedMode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception
     * @see    clearByNamespace()
     */
    protected function internalClear(& $normalizedMode, array & $normalizedOptions)
    {
        return $this->clearByPrefix('', $normalizedMode, $normalizedOptions);
    }

    /**
     * Clear items by namespace.
     *
     * Options:
     *  - ttl <float>
     *    - The time-to-life
     *  - namespace <string>
     *    - The namespace to use
     *  - tags <array>
     *    - Tags to search for used with matching modes of Adapter::MATCH_TAGS_*
     *
     * @param  int   $normalizedMode Matching mode (Value of Adapter::MATCH_*)
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception
     * @see    clear()
     */
    protected function internalClearByNamespace(& $normalizedMode, array & $normalizedOptions)
    {
        $prefix = $normalizedOptions['namespace'] . $this->getOptions()->getNamespaceSeparator();
        return $this->clearByPrefix($prefix, $normalizedMode, $normalizedOptions);
    }

    /**
     * Internal method to optimize adapter storage.
     *
     * Options:
     *  - namespace <string>
     *    - The namespace to use
     *
     * @param  array $normalizedOptions
     * @return boolean
     * @throws Exception
     */
    protected function internalOptimize(array & $normalizedOptions)
    {
        $baseOptions = $this->getOptions();
        if ($baseOptions->getDirLevel()) {
            // removes only empty directories
            $this->rmDir(
                $baseOptions->getCacheDir(),
                $normalizedOptions['namespace'] . $baseOptions->getNamespaceSeparator()
            );
        }

        return true;
    }

    /* status */

    /**
     * Internal method to get capabilities of this adapter
     *
     * @return Capabilities
     */
    protected function internalGetCapabilities()
    {
        if ($this->capabilities === null) {
            $marker  = new stdClass();
            $options = $this->getOptions();

            // detect metadata
            $metadata = array('mtime', 'filespec');
            if (!$options->getNoAtime()) {
                $metadata[] = 'atime';
            }
            if (!$options->getNoCtime()) {
                $metadata[] = 'ctime';
            }

            $capabilities = new Capabilities(
                $marker,
                array(
                    'supportedDatatypes' => array(
                        'NULL'     => 'string',
                        'boolean'  => 'string',
                        'integer'  => 'string',
                        'double'   => 'string',
                        'string'   => true,
                        'array'    => false,
                        'object'   => false,
                        'resource' => false,
                    ),
                    'supportedMetadata'  => $metadata,
                    'maxTtl'             => 0,
                    'staticTtl'          => false,
                    'tagging'            => true,
                    'ttlPrecision'       => 1,
                    'expiredRead'        => true,
                    'maxKeyLength'       => 251, // 255 - strlen(.dat | .ifo)
                    'namespaceIsPrefix'  => true,
                    'namespaceSeparator' => $options->getNamespaceSeparator(),
                    'iterable'           => true,
                    'clearAllNamespaces' => true,
                    'clearByNamespace'   => true,
                )
            );

            // update capabilities on change options
            $this->events()->attach('option', function ($event) use ($capabilities, $marker) {
                $params = $event->getParams();

                if (isset($params['namespace_separator'])) {
                    $capabilities->setNamespaceSeparator($marker, $params['namespace_separator']);
                }

                if (isset($params['no_atime']) || isset($params['no_ctime'])) {
                    $metadata = $capabilities->getSupportedMetadata();

                    if (isset($params['no_atime']) && !$params['no_atime']) {
                        $metadata[] = 'atime';
                    } elseif (isset($params['no_atime']) && ($index = array_search('atime', $metadata)) !== false) {
                        unset($metadata[$index]);
                    }

                    if (isset($params['no_ctime']) && !$params['no_ctime']) {
                        $metadata[] = 'ctime';
                    } elseif (isset($params['no_ctime']) && ($index = array_search('ctime', $metadata)) !== false) {
                        unset($metadata[$index]);
                    }

                    $capabilities->setSupportedMetadata($marker, $metadata);
                }
            });

            $this->capabilityMarker = $marker;
            $this->capabilities     = $capabilities;
        }

        return $this->capabilities;
    }

    /**
     * Internal method to get storage capacity.
     *
     * @param  array $normalizedOptions
     * @return array|boolean Capacity as array or false on failure
     * @throws Exception
     */
    protected function internalGetCapacity(array & $normalizedOptions)
    {
        return Utils::getDiskCapacity($this->getOptions()->getCacheDir());
    }

    /* internal */

    /**
     * Fetch by glob
     *
     * @return array|bool
     */
    protected function fetchByGlob()
    {
        $options = $this->stmtOptions;
        $mode    = $this->stmtMatch;

        $prefix  = $options['namespace'] . $this->getOptions()->getNamespaceSeparator();
        $prefixL = strlen($prefix);

        do {
            try {
                $valid = $this->stmtGlob->valid();
            } catch (\LogicException $e) {
                // @link https://bugs.php.net/bug.php?id=55701
                // GlobIterator throws LogicException with message
                // 'The parent constructor was not called: the object is in an invalid state'
                $valid = false;
            }
            if (!$valid) {
                return false;
            }

            $item = array();
            $meta = null;

            $current = $this->stmtGlob->current();
            $this->stmtGlob->next();

            $filename = $current->getFilename();
            if ($prefix !== '') {
                if (substr($filename, 0, $prefixL) != $prefix) {
                    continue;
                }

                // remove prefix and suffix (.dat)
                $key = substr($filename, $prefixL, -4);
            } else {
                // remove suffix (.dat)
                $key = substr($filename, 0, -4);
            }

            // if MATCH_ALL mode do not check expired
            if (($mode & self::MATCH_ALL) != self::MATCH_ALL) {
                $mtime = $current->getMTime();

                // if MATCH_EXPIRED -> filter not expired items
                if (($mode & self::MATCH_EXPIRED) == self::MATCH_EXPIRED) {
                    if ( time() < ($mtime + $options['ttl']) ) {
                        continue;
                    }

                // if MATCH_ACTIVE -> filter expired items
                } else {
                    if ( time() >= ($mtime + $options['ttl']) ) {
                        continue;
                    }
                }
            }

            // check tags only if one of the tag matching mode is selected
            if (($mode & 070) > 0) {

                $meta = $this->internalGetMetadata($key, $options);

                // if MATCH_TAGS mode -> check if all given tags available in current cache
                if (($mode & self::MATCH_TAGS_AND) == self::MATCH_TAGS_AND ) {
                    if (!isset($meta['tags']) || count(array_diff($options['tags'], $meta['tags'])) > 0) {
                        continue;
                    }

                // if MATCH_NO_TAGS mode -> check if no given tag available in current cache
                } elseif( ($mode & self::MATCH_TAGS_NEGATE) == self::MATCH_TAGS_NEGATE ) {
                    if (isset($meta['tags']) && count(array_diff($options['tags'], $meta['tags'])) != count($options['tags'])) {
                        continue;
                    }

                // if MATCH_ANY_TAGS mode -> check if any given tag available in current cache
                } elseif ( ($mode & self::MATCH_TAGS_OR) == self::MATCH_TAGS_OR ) {
                    if (!isset($meta['tags']) || count(array_diff($options['tags'], $meta['tags'])) == count($options['tags'])) {
                        continue;
                    }

                }
            }

            foreach ($options['select'] as $select) {
                if ($select == 'key') {
                    $item['key'] = $key;
                } else if ($select == 'value') {
                    $item['value'] = $this->getFileContent($current->getPathname());
                } else if ($select != 'key') {
                    if ($meta === null) {
                        $meta = $this->internalGetMetadata($key, $options);
                    }
                    $item[$select] = isset($meta[$select]) ? $meta[$select] : null;
                }
            }

            return $item;
        } while (true);
    }

    /**
     * Clear by prefix
     *
     * @param $prefix
     * @param $mode
     * @param array $opts
     * @return bool
     * @throws RuntimeException
     */
    protected function clearByPrefix($prefix, $mode, array &$opts)
    {
        $baseOptions = $this->getOptions();
        if (!$baseOptions->getWritable()) {
            return false;
        }

        $ttl = $opts['ttl'];

        if ($baseOptions->getClearStatCache()) {
            clearstatcache();
        }

        try {
            $find = $baseOptions->getCacheDir()
                . str_repeat(\DIRECTORY_SEPARATOR . $prefix . '*', $baseOptions->getDirLevel())
                . \DIRECTORY_SEPARATOR . $prefix . '*.dat';
            $glob = new GlobIterator($find);
        } catch (BaseException $e) {
            throw new Exception\RuntimeException('Instantiating GlobIterator failed', 0, $e);
        }

        $time = time();

        foreach ($glob as $entry) {

            // if MATCH_ALL mode do not check expired
            if (($mode & self::MATCH_ALL) != self::MATCH_ALL) {

                $mtime = $entry->getMTime();
                if (($mode & self::MATCH_EXPIRED) == self::MATCH_EXPIRED) {
                    if ( $time <= ($mtime + $ttl) ) {
                        continue;
                    }

                // if Zend_Cache::MATCH_ACTIVE mode selected do not remove expired data
                } else {
                    if ( $time >= ($mtime + $ttl) ) {
                        continue;
                    }
                }
            }

            // remove file suffix (*.dat)
            $pathnameSpec = substr($entry->getPathname(), 0, -4);

            ////////////////////////////////////////
            // on this time all expire tests match
            ////////////////////////////////////////

            // check tags only if one of the tag matching mode is selected
            if (($mode & 070) > 0) {

                $info = $this->readInfoFile($pathnameSpec . '.ifo');

                // if MATCH_TAGS mode -> check if all given tags available in current cache
                if (($mode & self::MATCH_TAGS) == self::MATCH_TAGS ) {
                    if (!isset($info['tags'])
                        || count(array_diff($opts['tags'], $info['tags'])) > 0
                    ) {
                        continue;
                    }

                // if MATCH_NO_TAGS mode -> check if no given tag available in current cache
                } elseif(($mode & self::MATCH_NO_TAGS) == self::MATCH_NO_TAGS) {
                    if (isset($info['tags'])
                        && count(array_diff($opts['tags'], $info['tags'])) != count($opts['tags'])
                    ) {
                        continue;
                    }

                // if MATCH_ANY_TAGS mode -> check if any given tag available in current cache
                } elseif ( ($mode & self::MATCH_ANY_TAGS) == self::MATCH_ANY_TAGS ) {
                    if (!isset($info['tags'])
                        || count(array_diff($opts['tags'], $info['tags'])) == count($opts['tags'])
                    ) {
                        continue;
                    }
                }
            }

            ////////////////////////////////////////
            // on this time all tests match
            ////////////////////////////////////////

            $this->unlink($pathnameSpec . '.dat'); // delete data file
            $this->unlink($pathnameSpec . '.ifo'); // delete info file
        }

        return true;
    }

    /**
     * Removes directories recursive by namespace
     *
     * @param  string $dir    Directory to delete
     * @param  string $prefix Namespace + Separator
     * @return bool
     */
    protected function rmDir($dir, $prefix)
    {
        $glob = glob(
            $dir . \DIRECTORY_SEPARATOR . $prefix  . '*',
            \GLOB_ONLYDIR | \GLOB_NOESCAPE | \GLOB_NOSORT
        );
        if (!$glob) {
            // On some systems glob returns false even on empty result
            return true;
        }

        $ret = true;
        foreach ($glob as $subdir) {
            // skip removing current directory if removing of sub-directory failed
            if ($this->rmDir($subdir, $prefix)) {
                // ignore not empty directories
                ErrorHandler::start();
                $ret = rmdir($subdir) && $ret;
                ErrorHandler::stop();
            } else {
                $ret = false;
            }
        }

        return $ret;
    }

    /**
     * Get file spec of the given key and namespace
     *
     * @param  string $normalizedKey
     * @param  array  $normalizedOptions
     * @return string
     */
    protected function getFileSpec($normalizedKey, array & $normalizedOptions)
    {
        $baseOptions = $this->getOptions();
        $prefix      = $normalizedOptions['namespace'] . $baseOptions->getNamespaceSeparator();
        $bufferKey   = $prefix . $normalizedKey;

        if ($this->bufferKey !== $bufferKey) {
            $path  = $baseOptions->getCacheDir();
            $level = $baseOptions->getDirLevel();
            if ( $level > 0 ) {
                // create up to 256 directories per directory level
                $hash = md5($normalizedKey);
                for ($i = 0, $max = ($level * 2); $i < $max; $i+= 2) {
                    $path .= \DIRECTORY_SEPARATOR . $prefix . $hash[$i] . $hash[$i+1];
                }
            }

            $this->bufferedFilespec = $path . \DIRECTORY_SEPARATOR . $prefix . $normalizedKey;
        }

        return $this->bufferedFilespec;
    }

    /**
     * Read info file
     *
     * @param string   $file
     * @return array|boolean The info array or false if file wasn't found
     * @throws Exception\RuntimeException
     */
    protected function readInfoFile($file)
    {
        if (!file_exists($file)) {
            return false;
        }

        $content = $this->getFileContent($file);

        ErrorHandler::start();
        $ifo = unserialize($content);
        $err = ErrorHandler::stop();
        if (!is_array($ifo)) {
            throw new Exception\RuntimeException(
                "Corrupted info file '{$file}'", 0, $err
            );
        }

        return $ifo;
    }

    /**
     * Read a complete file
     *
     * @param  string  $file File complete path
     * @param  boolean $wouldblock Return FALSE if the lock would block
     * @return string|boolean
     * @throws Exception\RuntimeException
     */
    protected function getFileContent($file, $wouldblock = false)
    {
        $locking = $this->getOptions()->getFileLocking();

        ErrorHandler::start();

        // if file locking enabled -> file_get_contents can't be used
        if ($locking) {
            $fp = fopen($file, 'rb');
            if ($fp === false) {
                $err = ErrorHandler::stop();
                throw new Exception\RuntimeException(
                    "Error opening file '{$file}'", 0, $err
                );
            }

            if ($wouldblock) {
                $wouldblock = null;
                $lock = flock($fp, \LOCK_SH | \LOCK_NB, $wouldblock);
                if ($wouldblock) {
                    fclose($fp);
                    ErrorHandler::stop();
                    return false;
                }
            } else {
                $lock = flock($fp, \LOCK_SH);
            }

            if (!$lock) {
                fclose($fp);
                $err = ErrorHandler::stop();
                throw new Exception\RuntimeException(
                    "Error locking file '{$file}'", 0, $err
                );
            }

            $res = stream_get_contents($fp);
            if ($res === false) {
                flock($fp, \LOCK_UN);
                fclose($fp);
                $err = ErrorHandler::stop();
                throw new Exception\RuntimeException(
                    'Error getting stream contents', 0, $err
                );
            }

            flock($fp, \LOCK_UN);
            fclose($fp);

        // if file locking disabled -> file_get_contents can be used
        } else {
            $res = file_get_contents($file, false);
            if ($res === false) {
                $err = ErrorHandler::stop();
                throw new Exception\RuntimeException(
                    "Error getting file contents for file '{$file}'", 0, $err
                );
            }
        }

        ErrorHandler::stop();
        return $res;
    }

    /**
     * Write content to a file
     *
     * @param  string  $file       File complete path
     * @param  string  $data       Data to write
     * @param  boolean $wouldblock Return FALSE if the lock would block
     * @return boolean TRUE on success, FALSE if lock would block
     * @throws Exception\RuntimeException
     */
    protected function putFileContent($file, $data, $wouldblock = false)
    {
        $locking    = $this->getOptions()->getFileLocking();
        $wouldblock = $locking && $wouldblock;

        ErrorHandler::start();

        // file_put_contents can't used
        if ($locking && $wouldblock) {
            $fp = fopen($file, 'cb');
            if (!$fp) {
                $err = ErrorHandler::stop();
                throw new Exception\RuntimeException(
                    "Error opening file '{$file}'", 0, $err
                );
            }

            $wouldblock = null;
            if(!flock($fp, \LOCK_EX | \LOCK_NB, $wouldblock)) {
                fclose($fp);
                $err = ErrorHandler::stop();
                if ($wouldblock) {
                    return false;
                } else {
                    throw new Exception\RuntimeException("Error locking file '{$file}'", 0, $err);
                }
            }

            if (!fwrite($fp, $data)) {
                flock($fp, \LOCK_UN);
                fclose($fp);
                $err = ErrorHandler::stop();
                throw new Exception\RuntimeException("Error writing file '{$file}'", 0, $err);
            }

            if (!ftruncate($fp, strlen($data))) {
                flock($fp, \LOCK_UN);
                fclose($fp);
                $err = ErrorHandler::stop();
                throw new Exception\RuntimeException("Error truncating file '{$file}'", 0, $err);
            }

            flock($fp, \LOCK_UN);
            fclose($fp);

        // file_put_contents can be used
        } else {
            $flags = 0;
            if ($locking) {
                $flags = $flags | \LOCK_EX;
            }

            $bytes = strlen($data);
            if (file_put_contents($file, $data, $flags) !== $bytes) {
                $err = ErrorHandler::stop();
                throw new Exception\RuntimeException(
                    "Error putting {$bytes} bytes to file '{$file}'", 0, $err
                );
            }
        }

        ErrorHandler::stop();
        return true;
    }

    /**
     * Unlink a file
     *
     * @param string $file
     * @return void
     * @throw RuntimeException
     */
    protected function unlink($file)
    {
        // If file does not exist, nothing to do
        if (!file_exists($file)) {
            return;
        }

        ErrorHandler::start();
        $res = unlink($file);
        $err = ErrorHandler::stop();

        // only throw exception if file still exists after deleting
        if (!$res && file_exists($file)) {
            throw new Exception\RuntimeException(
                "Error unlinking file '{$file}'; file still exists", 0, $err
            );
        }
    }
}
