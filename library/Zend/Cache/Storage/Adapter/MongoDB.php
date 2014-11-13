<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use MongoCollection as MongoResource;
use MongoDate;
use MongoException as MongoResourceException;
use stdClass;
use Zend\Cache\Exception;
use Zend\Cache\Storage\Capabilities;
use Zend\Cache\Storage\FlushableInterface;

class MongoDB extends AbstractAdapter implements FlushableInterface
{
    /**
     * Has this instance be initialized
     *
     * @var bool
     */
    private $initialized = false;

    /**
     * the mongodb resource manager
     *
     * @var null|MongoDBResourceManager
     */
    private $resourceManager;

    /**
     * The mongodb resource id
     *
     * @var null|string
     */
    private $resourceId;

    /**
     * The namespace prefix
     *
     * @var string
     */
    private $namespacePrefix = '';

    /**
     * {@inheritDoc}
     *
     * @throws Exception\ExtensionNotLoadedException
     */
    public function __construct($options = null)
    {
        if (!class_exists('\Mongo') || !class_exists('\MongoClient')) {
            throw new Exception\ExtensionNotLoadedException('MongoDB extension not loaded or Mongo polyfill not included');
        }

        parent::__construct($options);

        $initialized = & $this->initialized;
        $this->getEventManager()->attach('option', function ($event) use (& $initialized) {
            $initialized = false;
        });
    }

    /**
     * get mongodb resource
     *
     * @return MongoResource
     */
    private function getMongoDBResource()
    {
        if (!$this->initialized) {
            $options = $this->getOptions();

            $this->resourceManager = $options->getResourceManager();
            $this->resourceId      = $options->getResourceId();

            $namespace = $options->getNamespace();
            if ($namespace !== '') {
                $this->namespacePrefix = $namespace . $options->getNamespaceSeparator();
            } else {
                $this->namespacePrefix = '';
            }

            $this->initialized = true;
        }

        return $this->resourceManager->getResource($this->resourceId);
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions($options)
    {
        if (!$options instanceof MongoDBOptions) {
            $options = new MongoDBOptions($options);
        }

        return parent::setOptions($options);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\RuntimeException
     */
    protected function internalGetItem(& $normalizedKey, & $success = null, & $casToken = null)
    {
        $result = $this->fetchFromCollection($normalizedKey);

        if ($result == null) {
            $success = false;
            return null;
        }

        if ($result['expires'] !== null && $result['expires']->sec < time()) {
            $success = false;
            $this->internalRemoveItem($key);

            return null;
        }

        if (isset($result['expires'])) {
            if (!($result['expires'] instanceof MongoDate)) {
                throw new Exception\RuntimeException(sprintf(
                    "The found item _id '%s' for key '%s' is not a valid cache item'
                    . ': the field 'expired' isn't an instance of MongoDate",
                    (string) $result['_id'],
                    $this->namespacePrefix . $normalizedKey
                ));
            }
            if ($result['expires']->sec < time()) {
                $success = false;
                $this->internalRemoveItem($key);
                return null;
            }
        }

        if (!array_key_exists('value', $result)) {
            throw new Exception\RuntimeException(sprintf(
                "The found item _id '%s' for key '%s' is not a valid cache item'
                . ': missing the field 'value'",
                (string) $result['_id'],
                $this->namespacePrefix . $normalizedKey
            ));
        }

        $value = $result['value'];

        $success = true;
        $casToken = $value;

        return $value;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\RuntimeException
     */
    protected function internalSetItem(& $normalizedKey, & $value)
    {
        $mongo = $this->getMongoDBResource();

        $key = $this->namespacePrefix . $normalizedKey;

        $ttl = $this->getOptions()->getTTl();

        if ($ttl > 0) {
            $expiresMicro = microtime(true) + $ttl;
            $expiresSecs = (int) $expiresMicro;

            $expires = new MongoDate($expiresSecs, $expiresMicro - $expiresSecs);
        } else {
            $expires = null;
        }

        $cacheItem = array(
            'key' => $key,
            'value' => $value,
        );

        if ($expires !== null) {
            $cacheItem['expires'] = $expires;
        }

        try {
            $mongo->remove(array('key' => $key));
            $result = $mongo->insert($cacheItem);
        } catch (MongoResourceException $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        if ($result === null) {
            return false;
        }

        if ($result['ok'] === (double) 1) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\RuntimeException
     */
    protected function internalRemoveItem(& $normalizedKey)
    {
        $mongo = $this->getMongoDBResource();

        $key = $this->namespacePrefix . $normalizedKey;

        $deleteItem = array('key' => $key);

        try {
            $result = $mongo->remove($deleteItem);
        } catch (MongoResourceException $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        if ($result === false) {
            return false;
        }

        if ($result['ok'] === (double) 1 && $result['n'] > 0) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        $result = $this->getMongoDBResource()->drop();

        if ($result['ok'] === (double) 1) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function internalGetCapabilities()
    {
        if (! $this->capabilities) {
            $this->capabilities = new Capabilities(
                $this,
                $this->capabilityMarker = new stdClass(),
                array(
                    'supportedDatatypes' => array(
                        'NULL'     => true,
                        'boolean'  => true,
                        'integer'  => true,
                        'double'   => true,
                        'string'   => true,
                        'array'    => true,
                        'object'   => false,
                        'resource' => false,
                    ),
                    'supportedMetadata'  => array(
                        '_id',
                    ),
                    'minTtl'             => 0,
                    'maxTtl'             => 0,
                    'staticTtl'          => true,
                    'ttlPrecision'       => 1,
                    'useRequestTime'     => false,
                    'expiredRead'        => false,
                    'maxKeyLength'       => 255,
                    'namespaceIsPrefix'  => true,
                )
            );
        }

        return $this->capabilities;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetMetadata(& $normalizedKey)
    {
        $result = $this->fetchFromCollection($normalizedKey);

        if ($result == null) {
            return false;
        }

        return array(
            '_id' => $result['_id'],
        );
    }

    /**
     * Return raw records from MongoCollection
     *
     * @param string $normalizedKey
     *
     * @return array|null
     *
     * @throws Exception\RuntimeException
     */
    private function fetchFromCollection(& $normalizedKey)
    {
        $collection = $this->getMongoDBResource();

        $key = $this->namespacePrefix . $normalizedKey;

        try {
            return $collection->findOne(
                array('key' => $key)
            );

        } catch (MongoResourceException $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
