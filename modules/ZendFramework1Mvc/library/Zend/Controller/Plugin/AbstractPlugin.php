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
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Plugin;

use Zend\Controller\Request,
    Zend\Loader\Broker;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractPlugin
{
    /**
     * Helper Broker instance
     * 
     * @var \Zend\Loader\Broker
     */
    protected $broker;

    /**
     * @var \Zend\Controller\Request\AbstractRequest
     */
    protected $_request;

    /**
     * @var \Zend\Controller\Response\AbstractResponse
     */
    protected $_response;

    /**
     * Set the action helper broker instance
     * 
     * @param  null|Broker $broker 
     * @return AbstractPlugin
     */
    public function setHelperBroker(Broker $broker = null)
    {
        $this->broker = $broker;
        return $this;
    }

    /**
     * Get the action helper broker instance
     * 
     * @return null|Broker
     */
    public function getHelperBroker()
    {
        return $this->broker;
    }

    /**
     * Set request object
     *
     * @param \Zend\Controller\Request\AbstractRequest $request
     * @return \Zend\Controller\Plugin\AbstractPlugin
     */
    public function setRequest(Request\AbstractRequest $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Get request object
     *
     * @return \Zend\Controller\Request\AbstractRequest $request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set response object
     *
     * @param \Zend\Controller\Response\AbstractResponse $response
     * @return \Zend\Controller\Plugin\AbstractPlugin
     */
    public function setResponse(\Zend\Controller\Response\AbstractResponse $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Get response object
     *
     * @return \Zend\Controller\Response\AbstractResponse $response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @param \Zend\Controller\Request\AbstractRequest $request
     * @return void
     */
    public function routeStartup(Request\AbstractRequest $request)
    {}

    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  \Zend\Controller\Request\AbstractRequest $request
     * @return void
     */
    public function routeShutdown(Request\AbstractRequest $request)
    {}

    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  \Zend\Controller\Request\AbstractRequest $request
     * @return void
     */
    public function dispatchLoopStartup(Request\AbstractRequest $request)
    {}

    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * @param  \Zend\Controller\Request\AbstractRequest $request
     * @return void
     */
    public function preDispatch(Request\AbstractRequest $request)
    {}

    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior. By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * a new action may be specified for dispatching.
     *
     * @param  \Zend\Controller\Request\AbstractRequest $request
     * @return void
     */
    public function postDispatch(Request\AbstractRequest $request)
    {}

    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {}
}
