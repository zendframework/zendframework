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
 * @package    Zend_Json
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Json\Server;

use Zend\Http\Client as HttpClient,
    Zend\Server\Client as ClientInterface;

/**
 * @category   Zend
 * @package    Zend_Json
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Client implements ClientInterface
{
    /**
     * Full address of the JSON-RPC service.
     *
     * @var string
     */
    protected $serverAddress;

    /**
     * HTTP Client to use for requests.
     *
     * @var \Zend\Http\Client
     */
    protected $httpClient;

    /**
     * Request of the last method call.
     *
     * @var \Zend\Json\Server\Request
     */
    protected $lastRequest;

    /**
     * Response received from the last method call.
     *
     * @var \Zend\Json\Server\Response
     */
    protected $lastResponse;

    /**
     * Request ID counter.
     *
     * @var int
     */
    protected $id = 0;

    /**
     * Create a new JSON-RPC client to a remote server.
     *
     * @param string $server Full address of the JSON-RPC service.
     * @param \Zend\Http\Client $httpClient HTTP Client to use for requests.
     */
    public function __construct($server, HttpClient $httpClient = null)
    {
        $this->httpClient = $httpClient ?: new HttpClient();
        $this->serverAddress = $server;
    }

    /**
     * Sets the HTTP client object to use for connecting the JSON-RPC server.
     *
     * @param \Zend\Http\Client $httpClient New HTTP client to use.
     * @return \Zend\Json\Server\Client Self instance.
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * Gets the HTTP client object.
     *
     * @return \Zend\Http\Client HTTP client.
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * The request of the last method call.
     *
     * @return \Zend\Json\Server\Request Request instance.
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * The response received from the last method call.
     *
     * @return \Zend\Json\Server\Response Response instance.
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Perform an JSOC-RPC request and return a response.
     *
     * @param \Zend\Json\Server\Request $request Request.
     * @return \Zend\Json\Server\Response Response.
     * @throws \Zend\Json\Server\Exception\HttpException When HTTP communication fails.
     */
    public function doRequest($request)
    {
        $this->lastRequest = $request;

        $httpRequest = $this->httpClient->getRequest();
        if ($httpRequest->getUri() === null) {
            $this->httpClient->setUri($this->serverAddress);
        }

        $headers = $httpRequest->headers();
        $headers->addHeaders(array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ));

        if (!$headers->get('User-Agent')) {
            $headers->addHeaderLine('User-Agent', 'Zend_Json_Server_Client');
        }

        $this->httpClient->setRawBody($request->__toString());
        $httpResponse = $this->httpClient->setMethod('POST')->send();

        if (!$httpResponse->isSuccess()) {
            throw new Exception\HttpException(
                $httpResponse->getReasonPhrase(),
                $httpResponse->getStatusCode()
            );
        }

        $response = new Response();

        $this->lastResponse = $response;

        // import all response data form JSON HTTP response
        $response->loadJson($httpResponse->getBody());

        return $response;
    }

    /**
     * Send an JSON-RPC request to the service (for a specific method).
     *
     * @param string $method Name of the method we want to call.
     * @param array $params Array of parameters for the method.
     * @return mixed Method call results.
     * @throws \Zend\Json\Server\Exception\ErrorExceptionn When remote call fails.
     */
    public function call($method, $params = array())
    {
        $request = $this->createRequest($method, $params);

        $response = $this->doRequest($request);

        if ($response->isError()) {
            $error = $response->getError();
            throw new Exception\ErrorException(
                $error->getMessage(),
                $error->getCode()
            );
        }

        return $response->getResult();
    }

    /**
     * Create request object.
     *
     * @param string $method Method to call.
     * @param array $params List of arguments.
     * @return \Zend\Json\Server\Request Created request.
     */
    protected function createRequest($method, array $params)
    {
        $request = new Request();
        $request->setMethod($method)
            ->setParams($params)
            ->setId(++$this->id);
        return $request;
    }
}
