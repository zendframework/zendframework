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
 * @package    Zend_Amf
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Amf;

use Zend\Amf\Exception,
    Zend\Authentication\AuthenticationService,
    Zend\Loader\Broker,
    Zend\Loader\PluginBroker,
    Zend\Server\Reflection;

/**
 * An AMF gateway server implementation to allow the connection of the Adobe Flash Player to
 * Zend Framework
 *
 * @todo       Make the relection methods cache and autoload.
 * @uses       Zend\Acl\Resource\GenericResource
 * @uses       Zend\Amf\Constants
 * @uses       Zend\Amf\Parser\TypeLoader
 * @uses       Zend\Amf\Request\HttpRequest
 * @uses       Zend\Amf\Response\HttpResponse
 * @uses       Zend\Amf\Value\MessageBody
 * @uses       Zend\Amf\Value\MessageHeader
 * @uses       Zend\Amf\Value\Messaging\AcknowledgeMessage
 * @uses       Zend\Amf\Value\Messaging\CommandMessage
 * @uses       Zend\Amf\Value\Messaging\ErrorMessage
 * @uses       Zend\Amf\Server\Exception
 * @uses       Zend\Authentication\AuthenticationService
 * @uses       Zend\Loader\PluginLoader
 * @uses       Zend\Server\Server
 * @uses       Zend\Server\Reflection
 * @uses       Zend\Session\Manager
 * @uses       Zend\Session\Container
 * @package    Zend_Amf
 * @subpackage Server
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Server implements \Zend\Server\Server
{
    /**
     * Array of dispatchables
     * @var array
     */
    protected $_methods = array();

    /**
     * Array of classes that can be called without being explicitly loaded
     *
     * Keys are class names.
     *
     * @var array
     */
    protected $_classAllowed = array();

    /**
     * Loader for classes in added directories
     * @var Zend\Loader\Broker
     */
    protected $_broker;

    /**
     * @var bool Production flag; whether or not to return exception messages
     */
    protected $_production = true;

    /**
     * Request processed
     * @var null|Zend\Amf\Request\StreamRequest
     */
    protected $_request = null;

    /**
     * Class to use for responses
     * @var null|Zend\Amf\Response\StreamResponse
     */
    protected $_response;

    /**
     * Dispatch table of name => method pairs
     * @var array
     */
    protected $_table = array();

    /**
     *
     * @var bool session flag; whether or not to add a session to each response.
     */
    protected $_session = false;

    /**
     * Namespace allows all AMF calls to not clobber other PHP session variables
     * @var Zend\Session\Container default session namespace zend_amf
     */
    protected $_sessionNamespace = 'zend_amf';

    /**
     * Set the default session.name if php_
     * @var string
     */
    protected $_sessionName = 'PHPSESSID';

    /**
     * Authentication handler object
     *
     * @var Zend\Amf\AbstractAuthentication
     */
    protected $_auth;

    /**
     * Authentication service object
     *
     * @var Zend\Authentication\AuthenticationService
     */
    protected $_authService;

    /**
     * ACL handler object
     *
     * @var Zend\Acl\Acl
     */
    protected $_acl;

    /**
     * The server constructor
     */
    public function __construct()
    {
        Parser\TypeLoader::setResourceBroker(new Parser\ParserBroker());
    }

    /**
     * Set authentication service
     *
     * @param  Zend\Authentication\AuthenticationService $auth
     * @return Zend\Amf\Server
     */
    public function setAuthService(AuthenticationService $auth)
    {
        $this->_authService = $auth;
        return $this;
    }

   /**
     * Get authentication service
     *
     * @return Zend\Authentication\AuthenticationService
     */
    public function getAuthService()
    {
        if (null === $this->_authService) {
            $this->setAuthService(new AuthenticationService());
        }
        return $this->_authService;
    }

    /**
     * Set authentication adapter
     *
     * @param  Zend\Amf\AbstractAuthentication $auth
     * @return Zend\Amf\Server
     */
    public function setAuth(AbstractAuthentication $auth)
    {
        $this->_auth = $auth;
        return $this;
    }

   /**
     * Get authentication adapter
     *
     * @return Zend\Amf\AbstractAuthentication
     */
    public function getAuth()
    {
        return $this->_auth;
    }

    /**
     * Set ACL adapter
     *
     * @param  Zend\Acl\Acl $acl
     * @return Zend\Amf\Server
     */
    public function setAcl(\Zend\Acl\Acl $acl)
    {
        $this->_acl = $acl;
        return $this;
    }
   /**
     * Get ACL adapter
     *
     * @return Zend\Acl\Acl
     */
    public function getAcl()
    {
        return $this->_acl;
    }

    /**
     * Set production flag
     *
     * @param  bool $flag
     * @return Zend\Amf\Server
     */
    public function setProduction($flag)
    {
        $this->_production = (bool) $flag;
        return $this;
    }

    /**
     * Whether or not the server is in production
     *
     * @return bool
     */
    public function isProduction()
    {
        return $this->_production;
    }

    /**
     * @param namespace of all incoming sessions defaults to Zend_Amf
     * @return Zend\Amf\Server
     */
    public function setSession($namespace = 'Zend_Amf')
    {
        $this->_session = true;
        $this->_sessionNamespace = new \Zend\Session\Container($namespace);
        return $this;
    }

    /**
     * Whether of not the server is using sessions
     * @return bool
     */
    public function isSession()
    {
        return $this->_session;
    }

    /**
     * Check if the ACL allows accessing the function or method
     *
     * @param string|object $object Object or class being accessed
     * @param string $function Function or method being accessed
     * @return unknown_type
     */
    protected function _checkAcl($object, $function)
    {
        if(!$this->_acl) {
            return true;
        }
        if($object) {
            $isObject = is_object($object);
            $class    = ($isObject) ? get_class($object) : $object;
            if(!$this->_acl->hasResource($class)) {
                $this->_acl->addResource(new \Zend\Acl\Resource\GenericResource($class));
            }
            if (method_exists($object, 'initAcl')) {
                // if initAcl returns false, no ACL check
                if ($isObject && $object->initAcl($this->_acl)) {
                    return true;
                } elseif ($class::initAcl($this->_acl)) {
                    return true;
                }
            }
        } else {
            $class = null;
        }

        $auth = $this->getAuthService();
        if($auth->hasIdentity()) {
            $role = $auth->getIdentity()->role;
        } else {
            if($this->_acl->hasRole(Constants::GUEST_ROLE)) {
                $role = Constants::GUEST_ROLE;
            } else {
                throw new Exception\RuntimeException("Unauthenticated access not allowed");
            }
        }
        if($this->_acl->isAllowed($role, $class, $function)) {
            return true;
        } else {
            throw new Exception\RuntimeException("Access not allowed");
        }
    }

    /**
     * Set broker instance
     * 
     * @param  string|Broker $broker 
     * @return Server
     */
    public function setBroker($broker)
    {
        if (is_string($broker)) {
            if (!class_exists($broker)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Invalid broker class ("") provided; could not resolve class',
                    $broker
                ));
            }
            $broker = new $broker;
        }
        if (!$broker instanceof Broker) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Broker must implement Zend\Loader\Broker; received ""',
                (is_object($broker) ? get_class($broker) : gettype($broker))
            ));
        }
        $this->_broker = $broker;
        return $this;
    }

    /**
     * Get PluginBroker for the Server
     *
     * @return Zend\Loader\PluginBroker
     */
    public function getBroker()
    {
        if (empty($this->_broker)) {
            $this->setBroker(new PluginBroker());
        }
        return $this->_broker;
    }

    /**
     * Loads a remote class or method and executes the function and returns
     * the result
     *
     * @param  string $method Is the method to execute
     * @param  mixed $param values for the method
     * @return mixed $response the result of executing the method
     * @throws Zend\Amf\Server\Exception
     */
    protected function _dispatch($method, $params = null, $source = null)
    {
        if($source) {
            if(($mapped = Parser\TypeLoader::getMappedClassName($source)) !== false) {
                $source = $mapped;
            }
        }
        $qualifiedName = empty($source) ? $method : $source.".".$method;

        if (!isset($this->_table[$qualifiedName])) {
            // if source is null a method that was not defined was called.
            if ($source) {
                $className = str_replace(".", "\\", $source);
                if (class_exists($className, false) && !isset($this->_classAllowed[$className])) {
                    throw new Exception\RuntimeException('Can not call "' . $className . '" - use setClass()');
                }
                try {
                    $this->getBroker()->getClassLoader()->load($className);
                } catch (\Exception $e) {
                    throw new Exception\RuntimeException('Class "' . $className . '" does not exist: '.$e->getMessage(), 0, $e);
                }
                // Add the new loaded class to the server.
                $this->setClass($className, $source);
            } else {
                throw new Exception\BadMethodCallException('Method "' . $method . '" does not exist');
            }
        }

        $info = $this->_table[$qualifiedName];
        $argv = $info->getInvokeArguments();

        if (0 < count($argv)) {
            $params = array_merge($params, $argv);
        }

        if ($info instanceof Reflection\ReflectionFunction) {
            $func = $info->getName();
            $this->_checkAcl(null, $func);
            $return = call_user_func_array($func, $params);
        } elseif ($info instanceof Reflection\ReflectionMethod) {
            // Get class
            $class = $info->getDeclaringClass()->getName();
            if ('static' == $info->isStatic()) {
                // for some reason, invokeArgs() does not work the same as
                // invoke(), and expects the first argument to be an object.
                // So, using a callback if the method is static.
                $this->_checkAcl($class, $info->getName());
                $return = call_user_func_array(array($class, $info->getName()), $params);
            } else {
                // Object methods
                try {
                    $object = $info->getDeclaringClass()->newInstance();
                } catch (\Exception $e) {
                    throw new Exception\RuntimeException('Error instantiating class ' . $class . ' to invoke method ' . $info->getName() . ': '.$e->getMessage(), 621, $e);
                }
                $this->_checkAcl($object, $info->getName());
                $return = $info->invokeArgs($object, $params);
            }
        } else {
            throw new Exception\BadMethodCallException('Method missing implementation ' . get_class($info));
        }

        return $return;
    }

    /**
     * Handles each of the 11 different command message types.
     *
     * A command message is a flex.messaging.messages.CommandMessage
     *
     * @see    Zend_Amf_Value_Messaging_CommandMessage
     * @param  Zend\Amf\Value\Messaging\CommandMessage $message
     * @return Zend\Amf\Value\Messaging\AcknowledgeMessage
     */
    protected function _loadCommandMessage(Value\Messaging\CommandMessage $message)
    {
        switch($message->operation) {
            case Value\Messaging\CommandMessage::DISCONNECT_OPERATION :
            case Value\Messaging\CommandMessage::CLIENT_PING_OPERATION :
                $return = new Value\Messaging\AcknowledgeMessage($message);
                break;
            case Value\Messaging\CommandMessage::LOGIN_OPERATION :
                $data = explode(':', base64_decode($message->body));
                $userid = $data[0];
                $password = isset($data[1])?$data[1]:"";
                if(empty($userid)) {
                    throw new Exception\RuntimeException('Login failed: username not supplied');
                }
                if(!$this->_handleAuth($userid, $password)) {
                    throw new Exception\RuntimeException('Authentication failed');
                }
                $return = new Value\Messaging\AcknowledgeMessage($message);
                break;
           case Value\Messaging\CommandMessage::LOGOUT_OPERATION :
                if ($this->_auth) {
                    $this->getAuthService()->clearIdentity();
                }
                $return = new Value\Messaging\AcknowledgeMessage($message);
                break;
            default :
                throw new Exception\RuntimeException('CommandMessage::' . $message->operation . ' not implemented');
                break;
        }
        return $return;
    }

    /**
     * Create appropriate error message
     *
     * @param int $objectEncoding Current AMF encoding
     * @param string $message Message that was being processed when error happened
     * @param string $description Error description
     * @param mixed $detail Detailed data about the error
     * @param int $code Error code
     * @param int $line Error line
     * @return Zend\Amf\Value\Messaging\ErrorMessage|array
     */
    protected function _errorMessage($objectEncoding, $message, $description, $detail, $code, $line)
    {
        $return = null;
        switch ($objectEncoding) {
            case Constants::AMF0_OBJECT_ENCODING :
                return array (
                        'description' => ($this->isProduction ()) ? '' : $description,
                        'detail' => ($this->isProduction ()) ? '' : $detail,
                        'line' => ($this->isProduction ()) ? 0 : $line,
                        'code' => $code
                );
            case Constants::AMF3_OBJECT_ENCODING :
                $return = new Value\Messaging\ErrorMessage ( $message );
                $return->faultString = $this->isProduction () ? '' : $description;
                $return->faultCode = $code;
                $return->faultDetail = $this->isProduction () ? '' : $detail;
                break;
        }
        return $return;
    }

    /**
     * Handle AMF authentication
     *
     * @param string $userid
     * @param string $password
     * @return boolean
     */
    protected function _handleAuth( $userid,  $password)
    {
        if (!$this->_auth) {
            return true;
        }
        $this->_auth->setCredentials($userid, $password);
        $auth   = $this->getAuthService();
        $result = $auth->authenticate($this->_auth);
        if ($result->isValid()) {
            if (!$this->isSession()) {
                $this->setSession();
            }
            return true;
        } else {
            // authentication failed, good bye
            throw new Exception\RuntimeException(
                "Authentication failed: " . implode("\n",
                    $result->getMessages()), $result->getCode());
        }

    }

    /**
     * Takes the deserialized AMF request and performs any operations.
     *
     * @todo   should implement and SPL observer pattern for custom AMF headers
     * @todo   DescribeService support
     * @param  Zend\Amf\Request\StreamRequest $request
     * @return Zend\Amf\Response\StreamResponse
     * @throws Zend\Amf\Server\Exception|Exception
     */
    protected function _handle(Request\StreamRequest $request)
    {
        // Get the object encoding of the request.
        $objectEncoding = $request->getObjectEncoding();

        // create a response object to place the output from the services.
        $response = $this->getResponse();

        // set response encoding
        $response->setObjectEncoding($objectEncoding);

        $responseBody = $request->getAmfBodies();

        $handleAuth = false;
        if ($this->_auth) {
            $headers = $request->getAmfHeaders();
            if (isset($headers[Constants::CREDENTIALS_HEADER]) &&
                isset($headers[Constants::CREDENTIALS_HEADER]->userid)) {
                $handleAuth = true;
            }
        }

        // Iterate through each of the service calls in the AMF request
        foreach($responseBody as $body)
        {
            try {
                if ($handleAuth) {
                    if ($this->_handleAuth(
                        $headers[Constants::CREDENTIALS_HEADER]->userid,
                        $headers[Constants::CREDENTIALS_HEADER]->password)) {
                        // use RequestPersistentHeader to clear credentials
                        $response->addAmfHeader(
                            new Value\MessageHeader(
                                Constants::PERSISTENT_HEADER,
                                false,
                                new Value\MessageHeader(
                                    Constants::CREDENTIALS_HEADER,
                                    false, null)));
                        $handleAuth = false;
                    }
                }

                if ($objectEncoding == Constants::AMF0_OBJECT_ENCODING) {
                    // AMF0 Object Encoding
                    $targetURI = $body->getTargetURI();
                    $message = '';

                    // Split the target string into its values.
                    $source = substr($targetURI, 0, strrpos($targetURI, '.'));

                    if ($source) {
                        // Break off method name from namespace into source
                        $method = substr(strrchr($targetURI, '.'), 1);
                        $return = $this->_dispatch($method, $body->getData(), $source);
                    } else {
                        // Just have a method name.
                        $return = $this->_dispatch($targetURI, $body->getData());
                    }
                } else {
                    // AMF3 read message type
                    $message = $body->getData();
                    if ($message instanceof Value\Messaging\CommandMessage) {
                        // async call with command message
                        $return = $this->_loadCommandMessage($message);
                    } elseif ($message instanceof Value\Messaging\RemotingMessage) {
                        $return = new Value\Messaging\AcknowledgeMessage($message);
                        $return->body = $this->_dispatch($message->operation, $message->body, $message->source);
                    } else {
                        // Amf3 message sent with netConnection
                        $targetURI = $body->getTargetURI();

                        // Split the target string into its values.
                        $source = substr($targetURI, 0, strrpos($targetURI, '.'));

                        if ($source) {
                            // Break off method name from namespace into source
                            $method = substr(strrchr($targetURI, '.'), 1);
                            $return = $this->_dispatch($method, $body->getData(), $source);
                        } else {
                            // Just have a method name.
                            $return = $this->_dispatch($targetURI, $body->getData());
                        }
                    }
                }
                $responseType = Constants::RESULT_METHOD;
            } catch (\Exception $e) {
                $return = $this->_errorMessage($objectEncoding, $message,
                    $e->getMessage(), $e->getTraceAsString(),$e->getCode(),  $e->getLine());
                $responseType = Constants::STATUS_METHOD;
            }

            $responseURI = $body->getResponseURI() . $responseType;
            $newBody     = new Value\MessageBody($responseURI, null, $return);
            $response->addAmfBody($newBody);
        }
        // Add a session header to the body if session is requested.
        if($this->isSession()) {
           $currentID = session_id();
           $joint = "?";
           if(isset($_SERVER['QUERY_STRING'])) {
               if(!strpos($_SERVER['QUERY_STRING'], $currentID) !== FALSE) {
                   if(strrpos($_SERVER['QUERY_STRING'], "?") !== FALSE) {
                       $joint = "&";
                   }
               }
           }

            // create a new AMF message header with the session id as a variable.
            $sessionValue = $joint . $this->_sessionName . "=" . $currentID;
            $sessionHeader = new Value\MessageHeader(Constants::URL_APPEND_HEADER, false, $sessionValue);
            $response->addAmfHeader($sessionHeader);
        }

        // serialize the response and return serialized body.
        $response->finalize();
    }

    /**
     * Handle an AMF call from the gateway.
     *
     * @param  null|Zend\Amf\Request\StreamRequest $request Optional
     * @return Zend\Amf\Response\StreamResponse
     */
    public function handle($request = null)
    {
        // Check if request was passed otherwise get it from the server
        if ($request === null || !$request instanceof Request\StreamRequest) {
            $request = $this->getRequest();
        } else {
            $this->setRequest($request);
        }
        if ($this->isSession()) {
             // Check if a session is being sent from the amf call
             if (isset($_COOKIE[$this->_sessionName])) {
                 session_id($_COOKIE[$this->_sessionName]);
             }
        }

        // Check for errors that may have happend in deserialization of Request.
        try {
            // Take converted PHP objects and handle service call.
            // Serialize to Zend_Amf_response for output stream
            $this->_handle($request);
            $response = $this->getResponse();
        } catch (\Exception $e) {
            // Handle any errors in the serialization and service  calls.
            throw new Exception\RuntimeException('Handle error: ' . $e->getMessage() . ' ' . $e->getLine(), 0, $e);
        }

        // Return the Amf serialized output string
        return $response;
    }

    /**
     * Set request object
     *
     * @param  string|Zend\Amf\Request\StreamRequest $request
     * @return Zend\Amf\Server
     */
    public function setRequest($request)
    {
        if (is_string($request) && class_exists($request)) {
            $request = new $request();
            if (!$request instanceof Request\StreamRequest) {
                throw new Exception\InvalidArgumentException('Invalid request class');
            }
        } elseif (is_string($request) && !class_exists($request)) {
            throw new Exception\InvalidArgumentException('Invalid request class');
        } elseif (!$request instanceof Request\StreamRequest) {
            throw new Exception\InvalidArgumentException('Invalid request object');
        }
        $this->_request = $request;
        return $this;
    }

    /**
     * Return currently registered request object
     *
     * @return null|Zend\Amf\Request\StreamRequest
     */
    public function getRequest()
    {
        if (null === $this->_request) {
            $this->setRequest(new Request\HttpRequest());
        }

        return $this->_request;
    }

    /**
     * Public access method to private Zend_Amf_Server_Response reference
     *
     * @param  string|Zend_Amf_Server_Response $response
     * @return Zend\Amf\Server
     */
    public function setResponse($response)
    {
        if (is_string($response) && class_exists($response)) {
            $response = new $response();
            if (!$response instanceof Response\StreamResponse) {
                throw new Exception\InvalidArgumentException('Invalid response class');
            }
        } elseif (is_string($response) && !class_exists($response)) {
            throw new Exception\InvalidArgumentException('Invalid response class');
        } elseif (!$response instanceof Response\StreamResponse) {
            throw new Exception\InvalidArgumentException('Invalid response object');
        }
        $this->_response = $response;
        return $this;
    }

    /**
     * get a reference to the Zend_Amf_response instance
     *
     * @return Zend_Amf_Server_Response
     */
    public function getResponse()
    {
        if (null === $this->_response) {
            $this->setResponse(new Response\HttpResponse());
        }
        return $this->_response;
    }

    /**
     * Attach a class or object to the server
     *
     * Class may be either a class name or an instantiated object. Reflection
     * is done on the class or object to determine the available public
     * methods, and each is attached to the server as and available method. If
     * a $namespace has been provided, that namespace is used to prefix
     * AMF service call.
     *
     * @param  string|object $class
     * @param  string $namespace Optional
     * @param  mixed $arg Optional arguments to pass to a method
     * @return Zend\Amf\Server
     * @throws Zend\Amf\Server\Exception on invalid input
     */
    public function setClass($class, $namespace = '', $argv = null)
    {
        if (is_string($class) && !class_exists($class)){
            throw new Exception\InvalidArgumentException('Invalid method or class');
        } elseif (!is_string($class) && !is_object($class)) {
            throw new Exception\InvalidArgumentException('Invalid method or class; must be a classname or object');
        }

        $argv = null;
        if (2 < func_num_args()) {
            $argv = array_slice(func_get_args(), 2);
        }

        // Use the class name as the name space by default.

        if ($namespace == '') {
            $namespace = is_object($class) ? get_class($class) : $class;
        }

        $this->_classAllowed[is_object($class) ? get_class($class) : $class] = true;

        $this->_methods[] = Reflection::reflectClass($class, $argv, $namespace);
        $this->_buildDispatchTable();

        return $this;
    }

    /**
     * Attach a function to the server
     *
     * Additional arguments to pass to the function at dispatch may be passed;
     * any arguments following the namespace will be aggregated and passed at
     * dispatch time.
     *
     * @param  string|array $function Valid callback
     * @param  string $namespace Optional namespace prefix
     * @return Zend\Amf\Server
     * @throws Zend\Amf\Server\Exception
     */
    public function addFunction($function, $namespace = '')
    {
        if (!is_string($function) && !is_array($function)) {
            throw new Exception\InvalidArgumentException('Unable to attach function');
        }

        $argv = null;
        if (2 < func_num_args()) {
            $argv = array_slice(func_get_args(), 2);
        }

        $function = (array) $function;
        foreach ($function as $func) {
            if (!is_string($func) || !function_exists($func)) {
                throw new Exception\InvalidArgumentException('Unable to attach function');
            }
            $this->_methods[] = Reflection::reflectFunction($func, $argv, $namespace);
        }

        $this->_buildDispatchTable();
        return $this;
    }

    /**
     * (Re)Build the dispatch table
     *
     * The dispatch table consists of a an array of method name =>
     * Zend_Server_Reflection_Function_Abstract pairs
     *
     * @return void
     */
    protected function _buildDispatchTable()
    {
        $table = array();
        foreach ($this->_methods as $key => $dispatchable) {
            if ($dispatchable instanceof Reflection\AbstractFunction) {
                $ns   = $dispatchable->getNamespace();
                $name = $dispatchable->getName();
                $name = empty($ns) ? $name : $ns . '.' . $name;

                if (isset($table[$name])) {
                    throw new Exception\InvalidArgumentException('Duplicate method registered: ' . $name);
                }
                $table[$name] = $dispatchable;
                continue;
            }

            if ($dispatchable instanceof Reflection\ReflectionClass) {
                foreach ($dispatchable->getMethods() as $method) {
                    $ns   = $method->getNamespace();
                    $name = $method->getName();
                    $name = empty($ns) ? $name : $ns . '.' . $name;

                    if (isset($table[$name])) {
                        throw new Exception\InvalidArgumentException('Duplicate method registered: ' . $name);
                    }
                    $table[$name] = $method;
                    continue;
                }
            }
        }
        $this->_table = $table;
    }



    /**
     * Raise a server fault
     *
     * Unimplemented
     *
     * @param  string|Exception $fault
     * @return void
     */
    public function fault($fault = null, $code = 404)
    {
    }

    /**
     * Returns a list of registered methods
     *
     * Returns an array of dispatchables (Zend_Server_Reflection_Function,
     * _Method, and _Class items).
     *
     * @return array
     */
    public function getFunctions()
    {
        return $this->_table;
    }

    /**
     * Set server persistence
     *
     * Unimplemented
     *
     * @param  mixed $mode
     * @return void
     */
    public function setPersistence($mode)
    {
    }

    /**
     * Load server definition
     *
     * Unimplemented
     *
     * @param  array $definition
     * @return void
     */
    public function loadFunctions($definition)
    {
    }

    /**
     * Map ActionScript classes to PHP classes
     *
     * @param  string $asClass
     * @param  string $phpClass
     * @return Zend\Amf\Server
     */
    public function setClassMap($asClass, $phpClass)
    {
        Parser\TypeLoader::setMapping($asClass, $phpClass);
        return $this;
    }

    /**
     * List all available methods
     *
     * Returns an array of method names.
     *
     * @return array
     */
    public function listMethods()
    {
        return array_keys($this->_table);
    }
}
