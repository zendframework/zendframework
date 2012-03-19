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
 * @package    Zend_Service
 * @subpackage Nirvanix
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\Nirvanix\Context;

use Traversable,
    Zend\Http\Client as HttpClient,
    Zend\Http\Request as HttpRequest,
    Zend\Http\Response as HttpResponse,
    Zend\Service\Nirvanix\Exception,
    Zend\Service\Nirvanix\Response,
    Zend\Stdlib\ArrayUtils;

/**
 * The Nirvanix web services are split into namespaces.  This is a proxy class
 * representing one namespace.  It allows calls to the namespace to be made by
 * PHP object calls rather than by having to construct HTTP client requests.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Nirvanix
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Base
{
    /**
     * HTTP client instance that will be used to make calls to
     * the Nirvanix web services.
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * Host to use for calls to this Nirvanix namespace.  It is possible
     * that the user will wish to use different hosts for different namespaces.
     * @var string
     */
    protected $host = 'http://services.nirvanix.com';

    /**
     * Name of this namespace as used in the URL.
     * @var string
     */
    protected $namespace = '';

    /**
     * Defaults for POST parameters.  When a request to the service is to be
     * made, the POST parameters are merged into these.  This is a convenience
     * feature so parameters that are repeatedly required like sessionToken
     * do not need to be supplied again and again by the user.
     *
     * @param array
     */
    protected $defaults = array();

    /**
     * Class constructor.
     *
     * @param  $options  array  Options and dependency injection
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable of options');
        }
        
        if (isset($options['baseUrl'])) {
            $this->host = $options['baseUrl'];
        }

        if (isset($options['namespace'])) {
            $this->namespace = $options['namespace'];
        }

        if (isset($options['defaults'])) {
            $this->defaults = $options['defaults'];
        }

        if (! isset($options['httpClient'])) {
            $options['httpClient'] = new HttpClient();
        }
        $this->httpClient = $options['httpClient'];
    }

    /**
     * When a method call is made against this proxy, convert it to
     * an HTTP request to make against the Nirvanix REST service.
     *
     * $imfs->DeleteFiles(array('filePath' => 'foo'));
     *
     * Assuming this object was proxying the IMFS namespace, the
     * method call above would call the DeleteFiles command.  The
     * POST parameters would be filePath, merged with the
     * $this->defaults (containing the sessionToken).
     *
     * @param  string  $methodName  Name of the command to call
     *                              on this namespace.
     * @param  array   $args        Only the first is used and it must be
     *                              an array.  It contains the POST params.
     *
     * @return Response
     */
    public function __call($methodName, $args)
    {
        $client = $this->httpClient;

        $uri    = $this->makeUri($methodName);
        $client->setUri($uri);

        if (!isset($args[0]) || !is_array($args[0])) {
            $args[0] = array();
        }

        $params = array_merge($this->defaults, $args[0]);
        $client->resetParameters();
        $client->setParameterPost($params);
        $client->setMethod(HttpRequest::METHOD_POST);

        $httpResponse = $client->send();
        return $this->wrapResponse($httpResponse);
    }

    /**
     * Return the HTTP client used for this namespace.  This is useful
     * for inspecting the last request or directly interacting with the
     * HTTP client.
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Make a complete URI from an RPC method name.  All Nirvanix REST
     * service URIs use the same format.
     *
     * @param  string  $methodName  RPC method name
     * @return string
     */
    protected function makeUri($methodName)
    {
        $methodName = ucfirst($methodName);
        return "{$this->host}/ws/{$this->namespace}/{$methodName}.ashx";
    }

    /**
     * All Nirvanix REST service calls return an XML payload.  This method
     * makes a Zend_Service_Nirvanix_Response from that XML payload.
     *
     * @param  HttpResponse  $httpResponse  Raw response from Nirvanix
     * @return Response     Wrapped response
     */
    protected function wrapResponse(HttpResponse $httpResponse)
    {
        return new Response($httpResponse->getBody());
    }
}
