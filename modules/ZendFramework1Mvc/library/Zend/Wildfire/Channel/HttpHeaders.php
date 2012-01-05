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
 * @package    Zend_Wildfire
 * @subpackage Channel
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Wildfire\Channel;
use Zend\Wildfire,
    Zend\Wildfire\Protocol,
    Zend\Controller,
    Zend\Controller\Request\Http as HttpRequest;

/**
 * Implements communication via HTTP request and response headers for Wildfire Protocols.
 *
 * @uses       \Zend\Controller\Front
 * @uses       \Zend\Controller\Plugin\AbstractPlugin
 * @uses       \Zend\Controller\Request\AbstractRequest
 * @uses       \Zend\Controller\Response\AbstractResponse
 * @uses       \Zend\Loader
 * @uses       \Zend\Wildfire\Channel
 * @uses       \Zend\Wildfire\Exception
 * @uses       \Zend\Wildfire\Protocol\JsonStream
 * @category   Zend
 * @package    Zend_Wildfire
 * @subpackage Channel
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HttpHeaders
    extends Controller\Plugin\AbstractPlugin
    implements Wildfire\Channel
{
    /**
     * The string to be used to prefix the headers.
     * @var string
     */
    protected static $_headerPrefix = 'X-WF-';

    /**
     * Singleton instance
     * @var \Zend\Wildfire\Channel\HttpHeaders
     */
    protected static $_instance = null;

    /**
     * The index of the plugin in the controller dispatch loop plugin stack
     * @var integer
     */
    protected static $_controllerPluginStackIndex = 999;

    /**
     * The protocol instances for this channel
     * @var array
     */
    protected $_protocols = null;

    /**
     * Initialize singleton instance.
     *
     * @param string $class OPTIONAL Subclass of \Zend\Wildfire\Channel\HttpHeaders
     * @return \Zend\Wildfire\Channel\HttpHeaders Returns the singleton \Zend\Wildfire\Channel\HttpHeaders instance
     * @throws \Zend\Wildfire\Exception
     */
    public static function init($class = null)
    {
        if (self::$_instance !== null) {
            throw new Exception\RuntimeException('Singleton instance of Zend_Wildfire_Channel_HttpHeaders already exists!');
        }
        if ($class !== null) {
            if (!is_string($class)) {
                throw new Exception\InvalidArgumentException('Third argument is not a class string');
            }

            self::$_instance = new $class();

            if (!self::$_instance instanceof HttpHeaders) {
                self::$_instance = null;
                throw new Exception\InvalidArgumentException('Invalid class to third argument. Must be subclass of Zend_Wildfire_Channel_HttpHeaders.');
            }
        } else {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    /**
     * Get or create singleton instance
     *
     * @param $skipCreate boolean True if an instance should not be created
     * @return \Zend\Wildfire\Channel\HttpHeaders
     */
    public static function getInstance($skipCreate=false)
    {
        if (self::$_instance===null && $skipCreate!==true) {
            return self::init();
        }
        return self::$_instance;
    }

    /**
     * Destroys the singleton instance
     *
     * Primarily used for testing.
     *
     * @return void
     */
    public static function destroyInstance()
    {
        self::$_instance = null;
    }

    /**
     * Get the instance of a give protocol for this channel
     *
     * @param string $uri The URI for the protocol
     * @return object Returns the protocol instance for the diven URI
     */
    public function getProtocol($uri)
    {
        if (!isset($this->_protocols[$uri])) {
            $this->_protocols[$uri] = $this->_initProtocol($uri);
        }

        $this->_registerControllerPlugin();

        return $this->_protocols[$uri];
    }

    /**
     * Initialize a new protocol
     *
     * @param string $uri The URI for the protocol to be initialized
     * @return object Returns the new initialized protocol instance
     * @throws \Zend\Wildfire\Exception
     */
    protected function _initProtocol($uri)
    {
        switch ($uri) {
            case Protocol\JsonStream::PROTOCOL_URI;
                return new Protocol\JsonStream();
        }
        throw new Channel\InvalidArgumentException('Tyring to initialize unknown protocol for URI "'.$uri.'".');
    }


    /**
     * Flush all data from all protocols and send all data to response headers.
     *
     * @return boolean Returns TRUE if data was flushed
     */
    public function flush()
    {
        if (!$this->_protocols || !$this->isReady()) {
            return false;
        }

        foreach ( $this->_protocols as $protocol ) {

            $payload = $protocol->getPayload($this);

            if ($payload) {
                foreach( $payload as $message ) {

                    $this->getResponse()->setHeader(self::$_headerPrefix.$message[0],
                                                    $message[1], true);
                }
            }
        }
        return true;
    }

    /**
     * Set the index of the plugin in the controller dispatch loop plugin stack
     *
     * @param integer $index The index of the plugin in the stack
     * @return integer The previous index.
     */
    public static function setControllerPluginStackIndex($index)
    {
        $previous = self::$_controllerPluginStackIndex;
        self::$_controllerPluginStackIndex = $index;
        return $previous;
    }

    /**
     * Register this object as a controller plugin.
     *
     * @return void
     */
    protected function _registerControllerPlugin()
    {
        $controller = Controller\Front::getInstance();
        if (!$controller->hasPlugin(get_class($this))) {
            $controller->registerPlugin($this, self::$_controllerPluginStackIndex);
        }
    }


    /*
     * Zend_Wildfire_Channel_Interface
     */

    /**
     * Determine if channel is ready.
     *
     * The channel is ready as long as the request and response objects are initialized,
     * can send headers and the FirePHP header exists in the User-Agent.
     *
     * If the header does not exist in the User-Agent, no appropriate client
     * is making this request and the messages should not be sent.
     *
     * A timing issue arises when messages are logged before the request/response
     * objects are initialized. In this case we do not yet know if the client
     * will be able to accept the messages. If we consequently indicate that
     * the channel is not ready, these messages will be dropped which is in
     * most cases not the intended behaviour. The intent is to send them at the
     * end of the request when the request/response objects will be available
     * for sure.
     *
     * If the request/response objects are not yet initialized we assume if messages are
     * logged, the client will be able to receive them. As soon as the request/response
     * objects are availoable and a message is logged this assumption is challenged.
     * If the client cannot accept the messages any further messages are dropped
     * and messages sent prior are kept but discarded when the channel is finally
     * flushed at the end of the request.
     *
     * When the channel is flushed the $forceCheckRequest option is used to force
     * a check of the request/response objects. This is the last verification to ensure
     * messages are only sent when the client can accept them.
     *
     * @param boolean $forceCheckRequest OPTIONAL Set to TRUE if the request must be checked
     * @return boolean Returns TRUE if channel is ready.
     */
    public function isReady($forceCheckRequest=false)
    {
        if (!$forceCheckRequest
            && !$this->_request
            && !$this->_response
        ) {
            return true;
        }

        if (!($this->getRequest() instanceof HttpRequest)) {
            return false;
        }

        return ($this->getResponse()->canSendHeaders()
                && (preg_match_all(
                        '/\s?FirePHP\/([\.\d]*)\s?/si',
                        $this->getRequest()->getHeader('User-Agent'),
                        $m
                    ) ||
                    (($header = $this->getRequest()->getHeader('X-FirePHP-Version'))
                     && preg_match_all('/^([\.\d]*)$/si', $header, $m)
                   ))
               );
    }


    /*
     * Zend_Controller_Plugin_Abstract
     */

    /**
     * Flush messages to headers as late as possible but before headers have been sent.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        $this->flush();
    }

    /**
     * Get the request object
     *
     * @return \Zend\Controller\Request\AbstractRequest
     * @throws \Zend\Wildfire\Exception
     */
    public function getRequest()
    {
        if (!$this->_request) {
            $controller = Controller\Front::getInstance();
            $this->setRequest($controller->getRequest());
        }
        if (!$this->_request) {
            throw new Channel\RuntimeException('Request objects not initialized.');
        }
        return $this->_request;
    }

    /**
     * Get the response object
     *
     * @return \Zend\Controller\Response\AbstractResponse
     * @throws \Zend\Wildfire\Exception
     */
    public function getResponse()
    {
        if (!$this->_response) {
            $response = Controller\Front::getInstance()->getResponse();
            if ($response) {
                $this->setResponse($response);
            }
        }
        if (!$this->_response) {
            throw new Channel\RuntimeException('Response objects not initialized.');
        }
        return $this->_response;
    }
}
