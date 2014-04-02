<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use MongoClient;
use MongoDate;
use MongoException;
use Zend\Cache\Exception\ExtensionNotLoadedException;
use Zend\Cache\Exception\RuntimeException;

class MongoDB extends AbstractAdapter
{
    /**
     * mongoCollection
     *
     * @var \MongoCollection
     */
    protected $mongoCollection = null;

    /**
     * __construct
     *
     * @param mixed $options
     * @throws \Zend\Cache\Exception\ExtensionNotLoadedException
     */
    public function __construct($options = null)
    {
        if (!extension_loaded('mongo')) {
            throw new ExtensionNotLoadedException('MongoDB extension not loaded');
        }

        parent::__construct($options);
    }

    /**
     * getMongoDBResource
     *
     * @return \MongoCollection
     * @throws \Zend\Cache\Exception\RuntimeException
     */
    protected function getMongoDBResource()
    {
        if (is_null($this->mongoCollection)) {
            $options = $this->getOptions();

            try {
                $mongo = new MongoClient($options->getConnectString());
            } catch (MongoException $e) {
                throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
            }

            $database = $options->getDatabase();
            $collection = $options->getCollection();

            $this->mongoCollection = $mongo->selectCollection($database, $collection);
        }

        return $this->mongoCollection;
    }

    /**
     * setOptions
     *
     * @param mixed $options
     * @return $this
     */
    public function setOptions($options)
    {
        if (!$options instanceof MongoDBOptions) {
            $options = new MongoDBOptions($options);
        }

        return parent::setOptions($options);
    }

    /**
     * getOptions
     *
     * @return \Zend\Cache\Storage\Adapter\MongoDBOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Internal method to get an item.
     *
     * @param string $normalizedKey
     * @param bool $success
     * @param mixed $casToken
     * @return string|null
     * @throws \Zend\Cache\Exception\RuntimeException
     */
    protected function internalGetItem(& $normalizedKey, & $success = null, & $casToken = null)
    {
        $mongo = $this->getMongoDBResource();

        $key = $this->prefixNamespaceToKey($normalizedKey);

        try {
            $result = $mongo->findOne(
                array('key' => $key)
            );
        } catch (MongoException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        if (is_null($result)) {
            $success = false;
            return null;
        }

        if ($result['expires']->sec < time()) {
            $success = false;
            $this->internalRemoveItem($key);
            return null;
        }

        $value = $result['value'];

        $success = true;
        $casToken = $value;
        return $value;
    }

    /**
     * Internal method to store an item.
     *
     * @param  string $normalizedKey
     * @param  mixed $value
     * @return bool
     * @throws \Zend\Cache\Exception\RuntimeException
     */
    protected function internalSetItem(& $normalizedKey, & $value)
    {
        $mongo = $this->getMongoDBResource();

        $key = $this->prefixNamespaceToKey($normalizedKey);

        $ttl = $this->getOptions()->getTTl();

        $cacheItem = array(
            'key' => $key,
            'value' => $value,
            'expires' => new MongoDate(time() + $ttl),
        );

        if ($this->internalHasItem($normalizedKey)) {
            $this->internalRemoveItem($normalizedKey);
        }

        try {
            $result = $mongo->insert($cacheItem);

        } catch (MongoException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        if (is_null($result)) {
            return false;
        }

        if ($result['ok'] === (double) 1) {
            return true;
        }

        return false;
    }

    /**
     * Internal method to remove an item.
     *
     * @param  string $normalizedKey
     * @return bool
     * @throws \Zend\Cache\Exception\RuntimeException
     */
    protected function internalRemoveItem(& $normalizedKey)
    {
        $mongo = $this->getMongoDBResource();

        $key = $this->prefixNamespaceToKey($normalizedKey);

        $deleteItem = array('key' => $key);

        try {
            $result = $mongo->remove($deleteItem);
        } catch (MongoException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        if (is_null($result)) {
            return false;
        }

        if ($result['ok'] === (double) 1 && $result['n'] > 0) {
            return true;
        }

        return false;
    }

    /**
     * Prefix namespace to key
     *
     * @param string $key
     * @return void
     */
    protected function prefixNamespaceToKey($key)
    {
        $namespace = $this->getOptions()->getNamespace();
        $prefix = ($namespace === '') ? '' : $namespace . '_';
        return $prefix . $key;
    }
}
