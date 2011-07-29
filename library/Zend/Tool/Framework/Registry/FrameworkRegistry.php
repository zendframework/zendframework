<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework\Registry;

use Zend\Tool\Framework\Registry,
    Zend\Tool\Framework\RegistryEnabled,
    Zend\Tool\Framework\Client,
    Zend\Tool\Framework\Client\Storage,
    Zend\Tool\Framework\Action,
    Zend\Tool\Framework\Provider,
    Zend\Tool\Framework\Manifest,
    Zend\Tool\Framework\Client\Response;

/**
 * @uses       \Zend\Tool\Framework\Action\Repository
 * @uses       \Zend\Tool\Framework\Client\Config
 * @uses       \Zend\Tool\Framework\Client\Request
 * @uses       \Zend\Tool\Framework\Client\Response
 * @uses       \Zend\Tool\Framework\Client\Storage
 * @uses       \Zend\Tool\Framework\Loader\IncludePathLoader
 * @uses       \Zend\Tool\Framework\Manifest\Repository
 * @uses       \Zend\Tool\Framework\Provider\Repository
 * @uses       \Zend\Tool\Framework\Registry\Exception
 * @uses       \Zend\Tool\Framework\Registry
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FrameworkRegistry implements Registry
{
    /**
     * @var \Zend\Tool\Framework\Loader\AbstractLoader
     */
    protected $_loader = null;

    /**
     * @var \Zend\Tool\Framework\Client\AbstractClient
     */
    protected $_client = null;

    /**
     * @var \Zend\Tool\Framework\Client\Config
     */
    protected $_config = null;

    /**
     * @var \Zend\Tool\Framework\Client\Storage
     */
    protected $_storage = null;

    /**
     * @var \Zend\Tool\Framework\Action\Repository
     */
    protected $_actionRepository = null;

    /**
     * @var \Zend\Tool\Framework\Provider\Repository
     */
    protected $_providerRepository = null;

    /**
     * @var \Zend\Tool\Framework\Manifest\Repository
     */
    protected $_manifestRepository = null;

    /**
     * @var \Zend\Tool\Framework\Client\Request
     */
    protected $_request = null;

    /**
     * @var \Zend\Tool\Framework\Client\Response
     */
    protected $_response = null;

    /**
     * reset() - Reset all internal properties
     *
     */
    public function reset()
    {
        unset($this->_client);
        unset($this->_loader);
        unset($this->_actionRepository);
        unset($this->_providerRepository);
        unset($this->_request);
        unset($this->_response);
    }


    /**
     * Enter description here...
     *
     * @param \Zend\Tool\Framework\Client\AbstractClient $client
     * @return \Zend\Tool\Framework\Registry\FrameworkRegistry
     */
    public function setClient(Client\AbstractClient $client)
    {
        $this->_client = $client;
        if ($this->isObjectRegistryEnablable($this->_client)) {
            $this->enableRegistryOnObject($this->_client);
        }
        return $this;
    }

    /**
     * getClient() return the client in the registry
     *
     * @return \Zend\Tool\Framework\Client\AbstractClient
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * setConfig()
     *
     * @param \Zend\Tool\Framework\Client\Config $config
     * @return \Zend\Tool\Framework\Registry\FrameworkRegistry
     */
    public function setConfig(Client\Config $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * getConfig()
     *
     * @return \Zend\Tool\Framework\Client\Config
     */
    public function getConfig()
    {
        if ($this->_config === null) {
            $this->setConfig(new Client\Config());
        }

        return $this->_config;
    }

    /**
     * setStorage()
     *
     * @param \Zend\Tool\Framework\Client\Storage $storage
     * @return \Zend\Tool\Framework\Registry\FrameworkRegistry
     */
    public function setStorage(Storage $storage)
    {
        $this->_storage = $storage;
        return $this;
    }

    /**
     * getConfig()
     *
     * @return \Zend\Tool\Framework\Client\Storage
     */
    public function getStorage()
    {
        if ($this->_storage === null) {
            $this->setStorage(new Storage());
        }

        return $this->_storage;
    }

    /**
     * setLoader()
     *
     * @param \Zend\Tool\Framework\Loader $loader
     * @return \Zend\Tool\Framework\Registry\FrameworkRegistry
     */
    public function setLoader(\Zend\Tool\Framework\Loader $loader)
    {
        $this->_loader = $loader;
        if ($this->isObjectRegistryEnablable($this->_loader)) {
            $this->enableRegistryOnObject($this->_loader);
        }
        return $this;
    }

    /**
     * getLoader()
     *
     * @return \Zend\Tool\Framework\Loader
     */
    public function getLoader()
    {
        if ($this->_loader === null) {
            $this->setLoader(new \Zend\Tool\Framework\Loader\IncludePathLoader());
        }

        return $this->_loader;
    }

    /**
     * setActionRepository()
     *
     * @param \Zend\Tool\Framework\Action\Repository $actionRepository
     * @return \Zend\Tool\Framework\Registry\FrameworkRegistry
     */
    public function setActionRepository(Action\Repository $actionRepository)
    {
        $this->_actionRepository = $actionRepository;
        if ($this->isObjectRegistryEnablable($this->_actionRepository)) {
            $this->enableRegistryOnObject($this->_actionRepository);
        }
        return $this;
    }

    /**
     * getActionRepository()
     *
     * @return \Zend\Tool\Framework\Action\Repository
     */
    public function getActionRepository()
    {
        if ($this->_actionRepository == null) {
            $this->setActionRepository(new Action\Repository());
        }

        return $this->_actionRepository;
    }

    /**
     * setProviderRepository()
     *
     * @param \Zend\Tool\Framework\Provider\Repository $providerRepository
     * @return \Zend\Tool\Framework\Registry\FrameworkRegistry
     */
    public function setProviderRepository(Provider\Repository $providerRepository)
    {
        $this->_providerRepository = $providerRepository;
        if ($this->isObjectRegistryEnablable($this->_providerRepository)) {
            $this->enableRegistryOnObject($this->_providerRepository);
        }
        return $this;
    }

    /**
     * getProviderRepository()
     *
     * @return \Zend\Tool\Framework\Provider\Repository
     */
    public function getProviderRepository()
    {
        if ($this->_providerRepository == null) {
            $this->setProviderRepository(new Provider\Repository());
        }

        return $this->_providerRepository;
    }

    /**
     * setManifestRepository()
     *
     * @param \Zend\Tool\Framework\Manifest\Repository $manifestRepository
     * @return \Zend\Tool\Framework\Registry\FrameworkRegistry
     */
    public function setManifestRepository(Manifest\Repository $manifestRepository)
    {
        $this->_manifestRepository = $manifestRepository;
        if ($this->isObjectRegistryEnablable($this->_manifestRepository)) {
            $this->enableRegistryOnObject($this->_manifestRepository);
        }
        return $this;
    }

    /**
     * getManifestRepository()
     *
     * @return \Zend\Tool\Framework\Manifest\Repository
     */
    public function getManifestRepository()
    {
        if ($this->_manifestRepository == null) {
            $this->setManifestRepository(new Manifest\Repository());
        }

        return $this->_manifestRepository;
    }

    /**
     * setRequest()
     *
     * @param \Zend\Tool\Framework\Client\Request $request
     * @return \Zend\Tool\Framework\Registry\FrameworkRegistry
     */
    public function setRequest(Client\Request $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * getRequest()
     *
     * @return \Zend\Tool\Framework\Client\Request
     */
    public function getRequest()
    {
        if ($this->_request == null) {
            $this->setRequest(new Client\Request());
        }

        return $this->_request;
    }

    /**
     * setResponse()
     *
     * @param \Zend\Tool\Framework\Client\Response $response
     * @return \Zend\Tool\Framework\Registry\FrameworkRegistry
     */
    public function setResponse(Response $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * getResponse()
     *
     * @return \Zend\Tool\Framework\Client\Response
     */
    public function getResponse()
    {
        if ($this->_response == null) {
            $this->setResponse(new Response());
        }

        return $this->_response;
    }

    /**
     * __get() - Get a property via property call $registry->foo
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (method_exists($this, 'get' . $name)) {
            return $this->{'get' . $name}();
        } else {
            throw new Exception\InvalidArgumentException('Property ' . $name . ' was not located in this registry.');
        }
    }

    /**
     * __set() - Set a property via the magic set $registry->foo = 'foo'
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if (method_exists($this, 'set' . $name)) {
            $this->{'set' . $name}($value);
            return;
        } else {
            throw new Exception\InvalidArgumentException('Property ' . $name . ' was not located in this registry.');
        }
    }

    /**
     * isObjectRegistryEnablable() - Check whether an object is registry enablable
     *
     * @param object $object
     * @return bool
     */
    public function isObjectRegistryEnablable($object)
    {
        if (!is_object($object)) {
            throw new Exception\InvalidArgumentException('isObjectRegistryEnablable() expects an object.');
        }

        return ($object instanceof RegistryEnabled);
    }

    /**
     * enableRegistryOnObject() - make an object registry enabled
     *
     * @param object $object
     * @return \Zend\Tool\Framework\Registry\FrameworkRegistry
     */
    public function enableRegistryOnObject($object)
    {
        if (!$this->isObjectRegistryEnablable($object)) {
            throw new Exception\InvalidArgumentException('Object provided is not registry enablable, check first with Zend\\Tool\\Framework\\Registry::isObjectRegistryEnablable()');
        }

        $object->setRegistry($this);
        return $this;
    }

}
