<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter\AdapterOptions;

use Redis as RedisResource;

class RedisOptions extends AdapterOptions
{
    /**
     * Redis resource
     *
     * @var \Redis
     */
    protected $redisResource;

    /**
     * Optional password to Redis
     *
     * @var string
     */
    protected $password;

    /**
     * Database number
     *
     * @var int
     */
    protected $database = 0;

    /**
     * Redis server connection settings
     *
     * @var string
     */
    protected $server = array(
        'host'   => '127.0.0.1',
        'port'   => 6379,
        'timeout' => 0,
    );

    /**
     * List of Libmemcached options to set on initialize
     *
     * @var array
     */
    protected $libOptions = array();

    /**
     * Set namespace.
     *
     * The option Redis::OPT_PREFIX will be used as the namespace.
     * It can't be longer than 128 characters.
     *
     * @param string $namespace Prefix for each key stored in redis
     * @return \Zend\Cache\Storage\Adapter\RedisOptions
     *
     * @see AdapterOptions::setNamespace()
     * @see RedisOptions::setPrefixKey()
     */
    public function setNamespace($namespace)
    {
        $namespace = (string) $namespace;

        if (128 < strlen($namespace)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    '%s expects a prefix key of no longer than 128 characters',
                    __METHOD__
                )
            );
        }

        return parent::setNamespace($namespace);
    }

    /**
     * Sets optional password to redis server
     *
     * @param string $password Password to redis
     * @return RedisOptions
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Gets password to Redis
     *
     * @return string Redis password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets database number to use with redis
     *
     * @param string $database Redis database number
     * @return RedisOptions
     */
    public function setDatabase($database)
    {
        $this->database = (int)$database;
        return $this;
    }

    /**
     * Gets currently selected database number
     *
     * @return string Redis database
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Sets host for server
     *
     * @param string $host Redis server host
     * @return RedisOptions
     */
    public function setHost($host)
    {
        $this->server['host'] = $host;
        return $this;
    }

    /**
     * Sets port for redis server
     *
     * @param int $port Redis server port
     * @return RedisOptions
     */
    public function setPort($port)
    {
        $this->server['port'] = (int)$port;
        return $this;
    }

    /**
     * Sets Timeout for connection estabilisment
     *
     * @param int $timeout Connection timeout
     * @return RedisOptions
     */
    public function setTimeout($timeout)
    {
        $this->server['timeout'] = (int)$timeout;
        return $this;
    }

    /**
     * A redis resource to share
     *
     * @param null|RedisResource $redisResource Redis resource object
     * @return RedisOptions
     */
    public function setRedisResource(RedisResource $redisResource = null)
    {
        if ($this->redisResource !== $redisResource) {
            $this->triggerOptionEvent('redis_resource', $redisResource);
            $this->redisResource = $redisResource;
        }
        return $this;
    }

    /**
     * Get memcached resource to share
     *
     * @return null|RedisResource
     */
    public function getRedisResource()
    {
        return $this->redisResource;
    }

    /**
     * Add a server to the list
     *
     * @param string $host    Redis host
     * @param int    $port    Redis port
     * @param int    $timeout Timout for connection
     *
     * @return RedisOptions
     */
    public function setServer($host, $port = 6379, $timeout = 0)
    {
        $this->server = array('host' => $host, 'port' => $port, 'timeout' => $timeout);
        return $this;
    }

    /**
     * Get Server info in array
     *
     * @return array
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Set phpredis options
     *
     * @param array $libOptions Array of options
     * @return RedisOptions
     * @link https://github.com/nicolasff/phpredis
     */
    public function setLibOptions(array $libOptions)
    {
        $normalizedOptions = array();
        foreach ($libOptions as $key => $value) {
            $this->normalizeLibOptionKey($key);
            $normalizedOptions[$key] = $value;
        }

        $this->triggerOptionEvent('lib_options', $normalizedOptions);
        $this->libOptions = array_diff_key($this->libOptions, $normalizedOptions) + $normalizedOptions;

        return $this;
    }

    /**
     * Set phpredis option
     *
     * @param string|int $key   Option key
     * @param mixed      $value Option value
     *
     * @return RedisOptions
     * @link https://github.com/nicolasff/phpredis
     */
    public function setLibOption($key, $value)
    {
        $this->normalizeLibOptionKey($key);
        $this->triggerOptionEvent('lib_options', array($key, $value));
        $this->libOptions[$key] = $value;

        return $this;
    }

    /**
     * Get phpredis options
     *
     * @return array
     * @link https://github.com/nicolasff/phpredis
     */
    public function getLibOptions()
    {
        return $this->libOptions;
    }

    /**
     * Get phpredis option
     *
     * @param string|int $key Option key
     *
     * @return mixed
     * @link https://github.com/nicolasff/phpredis
     */
    public function getLibOption($key)
    {
        $this->normalizeLibOptionKey($key);
        if (isset($this->libOptions[$key])) {
            return $this->libOptions[$key];
        }
        return null;
    }

    /**
     * Normalize Redis option name into it's constant value
     *
     * @param string|int &$key Performs Normalization of a key provided
     * @return null
     *
     * @throws Exception\InvalidArgumentException
     */
    protected function normalizeLibOptionKey(& $key)
    {
        if (is_string($key)) {
            $const = 'Redis::OPT_' . str_replace(array(' ', '-'), '_', strtoupper($key));
            if (!defined($const)) {
                throw new Exception\InvalidArgumentException("Unknown redis option '{$key}' ({$const})");
            }
            $key = constant($const);
        } else {
            $key = (int) $key;
        }
    }
}
