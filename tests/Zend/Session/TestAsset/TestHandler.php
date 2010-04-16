<?php
namespace ZendTest\Session\TestAsset;

use Zend\Session\Handler as SessionHandler,
    Zend\Session\Configuration as SessionConfiguration,
    Zend\Session\Storage as SessionStorage;

class TestHandler implements SessionHandler
{
    public function start()
    {}

    public function destroy()
    {}

    public function stop()
    {}

    public function writeClose()
    {}


    public function getName()
    {}

    public function setName($name)
    {}

    public function getId()
    {}

    public function setId($id)
    {}

    public function regenerateId()
    {}

    public function rememberMe($ttl = null)
    {}

    public function forgetMe()
    {}


    public function setValidatorChain(\Zend\Messenger\Delivery $chain)
    {}

    public function getValidatorChain()
    {}

    public function isValid()
    {}


    public function sessionExists()
    {}

    public function expireSessionCookie()
    {}


    public function setConfiguration(SessionConfiguration $config)
    {
        $this->config = $config;
    }

    public function getConfiguration()
    {
        return $this->config;
    }

    public function setStorage(SessionStorage $storage)
    {
        $this->storage = $storage;
    }

    public function getStorage()
    {
        return $this->storage;
    }
}
