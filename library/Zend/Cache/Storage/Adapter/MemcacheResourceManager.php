<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use Memcache as MemcacheResource;
use Traversable;
use Zend\Cache\Exception;
use Zend\Stdlib\ArrayUtils;

/**
 * This is a resource manager for memcache
 */
class MemcacheResourceManager
{
    /**
     * Registered resources
     *
     * @var array
     */
    protected $resources = array();

    /**
     * Default server values per resource
     *
     * @var array
     */
    protected $serverDefaults = array();

    /**
     * Failure callback per resource
     *
     * @var array
     */
    protected $failureCallbacks = array();

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
     * Gets a memcache resource
     *
     * @param string $id
     * @return MemcacheResource
     * @throws Exception\RuntimeException
     */
    public function getResource($id)
    {
        if (!$this->hasResource($id)) {
            throw new Exception\RuntimeException("No resource with id '{$id}'");
        }

        $resource = $this->resources[$id];
        if ($resource instanceof MemcacheResource) {
            return $resource;
        }

        $memc = new MemcacheResource();
        $this->setResourceLibOptions($memc, $resource['lib_options']);
        foreach ($resource['servers'] as $server) {
            $this->addServerToResource(
                $memc, $server, $this->serverDefaults[$id], $this->failureCallbacks[$id]
            );
        }

        // buffer and return
        $this->resources[$id] = $memc;
        return $memc;
    }

    /**
     * @param MemcacheResource $resource
     * @param array $server
     * @param array $serverDefaults
     * @param callable|null $failureCallback
     */
    protected function addServerToResource(
        MemcacheResource $resource, array $server, array $serverDefaults, $failureCallback
    ) {
        // Apply server defaults
        $server = array_merge($serverDefaults, $server);

        // Reorder parameters
        $params = array(
            $server['host'],
            $server['port'],
            $server['persistent'],
            $server['weight'],
            $server['timeout'],
            $server['retry_interval'],
            $server['status'],
        );
        if (isset($failureCallback)) {
            $params[] = $failureCallback;
        }
        call_user_func_array(array($resource, 'addServer'), $params);
    }

    /**
     * Set a resource
     *
     * @param string $id
     * @param array|Traversable|MemcacheResource $resource
     * @return MemcacheResourceManager
     */
    public function setResource($id, $resource, $failureCallback = null, $serverDefaults = array())
    {
        $id = (string) $id;

        if ($serverDefaults instanceof Traversable) {
            $serverDefaults = ArrayUtils::iteratorToArray($serverDefaults);
        } elseif (!is_array($serverDefaults)) {
            throw new Exception\InvalidArgumentException(
                'ServerDefaults must be an instance Traversable or an array'
            );
        }

        if (!($resource instanceof MemcacheResource)) {
            if ($resource instanceof Traversable) {
                $resource = ArrayUtils::iteratorToArray($resource);
            } elseif (!is_array($resource)) {
                throw new Exception\InvalidArgumentException(
                    'Resource must be an instance of Memcache or an array or Traversable'
                );
            }

            if (isset($resource['server_defaults'])) {
                $serverDefaults = array_merge($serverDefaults, $resource['server_defaults']);
                unset($resource['server_defaults']);
            }

            $resource = array_merge(array(
                'lib_options'   => array(),
                'servers'       => array(),
            ), $resource);

            // normalize and validate params
            $this->normalizeLibOptions($resource['lib_options']);
            $this->normalizeServers($resource['servers']);
        }

        $this->normalizeServerDefaults($serverDefaults);

        $this->resources[$id] = $resource;
        $this->failureCallbacks[$id] = $failureCallback;
        $this->serverDefaults[$id] = $serverDefaults;

        return $this;
    }

    /**
     * Remove a resource
     *
     * @param string $id
     * @return MemcacheResourceManager
     */
    public function removeResource($id)
    {
        unset($this->resources[$id]);
        return $this;
    }

