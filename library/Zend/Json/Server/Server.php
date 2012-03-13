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

use ReflectionFunction,
    ReflectionMethod,
    Zend\Server\AbstractServer,
    Zend\Server\Definition,
    Zend\Server\Method,
    Zend\Server\Reflection;

/**
 * @category   Zend
 * @package    Zend_Json
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Server extends AbstractServer
{
    /**#@+
     * Version Constants
     */
    const VERSION_1 = '1.0';
    const VERSION_2 = '2.0';
    /**#@-*/

    /**
     * Flag: whether or not to auto-emit the response
     * @var bool
     */
    protected $returnResponse = false;

    /**
     * @var bool Flag; allow overwriting existing methods when creating server definition
     */
    protected $_overwriteExistingMethods = true;

    /**
     * Request object
     * @var Request
     */
    protected $_request;

    /**
     * Response object
     * @var Response
     */
    protected $_response;

    /**
     * SMD object
     * @var Smd
     */
    protected $_serviceMap;

    /**
     * SMD class accessors
     * @var array
     */
    protected $_smdMethods;

    /**
     * @var \Zend\Server\Description
     */
    protected $_table;

    /**
     * Attach a function or callback to the server
     *
     * @param  string|array $function Valid PHP callback
     * @param  string $namespace  Ignored
     * @return Server
     */
    public function addFunction($function, $namespace = '')
    {
        if (!is_string($function) && (!is_array($function) || (2 > count($function)))) {
            throw new Exception\InvalidArgumentException('Unable to attach function; invalid');
        }

        if (!is_callable($function)) {
            throw new Exception\InvalidArgumentException('Unable to attach function; does not exist');
        }

        $argv = null;
        if (2 < func_num_args()) {
            $argv = func_get_args();
            $argv = array_slice($argv, 2);
        }

        if (is_string($function)) {
            $method = Reflection::reflectFunction($function, $argv, $namespace);
        } else {
            $class  = array_shift($function);
            $action = array_shift($function);
            $reflection = Reflection::reflectClass($class, $argv, $namespace);
            $methods = $reflection->getMethods();
            $found   = false;
            foreach ($methods as $method) {
                if ($action == $method->getName()) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $this->fault('Method not found', Error::ERROR_INVALID_METHOD);
                return $this;
            }
        }

        $definition = $this->_buildSignature($method);
        $this->_addMethodServiceMap($definition);

        return $this;
    }

    /**
     * Register a class with the server
     *
     * @param  string $class
     * @param  string $namespace Ignored
     * @param  mixed $argv Ignored
     * @return Server
     */
    public function setClass($class, $namespace = '', $argv = null)
    {
        if (2 < func_num_args()) {
            $argv = func_get_args();
            $argv = array_slice($argv, 2);
        }

        $reflection = Reflection::reflectClass($class, $argv, $namespace);

        foreach ($reflection->getMethods() as $method) {
            $definition = $this->_buildSignature($method, $class);
            $this->_addMethodServiceMap($definition);
        }
        return $this;
    }

    /**
     * Indicate fault response
     *
     * @param  string $fault
     * @param  int $code
     * @return false
     */
    public function fault($fault = null, $code = 404, $data = null)
    {
        $error = new Error($fault, $code, $data);
        $this->getResponse()->setError($error);
        return $error;
    }

    /**
     * Handle request
     *
     * @param  Request $request
     * @return null|Response
     */
    public function handle($request = false)
    {
        if ((false !== $request) && (!$request instanceof Request)) {
            throw new Exception\InvalidArgumentException('Invalid request type provided; cannot handle');
        } elseif ($request) {
            $this->setRequest($request);
        }

        // Handle request
        $this->_handle();

        // Get response
        $response = $this->_getReadyResponse();

        // Emit response?
        if (!$this->returnResponse) {
            echo $response;
            return;
        }

        // or return it?
        return $response;
    }

    /**
     * Load function definitions
     *
     * @param  array|Definition $definition
     * @return void
     */
    public function loadFunctions($definition)
    {
        if (!is_array($definition) && (!$definition instanceof Definition)) {
            throw new Exception\InvalidArgumentException('Invalid definition provided to loadFunctions()');
        }

        foreach ($definition as $key => $method) {
            $this->_table->addMethod($method, $key);
            $this->_addMethodServiceMap($method);
        }
    }

    public function setPersistence($mode)
    {
    }

    /**
     * Set request object
     *
     * @param  Request $request
     * @return Server
     */
    public function setRequest(Request $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Get JSON-RPC request object
     *
     * @return Request
     */
    public function getRequest()
    {
        if (null === ($request = $this->_request)) {
            $this->setRequest(new Request\Http());
        }
        return $this->_request;
    }

    /**
     * Set response object
     *
     * @param  Response $response
     * @return Server
     */
    public function setResponse(Response $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Get response object
     *
     * @return Response
     */
    public function getResponse()
    {
        if (null === ($response = $this->_response)) {
            $this->setResponse(new Response\Http());
        }
        return $this->_response;
    }

    /**
     * Set flag indicating whether or not to auto-emit response
     *
     * @param  bool $flag
     * @return Server
     * @deprecated Left just for BC, drop it as soon as not used by anyone - use setReturnResponse() with negation instead.
     */
    public function setAutoEmitResponse($flag)
    {
        return $this->setReturnResponse(!$flag);
    }

    /**
     * Will we auto-emit the response?
     *
     * @return bool
     * @deprecated Left just for BC, drop it as soon as not used by anyone - use getReturnResponse() with negation instead.
     */
    public function autoEmitResponse()
    {
        return !$this->getReturnResponse();
    }

    /**
     * Set return response flag
     *
     * If true, {@link handle()} will return the response instead of
     * automatically sending it back to the requesting client.
     *
     * The response is always available via {@link getResponse()}.
     *
     * @param boolean $flag
     * @return Server
     */
    public function setReturnResponse($flag = true)
    {
        $this->returnResponse = ($flag) ? true : false;
        return $this;
    }

    /**
     * Retrieve return response flag
     *
     * @return boolean
     */
    public function getReturnResponse()
    {
        return $this->returnResponse;
    }

    // overloading for SMD metadata
    /**
     * Overload to accessors of SMD object
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (preg_match('/^(set|get)/', $method, $matches)) {
            if (in_array($method, $this->_getSmdMethods())) {
                if ('set' == $matches[1]) {
                    $value = array_shift($args);
                    $this->getServiceMap()->$method($value);
                    return $this;
                } else {
                    return $this->getServiceMap()->$method();
                }
            }
        }
        return null;
    }

    /**
     * Retrieve SMD object
     *
     * @return Smd
     */
    public function getServiceMap()
    {
        if (null === $this->_serviceMap) {
            $this->_serviceMap = new Smd();
        }
        return $this->_serviceMap;
    }

    /**
     * Add service method to service map
     *
     * @param  Method\Definition $method
     * @return void
     */
    protected function _addMethodServiceMap(Method\Definition $method)
    {
        $serviceInfo = array(
            'name'   => $method->getName(),
            'return' => $this->_getReturnType($method),
        );
        $params = $this->_getParams($method);
        $serviceInfo['params'] = $params;
        $serviceMap = $this->getServiceMap();
        if (false !== $serviceMap->getService($serviceInfo['name'])) {
            $serviceMap->removeService($serviceInfo['name']);
        }
        $serviceMap->addService($serviceInfo);
    }

    /**
     * Translate PHP type to JSON type
     *
     * @param  string $type
     * @return string
     */
    protected function _fixType($type)
    {
        return $type;
    }

    /**
     * Get default params from signature
     *
     * @param  array $args
     * @param  array $params
     * @return array
     */
    protected function _getDefaultParams(array $args, array $params)
    {
        $defaultParams = array_slice($params, count($args));
        foreach ($defaultParams as $param) {
            $value = null;
            if (array_key_exists('default', $param)) {
                $value = $param['default'];
            }
            array_push($args, $value);
        }
        return $args;
    }

    /**
     * Get method param type
     *
     * @param  Method\Definition $method
     * @return string|array
     */
    protected function _getParams(Method\Definition $method)
    {
        $params = array();
        foreach ($method->getPrototypes() as $prototype) {
            foreach ($prototype->getParameterObjects() as $key => $parameter) {
                if (!isset($params[$key])) {
                    $params[$key] = array(
                        'type'     => $parameter->getType(),
                        'name'     => $parameter->getName(),
                        'optional' => $parameter->isOptional(),
                    );
                    if (null !== ($default = $parameter->getDefaultValue())) {
                        $params[$key]['default'] = $default;
                    }
                    $description = $parameter->getDescription();
                    if (!empty($description)) {
                        $params[$key]['description'] = $description;
                    }
                    continue;
                }
                $newType = $parameter->getType();
                if (!is_array($params[$key]['type'])) {
                    if ($params[$key]['type'] == $newType) {
                        continue;
                    }
                    $params[$key]['type'] = (array) $params[$key]['type'];
                } elseif (in_array($newType, $params[$key]['type'])) {
                    continue;
                }
                array_push($params[$key]['type'], $parameter->getType());
            }
        }
        return $params;
    }

    /**
     * Set response state
     *
     * @return Response
     */
    protected function _getReadyResponse()
    {
        $request  = $this->getRequest();
        $response = $this->getResponse();

        $response->setServiceMap($this->getServiceMap());
        if (null !== ($id = $request->getId())) {
            $response->setId($id);
        }
        if (null !== ($version = $request->getVersion())) {
            $response->setVersion($version);
        }

        return $response;
    }

    /**
     * Get method return type
     *
     * @param  Method\Definition $method
     * @return string|array
     */
    protected function _getReturnType(Method\Definition $method)
    {
        $return = array();
        foreach ($method->getPrototypes() as $prototype) {
            $return[] = $prototype->getReturnType();
        }
        if (1 == count($return)) {
            return $return[0];
        }
        return $return;
    }

    /**
     * Retrieve list of allowed SMD methods for proxying
     *
     * @return array
     */
    protected function _getSmdMethods()
    {
        if (null === $this->_smdMethods) {
            $this->_smdMethods = array();
            $methods = get_class_methods('Zend\\Json\\Server\\Smd');
            foreach ($methods as $key => $method) {
                if (!preg_match('/^(set|get)/', $method)) {
                    continue;
                }
                if (strstr($method, 'Service')) {
                    continue;
                }
                $this->_smdMethods[] = $method;
            }
        }
        return $this->_smdMethods;
    }

    /**
     * Internal method for handling request
     *
     * @return void
     */
    protected function _handle()
    {
        $request = $this->getRequest();

        if (!$request->isMethodError() && (null === $request->getMethod())) {
            return $this->fault('Invalid Request', Error::ERROR_INVALID_REQUEST);
        }

        if ($request->isMethodError()) {
            return $this->fault('Invalid Request', Error::ERROR_INVALID_REQUEST);
        }

        $method = $request->getMethod();
        if (!$this->_table->hasMethod($method)) {
            return $this->fault('Method not found', Error::ERROR_INVALID_METHOD);
        }

        $params        = $request->getParams();
        $invocable     = $this->_table->getMethod($method);
        $serviceMap    = $this->getServiceMap();
        $service       = $serviceMap->getService($method);
        $serviceParams = $service->getParams();

        if (count($params) < count($serviceParams)) {
            $params = $this->_getDefaultParams($params, $serviceParams);
        }

        //Make sure named parameters are passed in correct order
        if ( is_string( key( $params ) ) ) {

            $callback = $invocable->getCallback();
            if ('function' == $callback->getType()) {
                $reflection = new ReflectionFunction( $callback->getFunction() );
            } else {
                
                $reflection = new ReflectionMethod( 
                    $callback->getClass(),
                    $callback->getMethod()
                );
            }

            $orderedParams = array();
            foreach( $reflection->getParameters() as $refParam ) {
                if( isset( $params[ $refParam->getName() ] ) ) {
                    $orderedParams[ $refParam->getName() ] = $params[ $refParam->getName() ];
                } elseif( $refParam->isOptional() ) {
                    $orderedParams[ $refParam->getName() ] = null;
                } else {
                    return $this->fault('Invalid params', Error::ERROR_INVALID_PARAMS);
                }
            }
            $params = $orderedParams;
        }

        try {
            $result = $this->_dispatch($invocable, $params);
        } catch (\Exception $e) {
            return $this->fault($e->getMessage(), $e->getCode(), $e);
        }

        $this->getResponse()->setResult($result);
    }
}
