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
    Zend\Cache\StorageFactory,
    Zend\Cache\Storage\StorageInterface as Storage,
    Zend\Stdlib\AbstractOptions;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Pattern
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PatternOptions extends AbstractOptions
{
    /**
     * Used by:
     * - ClassCache
     * - ObjectCache
     * @var bool
     */
    protected $cacheByDefault = true;

    /**
     * Used by:
     * - CallbackCache
     * - ClassCache
     * - ObjectCache
     * @var bool
     */
    protected $cacheOutput = true;

    /**
     * Used by:
     * - ClassCache
     * @var null|string
     */
    protected $class;

    /**
     * Used by:
     * - ClassCache
     * @var array
     */
    protected $classCacheMethods = array();

    /**
     * Used by:
     * - ClassCache
     * @var array
     */
    protected $classNonCacheMethods = array();

    /**
     * Used by:
     * - CaptureCache
     * @var int
     */
    protected $dirUmask = 0007;

    /**
     * Used by:
     * - CaptureCache
     * @var bool
     */
    protected $fileLocking = true;

    /**
     * Used by:
     * - CaptureCache
     * @var int
     */
    protected $fileUmask = 0117;

    /**
     * Used by:
     * - CaptureCache
     * @var string
     */
    protected $indexFilename = 'index.html';

    /**
     * Used by:
     * - ObjectCache
     * @var null|object
     */
    protected $object;

    /**
     * Used by:
     * - ObjectCache
     * @var bool
     */
    protected $objectCacheMagicProperties = false;

    /**
     * Used by:
     * - ObjectCache
     * @var array
     */
    protected $objectCacheMethods = array();

    /**
     * Used by:
     * - ObjectCache
     * @var null|string
     */
    protected $objectKey;

    /**
     * Used by:
     * - ObjectCache
     * @var array
     */
    protected $objectNonCacheMethods = array('__tostring');

    /**
     * Used by:
     * - CaptureCache
     * @var null|string
     */
    protected $publicDir;

    /**
     * Used by:
     * - CallbackCache
     * - ClassCache
     * - ObjectCache
     * - OutputCache
     * @var null|Storage
     */
    protected $storage;

    /**
     * Used by:
     * - CaptureCache
     * @var string
     */
    protected $tagKey = 'ZendCachePatternCaptureCache_Tags';

    /**
     * Used by:
     * - CaptureCache
     * @var array
     */
    protected $tags = array();

    /**
     * Used by:
     * - CaptureCache
     * @var null|Storage
     */
    protected $tagStorage;

    /**
     * Set flag indicating whether or not to cache by default
     *
     * Used by:
     * - ClassCache
     * - ObjectCache
     *
     * @param  bool $cacheByDefault
     * @return PatternOptions
     */
    public function setCacheByDefault($cacheByDefault)
    {
        $this->cacheByDefault = $cacheByDefault;
        return $this;
    }

    /**
     * Do we cache by default?
     *
     * Used by:
     * - ClassCache
     * - ObjectCache
     *
     * @return bool
     */
    public function getCacheByDefault()
    {
        return $this->cacheByDefault;
    }

    /**
     * Set whether or not to cache output
     *
     * Used by:
     * - CallbackCache
     * - ClassCache
     * - ObjectCache
     *
     * @param  bool $cacheOutput
     * @return PatternOptions
     */
    public function setCacheOutput($cacheOutput)
    {
        $this->cacheOutput = (bool) $cacheOutput;
        return $this;
    }

    /**
     * Will we cache output?
     *
     * Used by:
     * - CallbackCache
     * - ClassCache
     * - ObjectCache
     *
     * @return bool
     */
    public function getCacheOutput()
    {
        return $this->cacheOutput;
    }

    /**
     * Set class name
     *
     * Used by:
     * - ClassCache
     *
     * @param  string $class
     * @return PatternOptions
     */
    public function setClass($class)
    {
        if (!is_string($class)) {
            throw new Exception\InvalidArgumentException('Invalid classname provided; must be a string');
        }
        $this->class = $class;
        return $this;
    }

    /**
     * Get class name
     *
     * Used by:
     * - ClassCache
     *
     * @return null|string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set list of method return values to cache
     *
     * Used by:
     * - ClassCache
     *
     * @param  array $classCacheMethods
     * @return PatternOptions
     */
    public function setClassCacheMethods(array $classCacheMethods)
    {
        $this->classCacheMethods = $this->recursiveStrtolower($classCacheMethods);
        return $this;
    }

    /**
     * Get list of methods from which to cache return values
     *
     * Used by:
     * - ClassCache
     *
     * @return array
     */
    public function getClassCacheMethods()
    {
        return $this->classCacheMethods;
    }

    /**
     * Set list of method return values NOT to cache
     *
     * Used by:
     * - ClassCache
     *
     * @param  array $classNonCacheMethods
     * @return PatternOptions
     */
    public function setClassNonCacheMethods(array $classNonCacheMethods)
    {
        $this->classNonCacheMethods = $this->recursiveStrtolower($classNonCacheMethods);
        return $this;
    }

    /**
     * Get list of methods from which NOT to cache return values
     *
     * Used by:
     * - ClassCache
     *
     * @return array
     */
    public function getClassNonCacheMethods()
    {
        return $this->classNonCacheMethods;
    }

    /**
     * Set directory permissions
     *
     * Sets {@link $dirUmask} property to inverse of provided value.
     *
     * @param  string $dirPerm
     * @return PatternOptions
     */
    public function setDirPerm($dirPerm)
    {
        if (is_string($dirPerm)) {
            $dirPerm = octdec($dirPerm);
        } else {
            $dirPerm = (int) $dirPerm;
        }

        // use umask
        return $this->setDirUmask(~$dirPerm);
    }

    /**
     * Gets directory permissions
     *
     * Proxies to {@link $dirUmask} property, returning its inverse.
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
     * Used by:
     * - CaptureCache
     *
     * @param  int $dirUmask
     * @return PatternOptions
     */
    public function setDirUmask($dirUmask)
    {
        $dirUmask = $this->normalizeUmask($dirUmask, function($umask) {
            if ((~$umask & 0700) != 0700 ) {
                throw new Exception\InvalidArgumentException(
                    'Invalid directory umask or directory permissions: '
                    . 'need permissions to execute, read and write directories by owner'
                );
            }
        });
        $this->dirUmask = $dirUmask;
        return $this;
    }

    /**
     * Get directory umask
     *
     * Used by:
     * - CaptureCache
     *
     * @return int
     */
    public function getDirUmask()
    {
        return $this->dirUmask;
    }

    /**
     * Set whether or not file locking should be used
     *
     * Used by:
     * - CaptureCache
     *
     * @param  bool $fileLocking
     * @return PatternOptions
     */
    public function setFileLocking($fileLocking)
    {
        $this->fileLocking = (bool) $fileLocking;
        return $this;
    }

    /**
     * Is file locking enabled?
     *
     * Used by:
     * - CaptureCache
     *
     * @return bool
     */
    public function getFileLocking()
    {
        return $this->fileLocking;
    }

    /**
     * Set file permissions
     *
     * Sets {@link $fileUmask} property to inverse of provided value.
     *
     * @param  string $filePerm
     * @return PatternOptions
     */
    public function setFilePerm($filePerm)
    {
        if (is_string($filePerm)) {
            $filePerm = octdec($filePerm);
        } else {
            $filePerm = (int) $filePerm;
        }

        // use umask
        return $this->setFileUmask(~$filePerm);
    }

    /**
     * Gets file permissions
     *
     * Proxies to {@link $fileUmask} property, returning its inverse.
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
     * Used by:
     * - CaptureCache
     *
     * @param  int $fileUmask
     * @return PatternOptions
     */
    public function setFileUmask($fileUmask)
    {
        $fileUmask = $this->normalizeUmask($fileUmask, function($umask) {
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
        $this->fileUmask = $fileUmask;
        return $this;
    }

    /**
     * Get file umask
     *
     * Used by:
     * - CaptureCache
     *
     * @return int
     */
    public function getFileUmask()
    {
        return $this->fileUmask;
    }

    /**
     * Set value for index filename
     *
     * @param  string $indexFilename
     * @return PatternOptions
     */
    public function setIndexFilename($indexFilename)
    {
        $this->indexFilename = (string) $indexFilename;
        return $this;
    }

    /**
     * Get value for index filename
     *
     * @return string
     */
    public function getIndexFilename()
    {
        return $this->indexFilename;
    }

    /**
     * Set object to cache
     *
     * @param  mixed $value
     * @return $this
     */
    public function setObject($object)
    {
        if (!is_object($object)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an object; received "%s"', __METHOD__, gettype($object)
            ));
        }
        $this->object = $object;
        return $this;
    }

    /**
     * Get object to cache
     *
     * @return null|object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set flag indicating whether or not to cache magic properties
     *
     * Used by:
     * - ObjectCache
     *
     * @param  bool $objectCacheMagicProperties
     * @return PatternOptions
     */
    public function setObjectCacheMagicProperties($objectCacheMagicProperties)
    {
        $this->objectCacheMagicProperties = (bool) $objectCacheMagicProperties;
        return $this;
    }

    /**
     * Should we cache magic properties?
     *
     * Used by:
     * - ObjectCache
     *
     * @return bool
     */
    public function getObjectCacheMagicProperties()
    {
        return $this->objectCacheMagicProperties;
    }

    /**
     * Set list of object methods for which to cache return values
     *
     * @param  array $objectCacheMethods
     * @return PatternOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setObjectCacheMethods(array $objectCacheMethods)
    {
        $this->objectCacheMethods = $this->normalizeObjectMethods($objectCacheMethods);
        return $this;
    }

    /**
     * Get list of object methods for which to cache return values
     *
     * @return array
     */
    public function getObjectCacheMethods()
    {
        return $this->objectCacheMethods;
    }

    /**
     * Set the object key part.
     *
     * Used to generate a callback key in order to speed up key generation.
     *
     * Used by:
     * - ObjectCache
     *
     * @param  mixed $value
     * @return $this
     */
    public function setObjectKey($objectKey)
    {
        if ($objectKey !== null) {
            $this->objectKey = (string) $objectKey;
        } else {
            $this->objectKey = null;
        }
        return $this;
    }

    /**
     * Get object key
     *
     * Used by:
     * - ObjectCache
     *
     * @return mixed
     */
    public function getObjectKey()
    {
        if (!$this->objectKey) {
            return get_class($this->getObject());
        }
        return $this->objectKey;
    }

    /**
     * Set list of object methods for which NOT to cache return values
     *
     * @param  array $objectNonCacheMethods
     * @return PatternOptions
     * @throws Exception\InvalidArgumentException
     */
    public function setObjectNonCacheMethods(array $objectNonCacheMethods)
    {
        $this->objectNonCacheMethods = $this->normalizeObjectMethods($objectNonCacheMethods);
        return $this;
    }

    /**
     * Get list of object methods for which NOT to cache return values
     *
     * @return array
     */
    public function getObjectNonCacheMethods()
    {
        return $this->objectNonCacheMethods;
    }

    /**
     * Set location of public directory
     *
     * Used by:
     * - CaptureCache
     *
     * @param  string $publicDir
     * @return PatternOptions
     */
    public function setPublicDir($publicDir)
    {
        $this->publicDir = (string) $publicDir;
        return $this;
    }

    /**
     * Get location of public directory
     *
     * Used by:
     * - CaptureCache
     *
     * @return null|string
     */
    public function getPublicDir()
    {
        return $this->publicDir;
    }

    /**
     * Set storage adapter
     *
     * Required for the following Pattern classes:
     * - CallbackCache
     * - ClassCache
     * - ObjectCache
     * - OutputCache
     *
     * @param  string|array|Storage $storage
     * @return PatternOptions
     */
    public function setStorage($storage)
    {
        $this->storage = $this->storageFactory($storage);
        return $this;
    }

    /**
     * Get storage adapter
     *
     * Used by:
     * - CallbackCache
     * - ClassCache
     * - ObjectCache
     * - OutputCache
     *
     * @return null|Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set tag key
     *
     * @param  string $tagKey
     * @return PatternOptions
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
     * Get tag key
     *
     * @return string
     */
    public function getTagKey()
    {
        return $this->tagKey;
    }

    /**
     * Set tags
     *
     * Used by:
     * - CaptureCache
     *
     * @param  array $tags
     * @return PatternOptions
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Get tags
     *
     * Used by:
     * - CaptureCache
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set storage adapter for tags
     *
     * Used by:
     * - CaptureCache
     *
     * @param  string|array|Storage $tagStorage
     * @return PatternOptions
     */
    public function setTagStorage($tagStorage)
    {
        $this->tagStorage = $this->storageFactory($tagStorage);
        return $this;
    }

    /**
     * Get storage adapter for tags
     *
     * Used by:
     * - CaptureCache
     *
     * @return null|Storage
     */
    public function getTagStorage()
    {
        return $this->tagStorage;
    }

    /**
     * Recursively apply strtolower on all values of an array, and return as a
     * list of unique values
     *
     * @param  array $array
     * @return array
     */
    protected function recursiveStrtolower(array $array)
    {
        return array_values(array_unique(array_map(function($value) {
            return strtolower($value);
        }, $array)));
    }

    /**
     * Normalize object methods
     *
     * Recursively casts values to lowercase, then determines if any are in a
     * list of methods not handled, raising an exception if so.
     *
     * @param  array $methods
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    protected function normalizeObjectMethods(array $methods)
    {
        $methods   = $this->recursiveStrtolower($methods);
        $intersect = array_intersect(array('__set', '__get', '__unset', '__isset'), $methods);
        if (!empty($intersect)) {
            throw new Exception\InvalidArgumentException(
                "Magic properties are handled by option 'cache_magic_properties'"
            );
        }
        return $methods;
    }

    /**
     * Normalize a umask
     *
     * Allows specifying a umask as either an octal or integer. If the umask
     * fails required permissions, raises an exception.
     *
     * @param  int|string $umask
     * @param  callable   $comparison Callback used to verify the umask is acceptable for the given purpose
     * @return int
     * @throws Exception\InvalidArgumentException
     */
    protected function normalizeUmask($umask, $comparison)
    {
        if (is_string($umask)) {
            $umask = octdec($umask);
        } else {
            $umask = (int)$umask;
        }

        $comparison($umask);

        return $umask;
    }

    /**
     * Create a storage object from a given specification
     *
     * @param  array|string|Storage $storage
     * @return StorageAdapter
     */
    protected function storageFactory($storage)
    {
        if (is_array($storage)) {
            $storage = StorageFactory::factory($storage);
        } elseif (is_string($storage)) {
            $storage = StorageFactory::adapterFactory($storage);
        } elseif ( !($storage instanceof Storage) ) {
            throw new Exception\InvalidArgumentException(
                'The storage must be an instanceof Zend\Cache\Storage\StorageInterface '
                . 'or an array passed to Zend\Cache\Storage::factory '
                . 'or simply the name of the storage adapter'
            );
        }

        return $storage;
    }
}
