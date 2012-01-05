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
use Zend\Controller;
use Zend\Controller\Request;

/**
 * @uses       \Zend\Controller\Exception
 * @uses       \Zend\Controller\Front
 * @uses       \Zend\Controller\Plugin\AbstractPlugin
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Broker extends AbstractPlugin
{

    /**
     * Array of instance of objects extending Zend_Controller_Plugin_Abstract
     *
     * @var array
     */
    protected $_plugins = array();


    /**
     * Register a plugin.
     *
     * @param  \Zend\Controller\Plugin\AbstractPlugin $plugin
     * @param  int $stackIndex
     * @return \Zend\Controller\Plugin\Broker
     */
    public function registerPlugin(AbstractPlugin $plugin, $stackIndex = null)
    {
        if (false !== array_search($plugin, $this->_plugins, true)) {
            throw new Controller\Exception('Plugin already registered');
        }

        $stackIndex = (int) $stackIndex;

        if ($stackIndex) {
            if (isset($this->_plugins[$stackIndex])) {
                throw new Controller\Exception('Plugin with stackIndex "' . $stackIndex . '" already registered');
            }
            $this->_plugins[$stackIndex] = $plugin;
        } else {
            $stackIndex = count($this->_plugins);
            while (isset($this->_plugins[$stackIndex])) {
                ++$stackIndex;
            }
            $this->_plugins[$stackIndex] = $plugin;
        }

        $request = $this->getRequest();
        if ($request) {
            $this->_plugins[$stackIndex]->setRequest($request);
        }
        $response = $this->getResponse();
        if ($response) {
            $this->_plugins[$stackIndex]->setResponse($response);
        }

        ksort($this->_plugins);

        return $this;
    }

    /**
     * Unregister a plugin.
     *
     * @param string|\Zend\Controller\Plugin\AbstractPlugin $plugin Plugin object or class name
     * @return \Zend\Controller\Plugin\Broker
     */
    public function unregisterPlugin($plugin)
    {
        if ($plugin instanceof AbstractPlugin) {
            // Given a plugin object, find it in the array
            $key = array_search($plugin, $this->_plugins, true);
            if (false === $key) {
                throw new Controller\Exception('Plugin never registered.');
            }
            unset($this->_plugins[$key]);
        } elseif (is_string($plugin)) {
            // Given a plugin class, find all plugins of that class and unset them
            foreach ($this->_plugins as $key => $_plugin) {
                $type = get_class($_plugin);
                if ($plugin == $type) {
                    unset($this->_plugins[$key]);
                }
            }
        }
        return $this;
    }

    /**
     * Is a plugin of a particular class registered?
     *
     * @param  string $class
     * @return bool
     */
    public function hasPlugin($class)
    {
        $class = ltrim($class, '\\');
        foreach ($this->_plugins as $plugin) {
            $type = get_class($plugin);
            if ($class == $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve a plugin or plugins by class
     *
     * @param  string $class Class name of plugin(s) desired
     * @return false|\Zend\Controller\Plugin\AbstractPlugin|array Returns false if none found, plugin if only one found, and array of plugins if multiple plugins of same class found
     */
    public function getPlugin($class)
    {
        $found = array();
        foreach ($this->_plugins as $plugin) {
            $type = get_class($plugin);
            if ($class == $type) {
                $found[] = $plugin;
            }
        }

        switch (count($found)) {
            case 0:
                return false;
            case 1:
                return $found[0];
            default:
                return $found;
        }
    }

    /**
     * Retrieve all plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->_plugins;
    }

    /**
     * Set request object, and register with each plugin
     *
     * @param \Zend\Controller\Request\AbstractRequest $request
     * @return \Zend\Controller\Plugin\Broker
     */
    public function setRequest(Request\AbstractRequest $request)
    {
        $this->_request = $request;

        foreach ($this->_plugins as $plugin) {
            $plugin->setRequest($request);
        }

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
     * @return \Zend\Controller\Plugin\Broker
     */
    public function setResponse(Controller\Response\AbstractResponse $response)
    {
        $this->_response = $response;

        foreach ($this->_plugins as $plugin) {
            $plugin->setResponse($response);
        }


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
    {
        $broker = $this->getHelperBroker();
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->setHelperBroker($broker);
                $plugin->routeStartup($request);
            } catch (\Exception $e) {
                if (Controller\Front::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called before Zend_Controller_Front exits its iterations over
     * the route set.
     *
     * @param  \Zend\Controller\Request\AbstractRequest $request
     * @return void
     */
    public function routeShutdown(Request\AbstractRequest $request)
    {
        $broker = $this->getHelperBroker();
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->setHelperBroker($broker);
                $plugin->routeShutdown($request);
            } catch (\Exception $e) {
                if (Controller\Front::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * During the dispatch loop, Zend_Controller_Front keeps a
     * Zend_Controller_Request_Abstract object, and uses
     * Zend_Controller_Dispatcher to dispatch the
     * Zend_Controller_Request_Abstract object to controllers/actions.
     *
     * @param  \Zend\Controller\Request\AbstractRequest $request
     * @return void
     */
    public function dispatchLoopStartup(Request\AbstractRequest $request)
    {
        $broker = $this->getHelperBroker();
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->setHelperBroker($broker);
                $plugin->dispatchLoopStartup($request);
            } catch (\Exception $e) {
                if (Controller\Front::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * @param  \Zend\Controller\Request\AbstractRequest $request
     * @return void
     */
    public function preDispatch(Request\AbstractRequest $request)
    {
        $broker = $this->getHelperBroker();
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->setHelperBroker($broker);
                $plugin->preDispatch($request);
            } catch (\Exception $e) {
                if (Controller\Front::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * @param  \Zend\Controller\Request\AbstractRequest $request
     * @return void
     */
    public function postDispatch(Request\AbstractRequest $request)
    {
        $broker = $this->getHelperBroker();
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->setHelperBroker($broker);
                $plugin->postDispatch($request);
            } catch (\Exception $e) {
                if (Controller\Front::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     * @param  \Zend\Controller\Request\AbstractRequest $request
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        $broker = $this->getHelperBroker();
       foreach ($this->_plugins as $plugin) {
           try {
                $plugin->setHelperBroker($broker);
                $plugin->dispatchLoopShutdown();
            } catch (\Exception $e) {
                if (Controller\Front::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
       }
    }
}
