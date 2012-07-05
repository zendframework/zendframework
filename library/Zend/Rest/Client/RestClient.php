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
 * @package    Zend_Rest
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Rest\Client;

use Zend\Http\Client as HttpClient,
    Zend\Uri;

/**
 * @category   Zend
 * @package    Zend_Rest
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RestClient extends \Zend\Service\AbstractService
{
    /**
     * Data for the query
     * @var array
     */
    protected $data = array();

    /**
     * URI of this web service
     * @var Uri\Uri
     */
    protected $uri = null;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * Constructor
     *
     * @param string|Uri\Uri $uri URI for the web service
     * @return void
     */
    public function __construct($uri = null)
    {
        if (!empty($uri)) {
            $this->setUri($uri);
        }
    }

    /**
     * Set HTTP client instance to use with this service instance
     *
     * @param  HttpClient $client
     * @return RestClient
     */
    public function setHttpClient(HttpClient $client)
    {
        $this->httpClient = $client;
        return $this;
    }

    /**
     * Get the HTTP client instance registered with this service instance
     *
     * If none set, will check for a default instance.
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        if (null === $this->httpClient) {
            $this->setHttpClient(new HttpClient());
        }
        return $this->httpClient;
    }

    /**
     * Set the URI to use in the request
     *
     * @param  string|Uri\Uri $uri URI for the web service
     * @return RestClient
     */
    public function setUri($uri)
    {
        if ($uri instanceof Uri\Uri) {
            $this->uri = $uri;
        } else {
            $this->uri = Uri\UriFactory::factory($uri);
        }

        return $this;
    }

    /**
     * Retrieve the current request URI object
     *
     * @return Uri\Uri
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Call a remote REST web service URI and return the Zend_Http_Response object
     *
     * @param  string $path            The path to append to the URI
     * @throws Exception\UnexpectedValueException
     * @return void
     */
    protected function prepareRest($path)
    {
        // Get the URI object and configure it
        if (!$this->uri instanceof Uri\Uri) {
            throw new Exception\UnexpectedValueException('URI object must be set before performing call');
        }

        $uri = $this->uri->toString();

        if ($path[0] != '/' && $uri[strlen($uri)-1] != '/') {
            $path = '/' . $path;
        }

        $this->uri->setPath($path);

        /**
         * Get the HTTP client and configure it for the endpoint URI.  Do this
         * each time as the Zend\Http\Client instance may be shared with other
         * Zend\Service\AbstractService subclasses.
         */
        $client = $this->getHttpClient();
        $client->resetParameters();
        $client->setUri($this->uri);
    }

    /**
     * Performs an HTTP GET request to the $path.
     *
     * @param string $path
     * @param array  $query Array of GET parameters
     * @return Zend\Http\Response
     */
    public function restGet($path, array $query = null)
    {
        $this->prepareRest($path);
        $client = $this->getHttpClient();
        if (is_array($query)) {
            $client->setParameterGet($query);
        }
        return $client->setMethod('GET')->send();
    }

    /**
     * Perform a POST or PUT
     *
     * Performs a POST or PUT request. Any data provided is set in the HTTP
     * client. String data is pushed in as raw POST data; array or object data
     * is pushed in as POST parameters.
     *
     * @param mixed $method
     * @param mixed $data
     * @return \Zend\Http\Response
     */
    protected function performPost($method, $data = null)
    {
        $client = $this->getHttpClient();
        $client->setMethod($method);

        $request = $client->getRequest();
        if (is_string($data)) {
            $request->setContent($data);
        } elseif (is_array($data) || is_object($data)) {
            $request->getPost()->fromArray((array) $data);
        }
        return $client->send($request);
    }

    /**
     * Performs an HTTP POST request to $path.
     *
     * @param string $path
     * @param mixed $data Raw data to send
     * @return \Zend\Http\Response
     */
    public function restPost($path, $data = null)
    {
        $this->prepareRest($path);
        return $this->performPost('POST', $data);
    }

    /**
     * Performs an HTTP PUT request to $path.
     *
     * @param string $path
     * @param mixed $data Raw data to send in request
     * @return \Zend\Http\Response
     */
    public function restPut($path, $data = null)
    {
        $this->prepareRest($path);
        return $this->performPost('PUT', $data);
    }

    /**
     * Performs an HTTP DELETE request to $path.
     *
     * @param string $path
     * @return \Zend\Http\Response
     */
    public function restDelete($path)
    {
        $this->prepareRest($path);
        return $this->getHttpClient()->setMethod('DELETE')->send();
    }

    /**
     * Method call overload
     *
     * Allows calling REST actions as object methods; however, you must
     * follow-up by chaining the request with a request to an HTTP request
     * method (post, get, delete, put):
     * <code>
     * $response = $rest->sayHello('Foo', 'Manchu')->get();
     * </code>
     *
     * Or use them together, but in sequential calls:
     * <code>
     * $rest->sayHello('Foo', 'Manchu');
     * $response = $rest->get();
     * </code>
     *
     * @param string $method Method name
     * @param array $args Method args
     * @return \Zend\Rest\Client\RestClient_Result|\Zend\Rest\Client\RestClient \Zend\Rest\Client\RestClient if using
     * a remote method, Zend_Rest_Client_Result if using an HTTP request method
     */
    public function __call($method, $args)
    {
        $methods = array('post', 'get', 'delete', 'put');

        if (in_array(strtolower($method), $methods)) {
            if (!isset($args[0])) {
                $args[0] = $this->uri->getPath();
            }
            $this->data['rest'] = 1;
            $data               = array_slice($args, 1) + $this->data;
            $response           = $this->{'rest' . $method}($args[0], $data);
            $this->data         = array(); //Initializes for next Rest method.
            return new Result($response->getBody());
        } else {
            // More than one arg means it's definitely a Zend_Rest_Server
            if (count($args) == 1) {
                // Uses first called function name as method name
                if (!isset($this->data['method'])) {
                    $this->data['method'] = $method;
                    $this->data['arg1']   = $args[0];
                }
                $this->data[$method]  = $args[0];
            } else {
                $this->data['method'] = $method;
                if (count($args) > 0) {
                    foreach ($args as $key => $arg) {
                        $key = 'arg' . $key;
                        $this->data[$key] = $arg;
                    }
                }
            }
            return $this;
        }
    }
}
