<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use MongoCollection;
use MongoException;
use Traversable;
use Zend\Cache\Exception;
use Zend\Stdlib\ArrayUtils;

class MongoDBResourceManager
{
    /**
     * Registered resources
     *
     * @var array[]
     */
    private $resources = array();

    /**
     * Check if a resource exists
     *
     * @param string $id
     *
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
     * @param array|Traversable|MongoCollection $resource
     *
     * @return self
     *
     * @throws Exception\RuntimeException
     */
    public function setResource($id, $resource)
    {
        if ($resource instanceof MongoCollection) {
            $this->resources[$id] = array(
                'initialized' => true,
                'resource'    => $resource,
            );

            return $this;
        }

        $this->resources[$id] = array_merge(
            array(
                'server'            => 'mongodb://localhost:27017',
                'collection'        => 'cache',
                'database'          => 'zend',
                'driverOptions'     => array(),
                'connectionOptions' => array(
                    /**
                     * Journaling is enabled by default in 64bit builds of Mongo 2.0+
                     * As such, we should default fsync to false and journal to true
                     * See:
                     * http://docs.mongodb.org/manual/tutorial/manage-journaling/
                     * http://www.php.net/manual/en/mongoclient.construct.php
                     */
                    'fsync'   => false,
                    'journal' => true,
                ),
            ),
            ArrayUtils::iteratorToArray($resource),
            // force initialized flag to false
            array('initialized' => false)
        );

        return $this;
    }

    /**
     * @param string $id
     *
     * @return MongoCollection
     *
     * @throws Exception\RuntimeException
     */
    public function getResource($id)
    {
        if (!$this->hasResource($id)) {
            throw new Exception\RuntimeException("No resource with id '{$id}'");
        }

        if (! $this->resources[$id]['initialized']) {
            $clientClass = version_compare(phpversion('mongo'), '1.3.0', '<') ? 'Mongo' : 'MongoClient';

            try {
                /* @var $client \Mongo|\MongoClient */
                $client = new $clientClass(
                    $this->resources[$id]['server'],
                    $this->resources[$id]['connectionOptions'],
                    (array) $this->resources[$id]['driverOptions']
                );

                $collection = $client->selectCollection(
                    $this->resources[$id]['database'],
                    $this->resources[$id]['collection']
                );

                $collection->ensureIndex(array('key' => 1));

                $this->resources[$id]['resource'] = $collection;
            } catch (MongoException $e) {
                throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
            }

            $this->resources[$id]['initialized'] = true;
        }

        return $this->resources[$id]['resource'];
    }

    /**
     * @param string $id
     * @param array  $libOptions
     *
     * @return self
     */
    public function setLibOptions($id, array $libOptions)
    {
        if (!$this->hasResource($id)) {
            return $this->setResource($id, $libOptions);
        }

        unset($this->resources[$id]['resource']);

        $this->resources[$id]                = array_merge($this->resources[$id], $libOptions);
        $this->resources[$id]['initialized'] = false;

        return $this;
    }
}