    /**
     * Set Libmemcache options
     *
     * @param string $id
     * @param array  $libOptions
     * @return MemcacheResourceManager
     */
    public function setLibOptions($id, array $libOptions)
    {
        if (!$this->hasResource($id)) {
            return $this->setResource($id, array(
                'lib_options' => $libOptions
            ));
        }

        $this->normalizeLibOptions($libOptions);

        $resource = & $this->resources[$id];
        if ($resource instanceof MemcacheResource) {
            $this->setResourceLibOptions($resource, $libOptions);
        } else {
            $resource['lib_options'] = $libOptions;
        }

        return $this;
    }

    /**
     * Normalize libmemcache options
     *
     * @param array|Traversable $libOptions
     * @throws Exception\InvalidArgumentException
     */
    protected function normalizeLibOptions(& $libOptions)
    {
        if (!is_array($libOptions) && !($libOptions instanceof Traversable)) {
            throw new Exception\InvalidArgumentException(
                "Lib-Options must be an array or an instance of Traversable"
            );
        }

        $result = array();
        foreach ($libOptions as $key => $value) {
            switch ($key) {
                case 'compress_threshold':
                    $this->normalizeCompressThresholdOptions($value);
                    break;
            }
            $result[$key] = $value;
        }

        $libOptions = $result;
    }

    /**
     * Set lib options on a Memcache resource
     *
     * @param MemcacheResource $resource
     * @param array $libOptions
     */
    protected function setResourceLibOptions(MemcacheResource $resource, array $libOptions)
    {
        foreach ($libOptions as $key => $value) {
            switch ($key) {
                case 'compress_threshold':
                    if (isset($value['min_savings'])) {
                        $resource->setCompressThreshold($value['threshold'], $value['min_savings']);
                    } else {
                        $resource->setCompressThreshold($value['threshold']);
                    }
                    break;
            }
        }
    }

    /**
     * Normalize compress threshold options into the following format:
     * array('threshold' => <threshold>[, 'min_savings' => <min_savings>])
     *
     * @param array|ArrayAccess $options
     * @throws Exception\InvalidArgumentException
     */
    protected function normalizeCompressThresholdOptions(& $options)
    {
        if (!is_array($options) && !($options instanceof ArrayAccess)) {
            $options = array('threshold' => $options);
        }
        if (!isset($options['threshold'])) {
            throw new Exception\InvalidArgumentException(
                "Compress threshold options must contain a 'threshold' value"
            );
        }
        $options['threshold'] = (int) $options['threshold'];
        if (isset($options['min_savings'])) {
            $options['min_savings'] = (float) $options['min_savings'];
        }
    }

    /**
     * Get Libmemcache options
     *
     * @param string $id
     * @return array
     * @throws Exception\RuntimeException
     */
    public function getLibOptions($id)
    {
        if (!$this->hasResource($id)) {
            throw new Exception\RuntimeException("No resource with id '{$id}'");
        }

        $resource = & $this->resources[$id];
        if ($resource instanceof MemcacheResource) {
            // Cannot get options from Memcache resource once created
            throw new Exception\RuntimeException("Cannot get LibOptions once resource is created");
        }
        return $resource['lib_options'];
    }

    /**
     * Set one Libmemcache option
     *
     * @param string     $id
     * @param string|int $key
     * @param mixed      $value
     * @return MemcacheResourceManager
     */
    public function setLibOption($id, $key, $value)
    {
        return $this->setLibOptions($id, array($key => $value));
    }

    /**
     * Get one Libmemcache option
     *
     * @param string     $id
     * @param string|int $key
     * @return mixed
     * @throws Exception\RuntimeException
     */
    public function getLibOption($id, $key)
    {
        $libOptions = $this->getLibOptions($id);
        return isset($libOptions[$key]) ? $libOptions[$key] : null;
    }

