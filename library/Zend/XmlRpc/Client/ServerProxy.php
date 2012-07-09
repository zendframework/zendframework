<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_XmlRpc
 */

namespace Zend\XmlRpc\Client;

use Zend\XmlRpc\Client as XMLRPCClient;

/**
 * The namespace decorator enables object chaining to permit
 * calling XML-RPC namespaced functions like "foo.bar.baz()"
 * as "$remote->foo->bar->baz()".
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Client
 */
class ServerProxy
{
    /**
     * @var \Zend\XmlRpc\Client
     */
    private $_client = null;

    /**
     * @var string
     */
    private $_namespace = '';


    /**
     * @var array of \Zend\XmlRpc\Client\ServerProxy
     */
    private $_cache = array();


    /**
     * Class constructor
     *
     * @param \Zend\XmlRpc\Client $client
     * @param string             $namespace
     */
    public function __construct(XMLRPCClient $client, $namespace = '')
    {
        $this->_client    = $client;
        $this->_namespace = $namespace;
    }


    /**
     * Get the next successive namespace
     *
     * @param string $name
     * @return \Zend\XmlRpc\Client\ServerProxy
     */
    public function __get($namespace)
    {
        $namespace = ltrim("$this->_namespace.$namespace", '.');
        if (!isset($this->_cache[$namespace])) {
            $this->_cache[$namespace] = new $this($this->_client, $namespace);
        }
        return $this->_cache[$namespace];
    }


    /**
     * Call a method in this namespace.
     *
     * @param  string $methodN
     * @param  array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        $method = ltrim("{$this->_namespace}.{$method}", '.');
        return $this->_client->call($method, $args);
    }
}
