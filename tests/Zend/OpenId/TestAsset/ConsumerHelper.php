<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OpenId
 */

namespace ZendTest\OpenId\TestAsset;

use Zend\OpenId\Consumer\GenericConsumer as Consumer;
use Zend\OpenId\Consumer\Storage;
use Zend\Session\Container as SessionContainer;
use ZendTest\Session\TestAsset\TestManager as SessionManager;

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
