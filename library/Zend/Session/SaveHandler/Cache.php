<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace Zend\Session\SaveHandler;

use Zend\Cache\Storage\ClearExpiredInterface as ClearExpiredCacheStorage;
use Zend\Cache\Storage\StorageInterface as CacheStorage;

/**
 * Cache session save handler
 *
 * @category   Zend
 * @package    Zend_Session
 * @subpackage SaveHandler
 */
class Cache implements SaveHandlerInterface
{
    /**
     * Session Save Path
     *
     * @var string
     */
    protected $sessionSavePath;

    /**
     * Session Name
     *
     * @var string
     */
    protected $sessionName;

    /**
     * The cache storage
     * @var CacheStorage
     */
    protected $cacheStorage;

    /**
     * Constructor
     *
     * @param  CacheStorage $cacheStorage
     */
    public function __construct(CacheStorage $cacheStorage)
    {
        $this->setCacheStorage($cacheStorage);
    }

    /**
     * Open Session
     *
     * @param string $savePath
     * @param string $name
     * @return boolean
     */
    public function open($savePath, $name)
    {
        // @todo figure out if we want to use these
        $this->sessionSavePath = $savePath;
        $this->sessionName     = $name;

        return true;
    }

    /**
     * Close session
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id
     * @return string
     */
    public function read($id)
    {
        return $this->getCacheStorge()->getItem($id);
    }

    /**
     * Write session data
     *
     * @param string $id
     * @param string $data
     * @return boolean
     */
    public function write($id, $data)
    {
        return $this->getCacheStorge()->setItem($id, $data);
    }

    /**
     * Destroy session
     *
     * @param string $id
     * @return boolean
     */
    public function destroy($id)
    {
        return $this->getCacheStorge()->removeItem($id);
    }

    /**
     * Garbage Collection
     *
     * @param int $maxlifetime
     * @return boolean
     */
    public function gc($maxlifetime)
    {
        $cache = $this->getCacheStorge();
        if ($cache instanceof ClearExpiredCacheStorage) {
            return $cache->clearExpired();
        }
        return true;
    }

    /**
     * Set cache storage
     *
     * @param  CacheStorage $cacheStorage
     * @return Cache
     */
    public function setCacheStorage(CacheStorage $cacheStorage)
    {
        $this->cacheStorage = $cacheStorage;
        return $this;
    }

    /**
     * Get Cache Storage Adapter Object
     *
     * @return CacheStorage
     */
    public function getCacheStorge()
    {
        return $this->cacheStorage;
    }
}