    /**
     * Set default server values
     * array(
     *   'persistent' => <persistent>, 'weight' => <weight>,
     *   'timeout' => <timeout>, 'retry_interval' => <retryInterval>,
     * )
     * @param string $id
     * @param array $serverDefaults
     * @return MemcacheResourceManager
     */
    public function setServerDefaults($id, array $serverDefaults)
    {
        if (!$this->hasResource($id)) {
            return $this->setResource($id, array(
                'server_defaults' => $serverDefaults
            ));
        }

        $this->normalizeServerDefaults($serverDefaults);
        $this->serverDefaults[$id] = $serverDefaults;

        return $this;
    }

    /**
     * Get default server values
     *
     * @param string $id
     * @return array
     * @throws Exception\RuntimeException
     */
    public function getServerDefaults($id)
    {
        if (!isset($this->serverDefaults[$id])) {
            throw new Exception\RuntimeException("No resource with id '{$id}'");
        }
        return $this->serverDefaults[$id];
    }

    /**
     * @param array $serverDefaults
     * @throws Exception\InvalidArgumentException
     */
    protected function normalizeServerDefaults(& $serverDefaults)
    {
        if (!is_array($serverDefaults) && !($serverDefaults instanceof Traversable)) {
            throw new Exception\InvalidArgumentException(
                "Lib-Options must be an array or an instance of Traversable"
            );
        }

        // Defaults
        $result = array(
            'persistent' => true,
            'weight' => 1,
            'timeout' => 1, // seconds
            'retry_interval' => 15, // seconds
        );

        foreach ($serverDefaults as $key => $value) {
            switch ($key) {
                case 'persistent':
                    $value = (bool) $value;
                    break;
                case 'weight':
                case 'timeout':
                case 'retry_interval':
                    $value = (int) $value;
                    break;
            }
            $result[$key] = $value;
        }

        $serverDefaults = $result;
    }

    /**
     * Set callback for server connection failures
     *
     * @param string $id
     * @param callable|null $failureCallback
     * @return MemcacheResourceManager
     */
    public function setFailureCallback($id, $failureCallback)
    {
        if (!$this->hasResource($id)) {
            return $this->setResource($id, array(), $failureCallback);
        }

        $this->failureCallbacks[$id] = $failureCallback;
        return $this;
    }

    /**
     * Get callback for server connection failures
     *
     * @param string $id
     * @return callable|null
     * @throws Exception\RuntimeException
     */
    public function getFailureCallback($id)
    {
        if (!isset($this->failureCallbacks[$id])) {
            throw new Exception\RuntimeException("No resource with id '{$id}'");
        }
        return $this->failureCallbacks[$id];
    }

    /**
     * Get servers
     *
     * @param string $id
     * @throws Exception\RuntimeException
     * @return array array('host' => <host>, 'port' => <port>, 'weight' => <weight>)
     */
    public function getServers($id)
    {
        if (!$this->hasResource($id)) {
            throw new Exception\RuntimeException("No resource with id '{$id}'");
        }

        $resource = & $this->resources[$id];
        if ($resource instanceof MemcacheResource) {
            throw new Exception\RuntimeException("Cannot get server list once resource is created");
        }
        return $resource['servers'];
    }

    /**
     * Add servers
     *
     * @param string       $id
     * @param string|array $servers
     * @return MemcacheResourceManager
     */
    public function addServers($id, $servers)
    {
        if (!$this->hasResource($id)) {
            return $this->setResource($id, array(
                'servers' => $servers
            ));
        }

        $this->normalizeServers($servers);

        $resource = & $this->resources[$id];
        if ($resource instanceof MemcacheResource) {
            foreach ($servers as $server) {
                $this->addServerToResource(
                    $resource, $server, $this->serverDefaults[$id], $this->failureCallbacks[$id]
                );
            }
        } else {
            // don't add servers twice
            $resource['servers'] = array_merge(
                $resource['servers'],
                array_udiff($servers, $resource['servers'], array($this, 'compareServers'))
            );
        }

        return $this;
    }

