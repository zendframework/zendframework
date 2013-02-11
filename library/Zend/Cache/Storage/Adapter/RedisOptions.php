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
     * The namespace separator
     * @var string
     */
    protected $namespaceSeparator = ':';

    /**
     * The memcached resource manager
     *
     * @var null|RedisResourceManager
     */
    protected $resourceManager;

    /**
     * The resource id of the resource manager
     *
     * @var string
     */
    protected $resourceId = 'default';

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
     * Get the redis resource id
     *
     * @return string
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * Set the redis resource id
     *
     * @param string $resourceId
     * @return RedisOptions
     */
    public function setResourceId($resourceId)
    {
        $resourceId = (string) $resourceId;
        if ($this->resourceId !== $resourceId) {
            $this->triggerOptionEvent('resource_id', $resourceId);
            $this->resourceId = $resourceId;
        }
        return $this;
    }

    /**
     * Set the redis resource manager to use
     *
     * @param null|RedisResourceManager $resourceManager
     * @return RedisOptions
     */
    public function setResourceManager(RedisResourceManager $resourceManager = null)
    {
        if ($this->resourceManager !== $resourceManager) {
            $this->triggerOptionEvent('resource_manager', $resourceManager);
            $this->resourceManager = $resourceManager;
        }
        return $this;
    }

    /**
     * Get the redis resource manager
     *
     * @return RedisResourceManager
     */
    public function getResourceManager()
    {
        if (!$this->resourceManager) {
            $this->resourceManager = new RedisResourceManager();
        }
        return $this->resourceManager;
    }

    /**
     * Set namespace separator
     *
     * @param  string $namespaceSeparator
     * @return RedisOptions
     */
    public function setNamespaceSeparator($namespaceSeparator)
    {
        $namespaceSeparator = (string) $namespaceSeparator;
        if ($this->namespaceSeparator !== $namespaceSeparator) {
            $this->triggerOptionEvent('namespace_separator', $namespaceSeparator);
            $this->namespaceSeparator = $namespaceSeparator;
        }
        return $this;
    }

    /**
     * Get namespace separator
     *
     * @return string
     */
    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }
}
