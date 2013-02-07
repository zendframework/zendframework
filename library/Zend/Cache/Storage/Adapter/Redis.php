<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use Redis as RedisResource;

use stdClass;
use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\Cache\Exception;
use Zend\Cache\Storage\AvailableSpaceCapableInterface;
use Zend\Cache\Storage\Capabilities;
use Zend\Cache\Storage\FlushableInterface;
use Zend\Cache\Storage\TotalSpaceCapableInterface;

class Redis extends AbstractAdapter implements
    FlushableInterface
{

    /**
     * Connection to redis
     *
     * @var Redis
     */
    protected $redisResource;

    /**
     * Get Redis resource
     *
     * @return Redis
     */
    public function getRedisResource()
    {
        if ($this->redisResource) {
            return $this->redisResource;
        }

        $options = $this->getOptions();
        $redis = $options->getRedisResource() ?: new RedisResource();

        foreach ($options->getLibOptions() as $key => $value) {
            $redis->setOption($key, $value);
        }

        // Allow updating namespace
        $this->getEventManager()->attach(
            'option',
            function ($event) use ($redis) {
                $allowed = array('namespace', 'database');
                $params = $event->getParams();
                foreach ($params as $key => $value) {
                    if (!in_array($key, $allowed)) {
                        // Cannot set lib options after initialization
                        continue;
                    }
                    $redis->setOption(RedisResource::OPT_PREFIX, $value);
                }
            }
        );

        $server = $options->getServer();

        $redis->connect($server['host'], $server['port'], $server['timeout']);

        $redis->select($options->getDatabase());
        $redis->setOption(RedisResource::OPT_PREFIX, $options->getNamespace());
        $password = $this->getOptions()->getPassword();

        if ($password) {
            $redis->auth($password);
        }

        $this->redisResource = $redis;

        return $this->redisResource;
    }

    /**
     * Create new Adapter for redis storage
     *
     * @param \Zend\Cache\Storage\Adapter\RedisOptions $options
     * @see \Zend\Cache\Storage\Adapter\Abstract
     */
    public function __construct(RedisOptions $options = null)
    {
        if (!extension_loaded('redis')) {
            throw new Exception\ExtensionNotLoadedException("Redis extension is not loaded");
        }

        parent::__construct($options ?: new RedisOptions());
    }

    /**
     * Internal method to get an item.
     *
     * @param string  &$normalizedKey Key where to store data
     * @param boolean &$success       If the operation was successfull
     * @param mixed   &$casToken      Token
     * @return mixed Data on success, false on key not found
     */
    protected function internalGetItem(& $normalizedKey, & $success = null, & $casToken = null)
    {
        $success = true;
        return $this->getRedisResource()->get($normalizedKey);
    }

     /**
     * Internal method to get multiple items.
     *
     * @param array &$normalizedKeys Array of keys to be obtained
     *
     * @return array Associative array of keys and values
     */
    protected function internalGetItems(array & $normalizedKeys)
    {
        return $this->getRedisResource()->mGet($normalizedKeys);
    }

    /**
     * Internal method to test if an item exists.
     *
     * @param string &$normalizedKey Normalized key which will be checked
     *
     * @return boolean
     */
    protected function internalHasItem(& $normalizedKey)
    {
        return $this->getRedisResource()->exists($normalizedKey);
    }

    /**
     * Internal method to store an item.
     *
     * @param string &$normalizedKey Key in Redis under which value will be saved
     * @param mixed  &$value         Value to store under cache key
     *
     * @return boolean
     */
    protected function internalSetItem(& $normalizedKey, & $value)
    {

        $ttl = $this->getOptions()->getTtl();
        if ($ttl) {
            return $this->getRedisResource()->setex($normalizedKey, $ttl, $value);
        } else {
            return $this->getRedisResource()->set($normalizedKey, $value);
        }
    }

     /**
     * Internal method to store multiple items.
     *
     * @param array &$normalizedKeyValuePairs An array of normalized key/value pairs
     *
     * @return array Array of not stored keys
     */
    protected function internalSetItems(array & $normalizedKeyValuePairs)
    {
        return $this->getRedisResource()->mSet($normalizedKeyValuePairs);
    }

    /**
     * Internal method to remove an item.
     *
     * @param string &$normalizedKey Key which will be removed
     *
     * @return boolean
     */
    protected function internalRemoveItem(& $normalizedKey)
    {
        return $this->getRedisResource()->delete($normalizedKey);
    }

    /**
     * Flushes all contents of current database
     *
     * @return bool Always true
     */
    public function flush()
    {
        return $this->getRedisResource()->flushDB();
    }
}
