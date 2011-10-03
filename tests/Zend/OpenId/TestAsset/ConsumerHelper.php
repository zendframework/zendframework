<?php

namespace ZendTest\OpenId\TestAsset;

use Zend\OpenId\Consumer\GenericConsumer as Consumer,
    Zend\OpenId\Consumer\Storage,
    Zend\Session\Container as SessionContainer,
    ZendTest\Session\TestAsset\TestManager as SessionManager;

class ConsumerHelper extends Consumer 
{
    public function __construct(Storage\AbstractStorage $storage = null,
                                $dumbMode = false)
    {
        $container = new SessionContainer('Default', new SessionManager);
        $this->setSession($container);
        parent::__construct($storage, $dumbMode);
    }

    public function addAssociation($url, $handle, $macFunc, $secret, $expires)
    {
        return $this->_addAssociation($url, $handle, $macFunc, $secret, $expires);
    }

    public function getAssociation($url, &$handle, &$macFunc, &$secret, &$expires)
    {
        return $this->_getAssociation($url, $handle, $macFunc, $secret, $expires);
    }

    public function clearAssociation()
    {
        $this->_cache = array();
    }

    public function httpRequest($url, $method = 'GET', array $params = array())
    {
        return $this->_httpRequest($url, $method, $params);
    }

    public function associate($url, $version, $priv_key = null)
    {
        return $this->_associate($url, $version, $priv_key);
    }

    public function discovery(&$id, &$server, &$version)
    {
        return $this->_discovery($id, $server, $version);
    }

}
