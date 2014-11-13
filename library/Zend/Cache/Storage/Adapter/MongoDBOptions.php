<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

class MongoDBOptions extends AdapterOptions
{
    /**
     * The namespace separator
     *
     * @var string
     */
    private $namespaceSeparator = ':';

    /**
     * The redis resource manager
     *
     * @var null|RedisResourceManager
     */
    private $resourceManager;

    /**
     * The resource id of the resource manager
     *
     * @var string
     */
    private $resourceId = 'default';

    /**
     * Set namespace separator
     *
     * @param  string $namespaceSeparator
     *
     * @return self
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

    /**
     * Set the mongodb resource manager to use
     *
     * @param null|MongoDBResourceManager $resourceManager
     *
     * @return self
     */
    public function setResourceManager(MongoDBResourceManager $resourceManager = null)
    {
        if ($this->resourceManager !== $resourceManager) {
            $this->triggerOptionEvent('resource_manager', $resourceManager);

            $this->resourceManager = $resourceManager;
        }

        return $this;
    }

    /**
     * Get the mongodb resource manager
     *
     * @return MongoDBResourceManager
     */
    public function getResourceManager()
    {
        return $this->resourceManager ?: $this->resourceManager = new MongoDBResourceManager();
    }

    /**
     * Get the mongodb resource id
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
     *
     * @return self
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
     * set mongo options
     *
     * @param array $libOptions
     *
     * @return self
     */
    public function setLibOptions(array $libOptions)
    {
        $this->triggerOptionEvent('lib_option', $libOptions);
        $this->getResourceManager()->setLibOptions($this->getResourceId(), $libOptions);

        return $this;
    }
}