    /**
     * Add one server
     *
     * @param string       $id
     * @param string|array $server
     * @return MemcacheResourceManager
     */
    public function addServer($id, $server)
    {
        return $this->addServers($id, array($server));
    }

    /**
     * Normalize a list of servers into the following format:
     * array(array('host' => <host>, 'port' => <port>, 'weight' => <weight>)[, ...])
     *
     * @param string|array $servers
     */
    protected function normalizeServers(& $servers)
    {
        if (is_string($servers)) {
            // Convert string into a list of servers
            $servers = explode(',', $servers);
        }

        $result = array();
        foreach ($servers as $server) {
            $this->normalizeServer($server);
            $result[$server['host'] . ':' . $server['port']] = $server;
        }

        $servers = array_values($result);
    }

    /**
     * Normalize one server into the following format:
     * array(
     *   'host' => <host>, 'port' => <port>, 'weight' => <weight>,
     *   'status' => <status>, 'persistent' => <persistent>,
     *   'timeout' => <timeout>, 'retry_interval' => <retryInterval>,
     * )
     *
     * @param string|array $server
     * @throws Exception\InvalidArgumentException
     */
    protected function normalizeServer(& $server)
    {
        $sTmp = array(
            'host'           => null,
            'port'           => 11211,
            'weight'         => null,
            'status'         => true,
            'persistent'     => null,
            'timeout'        => null,
            'retry_interval' => null,
        );

        // convert a single server into an array
        if ($server instanceof Traversable) {
            $server = ArrayUtils::iteratorToArray($server);
        }

        if (is_array($server)) {
            if (isset($server[0])) {
                // Convert ordered array to keyed array
                // array(<host>[, <port>[, <weight>[, <status>[, <persistent>[, <timeout>[, <retryInterval>]]]]]])
                $server = array_combine(
                    array_slice(array_keys($sTmp), 0, count($server)),
                    $server
                );
            }
            $sTmp = array_merge($sTmp, $server);
        } elseif (is_string($server)) {
            // parse server from URI host{:?port}{?weight}
            $server = trim($server);
            if (strpos($server, '://') === false) {
                $server = 'tcp://' . $server;
            }

            $urlParts = parse_url($server);
            if (!$urlParts) {
                throw new Exception\InvalidArgumentException("Invalid server given");
            }

            $sTmp = array_merge($sTmp, array_intersect_key($urlParts, $sTmp));
            if (isset($urlParts['query'])) {
                $query = null;
                parse_str($urlParts['query'], $query);
                $sTmp = array_merge($sTmp, array_intersect_key($query, $sTmp));
            }
        }

        if (!$sTmp['host']) {
            throw new Exception\InvalidArgumentException('Missing required server host');
        }

        // Filter values
        foreach ($sTmp as $key => $value) {
            if (isset($value)) {
                switch ($key) {
                    case 'host':
                        $value = (string) $value;
                        break;
                    case 'status':
                    case 'persistent':
                        $value = (bool) $value;
                        break;
                    case 'port':
                    case 'weight':
                    case 'timeout':
                    case 'retry_interval':
                        $value = (int) $value;
                        break;
                }
            }
            $sTmp[$key] = $value;
        }
        $sTmp = array_filter($sTmp, function ($val) { return isset($val); });

        $server = $sTmp;
    }

    /**
     * Compare 2 normalized server arrays
     * (Compares only the host and the port)
     *
     * @param array $serverA
     * @param array $serverB
     * @return int
     */
    protected function compareServers(array $serverA, array $serverB)
    {
        $keyA = $serverA['host'] . ':' . $serverA['port'];
        $keyB = $serverB['host'] . ':' . $serverB['port'];
        if ($keyA === $keyB) {
            return 0;
        }
        return $keyA > $keyB ? 1 : -1;
    }
}
