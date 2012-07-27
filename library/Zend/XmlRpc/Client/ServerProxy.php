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
    private $client = null;

    /**
     * @var string
     */
    private $namespace = '';


    /**
     * @var array of \Zend\XmlRpc\Client\ServerProxy
     */
    private $cache = array();


    /**
     * Class constructor
     *
     * @param \Zend\XmlRpc\Client $client
     * @param string             $namespace
     */
    public function __construct(XMLRPCClient $client, $namespace = '')
    {
        $this->client    = $client;
        $this->namespace = $namespace;
    }


    /**
     * Get the next successive namespace
     *
     * @param string $name
     * @return \Zend\XmlRpc\Client\ServerProxy
     */
    public function __get($namespace)
    {
        $namespace = ltrim("$this->namespace.$namespace", '.');
        if (!isset($this->cache[$namespace])) {
            $this->cache[$namespace] = new $this($this->client, $namespace);
        }
        return $this->cache[$namespace];
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
        $method = ltrim("{$this->namespace}.{$method}", '.');
        return $this->client->call($method, $args);
    }
}
