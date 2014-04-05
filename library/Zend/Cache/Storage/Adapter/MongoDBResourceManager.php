<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use DateTime;
use MongoCollection as MongoDBResource;
use MongoException as MongoDBResourceException;
use MongoDate as MongoDBDateResource;
use Traversable;
use Zend\Cache\Exception;
use Zend\Stdlib\ArrayUtils;

class MongoDBResourceManager
{
    /**
     * Registered resources
     *
     * @var array
     */
    protected $resources = array();

    /**
     * Check if a resource exists
     *
     * @param string $id
     * @return bool
     */
    public function hasResource($id)
    {
        return isset($this->resources[$id]);
    }

    /**
     * Set a resource
     *
     * @param string $id
     * @param array|Traversable|MongoDBResource $resource
     * @return MongoDBResourceManager
     * @throws Exception\RuntimeException
     */
    public function setResource($id, $resource)
    {
        if ($resource instanceof MongoDBResource) {
            $this->resources[$id] = array(
                'initialized' => true,
                'resource' => $resource,
            );
        } else {
            if ($resource instanceof Traversable) {
                $resource = ArrayUtils::iteratorToArray($resource);
            } elseif (!is_array($resource)) {
                throw new Exception\InvalidArgumentException(
                    'Resource must be an instance of an array or Traversable'
                );
            }

            $defaults = array(
                'collection' => 'cache',
                'database' => 'zend',
                'driverOptions' => array(),
                'options' => array(
                    /**
                     * Journaling is enabled by default in 64bit builds of Mongo 2.0+
                     * As such, we should default fsync to false and journal to true
                     * See:
                     * http://docs.mongodb.org/manual/tutorial/manage-journaling/
                     * http://www.php.net/manual/en/mongoclient.construct.php
                     */
                    'fsync' => false,
                    'journal' => true,
                ),
                'server' => 'mongodb://localhost:27017',
            );

            $this->resources[$id] = array_merge($defaults, $resource);

            // force initialized flag to false
            $this->resources[$id]['initialized'] = false;
        }

        return $this;
    }

    /**
     * @param string $id
     * @return MongoDBResource
     * @throws Exception\RuntimeException
     */
    public function getResource($id)
    {
        if (!$this->hasResource($id)) {
            throw new Exception\RuntimeException("No resource with id '{$id}'");
        }

        $resource = & $this->resources[$id];

        if ($resource['initialized'] !== true) {
            $clientClass = (version_compare(phpversion('mongo'), '1.3.0', '<')) ? '\Mongo' : '\MongoClient';

            try {
                if (!empty($resource['driverOptions'])) {
                    $client = new $clientClass($resource['server'], $resource['options'], $resource['driverOptions']);
                } else {
                    $client = new $clientClass($resource['server'], $resource['options']);
                }
                $resource['resource'] = $client->selectCollection($resource['database'], $resource['collection']);
                $resource['resource']->ensureIndex(array('key' => 1));
            } catch (MongoDBResourceException $e) {
                throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
            }

            $resource['initialized'] = true;
        }

        return $resource['resource'];
    }

    public function setLibOptions($id, array $libOptions)
    {
        if (!$this->hasResource($id)) {
            return $this->setResource($id, $libOptions);
        }

        $resource = & $this->resources[$id];

        unset($resource['resource']);
        $resource = array_merge($resource, $libOptions);
        $resource['initialized'] = false;

        return $this;
    }

    /**
     * create mongo date resource
     *
     * @param null|int|DateTime $timestamp
     * @return MongoDBDateResource
     */
    public function createMongoDate($timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        } elseif ($timestamp instanceof DateTime) {
            $timestamp = $timestamp->getTimestamp();
        }

        return new MongoDBDateResource($timestamp);
    }
}
