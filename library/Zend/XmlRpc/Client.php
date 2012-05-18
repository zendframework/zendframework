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
 * @package    Zend_XmlRpc
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\XmlRpc;

use Zend\Http,
    Zend\Server\Client as ServerClient,
    Zend\XmlRpc\Value;

/**
 * An XML-RPC client implementation
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Client implements ServerClient
{
    /**
     * Full address of the XML-RPC service
     * @var string
     * @example http://time.xmlrpc.com/RPC2
     */
    protected $_serverAddress;

    /**
     * HTTP Client to use for requests
     * @var Zend\Http\Client
     */
    protected $_httpClient = null;

    /**
     * Introspection object
     * @var Zend\Http\Client\ServerIntrospection
     */
    protected $_introspector = null;

    /**
     * Request of the last method call
     * @var Zend\XmlRpc\Request
     */
    protected $_lastRequest = null;

    /**
     * Response received from the last method call
     * @var Zend\XmlRpc\Response
     */
    protected $_lastResponse = null;

    /**
     * Proxy object for more convenient method calls
     * @var array of Zend\XmlRpc\Client\ServerProxy
     */
    protected $_proxyCache = array();

    /**
     * Flag for skipping system lookup
     * @var bool
     */
    protected $_skipSystemLookup = false;

    /**
     * Create a new XML-RPC client to a remote server
     *
     * @param  string $server      Full address of the XML-RPC service
     *                             (e.g. http://time.xmlrpc.com/RPC2)
     * @param  \Zend\Http\Client $httpClient HTTP Client to use for requests
     * @return void
     */
    public function __construct($server, Http\Client $httpClient = null)
    {
        if ($httpClient === null) {
            $this->_httpClient = new Http\Client();
        } else {
            $this->_httpClient = $httpClient;
        }

        $this->_introspector  = new Client\ServerIntrospection($this);
        $this->_serverAddress = $server;
    }


    /**
     * Sets the HTTP client object to use for connecting the XML-RPC server.
     *
     * @param  Zend\Http\Client $httpClient
     * @return Zend\Http\Client
     */
    public function setHttpClient(Http\Client $httpClient)
    {
        return $this->_httpClient = $httpClient;
    }


    /**
     * Gets the HTTP client object.
     *
     * @return Zend\Http\Client
     */
    public function getHttpClient()
    {
        return $this->_httpClient;
    }


    /**
     * Sets the object used to introspect remote servers
     *
     * @param  Zend\XmlRpc\Client\ServerIntrospection
     * @return Zend\XmlRpc\Client\ServerIntrospection
     */
    public function setIntrospector(Client\ServerIntrospection $introspector)
    {
        return $this->_introspector = $introspector;
    }


    /**
     * Gets the introspection object.
     *
     * @return Zend\XmlRpc\Client\ServerIntrospection
     */
    public function getIntrospector()
    {
        return $this->_introspector;
    }


   /**
     * The request of the last method call
     *
     * @return Zend\XmlRpc\Request
     */
    public function getLastRequest()
    {
        return $this->_lastRequest;
    }


    /**
     * The response received from the last method call
     *
     * @return Zend\XmlRpc\Response
     */
    public function getLastResponse()
    {
        return $this->_lastResponse;
    }


    /**
     * Returns a proxy object for more convenient method calls
     *
     * @param $namespace  Namespace to proxy or empty string for none
     * @return Zend\XmlRpc\Client\ServerProxy
     */
    public function getProxy($namespace = '')
    {
        if (empty($this->_proxyCache[$namespace])) {
            $proxy = new Client\ServerProxy($this, $namespace);
            $this->_proxyCache[$namespace] = $proxy;
        }
        return $this->_proxyCache[$namespace];
    }

    /**
     * Set skip system lookup flag
     *
     * @param  bool $flag
     * @return Zend\XmlRpc\Client
     */
    public function setSkipSystemLookup($flag = true)
    {
        $this->_skipSystemLookup = (bool) $flag;
        return $this;
    }

    /**
     * Skip system lookup when determining if parameter should be array or struct?
     *
     * @return bool
     */
    public function skipSystemLookup()
    {
        return $this->_skipSystemLookup;
    }

    /**
     * Perform an XML-RPC request and return a response.
     *
     * @param Zend\XmlRpc\Request $request
     * @param null|Zend\XmlRpc\Response $response
     * @return void
     * @throws Zend\XmlRpc\Client\HttpException
     */
    public function doRequest($request, $response = null)
    {
        $this->_lastRequest = $request;

        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');

        $http        = $this->getHttpClient();
        $httpRequest = $http->getRequest();
        if ($httpRequest->getUri() === null) {
            $http->setUri($this->_serverAddress);
        }

        $headers = $httpRequest->headers();
        $headers->addHeaders(array(
            'Content-Type: text/xml; charset=utf-8',
            'Accept: text/xml',
        ));

        if (!$headers->get('user-agent')) {
            $headers->addHeaderLine('user-agent', 'Zend_XmlRpc_Client');
        }

        $xml = $this->_lastRequest->__toString();
        $http->setRawBody($xml);
        $httpResponse = $http->setMethod('POST')->send();

        if (!$httpResponse->isSuccess()) {
            /**
             * Exception thrown when an HTTP error occurs
             */
            throw new Client\Exception\HttpException(
                $httpResponse->getReasonPhrase(),
                $httpResponse->getStatusCode()
            );
        }

        if ($response === null) {
            $response = new Response();
        }

        $this->_lastResponse = $response;
        $this->_lastResponse->loadXml($httpResponse->getBody());
    }

    /**
     * Send an XML-RPC request to the service (for a specific method)
     *
     * @param  string $method Name of the method we want to call
     * @param  array $params Array of parameters for the method
     * @return mixed
     * @throws Zend\XmlRpc\Client\FaultException
     */
    public function call($method, $params=array())
    {
        if (!$this->skipSystemLookup() && ('system.' != substr($method, 0, 7))) {
            // Ensure empty array/struct params are cast correctly
            // If system.* methods are not available, bypass. (ZF-2978)
            $success = true;
            try {
                $signatures = $this->getIntrospector()->getMethodSignature($method);
            } catch (\Zend\XmlRpc\Exception\ExceptionInterface $e) {
                $success = false;
            }
            if ($success) {
                $validTypes = array(
                    Value::XMLRPC_TYPE_ARRAY,
                    Value::XMLRPC_TYPE_BASE64,
                    Value::XMLRPC_TYPE_BOOLEAN,
                    Value::XMLRPC_TYPE_DATETIME,
                    Value::XMLRPC_TYPE_DOUBLE,
                    Value::XMLRPC_TYPE_I4,
                    Value::XMLRPC_TYPE_INTEGER,
                    Value::XMLRPC_TYPE_NIL,
                    Value::XMLRPC_TYPE_STRING,
                    Value::XMLRPC_TYPE_STRUCT,
                );

                if (!is_array($params)) {
                    $params = array($params);
                }
                foreach ($params as $key => $param) {

                    if ($param instanceof Value) {
                        continue;
                    }

                    $type = Value::AUTO_DETECT_TYPE;
                    foreach ($signatures as $signature) {
                        if (!is_array($signature)) {
                            continue;
                        }

                        if (isset($signature['parameters'][$key])) {
                            $type = $signature['parameters'][$key];
                            $type = in_array($type, $validTypes) ? $type : Value::AUTO_DETECT_TYPE;
                        }
                    }

                    $params[$key] = Value::getXmlRpcValue($param, $type);
                }
            }
        }

        $request = $this->_createRequest($method, $params);

        $this->doRequest($request);

        if ($this->_lastResponse->isFault()) {
            $fault = $this->_lastResponse->getFault();
            /**
             * Exception thrown when an XML-RPC fault is returned
             */
            throw new Client\Exception\FaultException(
                $fault->getMessage(),
                $fault->getCode()
                );
        }

        return $this->_lastResponse->getReturnValue();
    }

    /**
     * Create request object
     *
     * @return Zend\XmlRpc\Request
     */
    protected function _createRequest($method, $params)
    {
        return new Request($method, $params);
    }
}
